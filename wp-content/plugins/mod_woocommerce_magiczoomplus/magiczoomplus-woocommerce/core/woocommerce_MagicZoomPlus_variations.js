'use strict';
var prodVariations;
var clicked = false;
var ready = false;
var lastChangedImage = null;
window.current_img = false;
var onZoomReady = false;
mzOptions["onZoomReady"] = function() { onZoomReady = true; change(); }

window['MagicRefresh'] = function() {
    //if (!onZoomReady) return;
    
    var aHref = $('.invisImg').attr("href");
    if (typeof($('.invisImg').attr("src")) != "undefined" && $('.invisImg').attr("src").length > 0) {
        var imgSrc = $('.invisImg').attr("src");
    } else {
        var imgSrc = $('.invisImg img').attr("src");
    }
    MagicZoom.stop();
    
    jQuery("#MagicZoomPlusImage_Main img").removeAttr("srcset");
    jQuery("#MagicZoomPlusImage_Main").removeAttr("data-image-2x");

    jQuery("#MagicZoomPlusImage_Main > img").attr("src", imgSrc);
    jQuery("#MagicZoomPlusImage_Main").attr("data-image", imgSrc);
    jQuery("#MagicZoomPlusImage_Main").attr("href",aHref);

    // Force browser to update currentSrc
    jQuery("#MagicZoomPlusImage_Main > img")[0].currentSrc;

    setTimeout(function () {
        MagicZoom.start();
        if (typeof(MagicScroll) !== "undefined") {
            MagicScroll.start();
        }
    }, 0);
    
}


function getCurrentVariationByImage (img) {
    for(var i in prodVariations) {
        if (prodVariations[i]["image_link"] == img) {
            return prodVariations[i];
        }
    }
}

function activateVariationAttributes (variation, link) {
    attrs = Object.keys(variation.attributes);
    result = new Array();

    clicked = true;
    $('.reset_variations').trigger('click'); //prevent dropdowns modifications
    clicked = false;

    $(attrs).each(function (attribute) {
        value = variation.attributes[attrs[attribute]]+"";
        if (value != "") {
            $("form.variations_form [name$=\'" + attrs[attribute] + "\']").val(value).change();
        }
    });
}


window['onMagicZoomPlusSelectorClick'] = function(elem) {
    link = $(elem).attr("href");
    variation = getCurrentVariationByImage(link);
    activateVariationAttributes(variation, link);
};

function getName(src) {
    return src.match(/([^\/]*?\.(?:jpg|jpeg|png|gif))/)[1];
}

function change(reset) {
    
    var a = $('.invisImg'),
        img1 = (isWoo301 && !reset) ? a : $('.invisImg img'),
        img2 = $('#MagicZoomPlusImage_Main img'),
        firstName;

    if (!onZoomReady) { return; }
    
    if (!ready || !img1.attr('src')) { return; }
    if (a.attr('href') == $('#MagicZoomPlusImage_Main').attr('href')) { return; } //pevent same images change
        
    firstName = reset ? getName(a.attr('href')) : getName(img1.attr('src'));
    if (!reset) {
        //---------------------------------fix for variation when magic360 is working-----------
        var activeSlide = $('.mt-active').attr('data-magic-slide');
        if(activeSlide != 'zoom'){
           $('.mt-active').removeClass('mt-active');
           $('[data-magic-slide="zoom"]').addClass('mt-active');
           $('.active-selector').removeClass('active-selector');
        }

        //---------------------------------end fix----------------------------------------------
    }

    if (window.current_img && !reset) {
        MagicZoom.update('MagicZoomPlusImage_Main', a.attr('href'), window.current_img);
        window.current_img = false;
    } else {
        if (firstName !== getName(img2.attr('src')) || lastChangedImage !== img1 && !addVarEnabled) {
            MagicZoom.update('MagicZoomPlusImage_Main', a.attr('href'), img1.attr('src'));
            lastChangedImage = img1;
        }
    }
}


$(document).ready(function() {
    prodVariations = window['product_variations_' + prodId];
    clicked = false;
    addVarEnabled = false;

    if (typeof($.wc_additional_variation_images_frontend) == "object" && typeof($.wc_additional_variation_images_frontend.imageSwap) == "function") {
        addVarEnabled = true;
        $.wc_additional_variation_images_frontend.imageSwap_old = $.wc_additional_variation_images_frontend.imageSwap;
        $.wc_additional_variation_images_frontend.imageSwap = function() {
            $.wc_additional_variation_images_frontend.imageSwap_old(arguments[0], arguments[1]);
            if (typeof(MagicZoomPlus) != "undefined") {
                
                if (typeof(arguments[0].gallery_images) != "undefined" && arguments[0].gallery_images.length > 0) { //woo < 3.0
                        
                        $(".MagicToolboxSelectorsContainer").html(arguments[0].gallery_images);
                        $(".MagicToolboxSelectorsContainer a").each(function() {
                            $(this).attr("class", "").attr("rev", $(this).attr("href")).attr("rel", "zoom-id:MagicZoomPlusImage_Main").attr("data-rel", "");
                        });

                        setTimeout("MagicRefresh();", 50);

                } else if (typeof(arguments[0].main_images) != "undefined" && arguments[0].main_images.length > 0) {
                        
                    var newSelectors = $.parseHTML(arguments[0].main_images);

                    if ($(newSelectors).find("img").length > 1) { //apply only if gallery exists
                        
                        $(newSelectors).removeAttr("class");
                        $(newSelectors).find("figure.woocommerce-product-gallery__wrapper").removeAttr("class");
                        $(newSelectors).find("img.attachment-shop_single").removeAttr("class").removeAttr("srcset").removeAttr("width").removeAttr("height");

                        $(newSelectors).find("img").each(function () { return $(this).attr("src",$(this).parent().attr("data-thumb")).attr("style","height: "+thumbHeight+" !important;"); });
                        
                        $(newSelectors).find("figure.woocommerce-product-gallery__image").each(function() {
                            $(this).replaceWith($('<a class="lightbox-added" data-zoom-id="MagicZoomPlusImage_Main">' + this.innerHTML + '</a>'));
                        });
                        
                        $(newSelectors).find("a.lightbox-added").each(function () { 
                            var newHref = $(this).find("> img").attr("data-large_image");
                            return $(this).attr("href",newHref).attr("data-image",newHref);
                            
                        });
                        
                        if (typeof(MagicScroll) !== "undefined" && ($(".MagicToolboxSelectorsContainer .MagicScroll").length || typeof(addScroll) !== "undefined")) {
                            var dataOptions = $(".MagicToolboxSelectorsContainer .MagicScroll").attr('data-options');
                            addScroll = true;
                            MagicScroll.stop();
                        }
                        $(".MagicToolboxSelectorsContainer").html($(newSelectors).find('a'));
                        if (typeof(MagicScroll) !== "undefined" && typeof(addScroll) !== "undefined") {
                            $(".MagicToolboxSelectorsContainer").addClass("MagicScroll").attr('data-options',dataOptions);
                        }
                        
                        setTimeout("MagicRefresh();", 50);
                    }
                }
                
            }
        }
    }
    
    
    if (useWpImages) {
        //get variations array
        if(typeof prodVariations === 'undefined') {
            prodVariations = $.parseJSON($('.variations_form').attr('data-product_variations'));
        };

        //change elements to magictoolbox images
        if (jsonVariations != false)
            $.each(jsonVariations, function(index, value) {
                if (typeof prodVariations != 'undefined') {
                    var resEl = $.grep(prodVariations, function(e) { return e.variation_id == index; });
                } else {
                    var resEl = $.grep($('form.variations_form').data('product_variations'), function(e) { return e.variation_id == index; });
                }

                if (typeof resEl!="undefined" && typeof resEl[0]!="undefined" && resEl[0].image_src !="undefined") {
                    resEl[0].image_src = value.thumb;
                    resEl[0].image_link = value.original;

                    if (isWoo301) {
                        resEl[0].image.url = value.original;
                        resEl[0].image.src = value.thumb;
                        resEl[0].image.full_src = value.original;
                    }
                }
            });

        $('.variations_form').attr('data-product_variations', JSON.stringify(prodVariations));
        $('.variations_form').data('product_variations', prodVariations);
        $('.variations_form').trigger('reload_product_variations');
    }
    
    if (typeof $('form.variations_form')[0] != 'undefined') {
        var onVarChange = $._data($('form.variations_form')[0], 'events').found_variation[0].handler;
        var onVarReset = $._data($('form.variations_form')[0], 'events').reset_image[0].handler;
    }

    if (typeof onVarChange !== 'undefined') {
        $('form.variations_form').on('found_variation', function() {
            change();
        });
    }

    if (typeof onVarReset !== 'undefined') {
        $('form.variations_form').on('reset_image', function() {
            // onVarReset(event=false);
            if ($("a.reset_variations").css('visibility') != 'hidden' && !clicked) { //prevent multiply reset
                change(true);
            }
        });
    }

    ready = true;
    change();
});

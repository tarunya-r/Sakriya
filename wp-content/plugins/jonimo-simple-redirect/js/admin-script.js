jQuery(document).ready(function(jQuery) {   
jQuery('#jj_register_page').change(function(){
    jQuery('#jj_register_cats').prop('selectedIndex',0);
    jQuery('#jj_register_tag').prop('selectedIndex',0);
    jQuery('#jj_register_bp').prop('selectedIndex',0);
});

jQuery('#jj_register_cats').change(function(){
    jQuery('#jj_register_page').prop('selectedIndex',0);
    jQuery('#jj_register_tag').prop('selectedIndex',0);
    jQuery('#jj_register_bp').prop('selectedIndex',0);
});

jQuery('#jj_register_tag').change(function(){
    jQuery('#jj_register_page').prop('selectedIndex',0);
    jQuery('#jj_register_cats').prop('selectedIndex',0);
    jQuery('#jj_register_bp').prop('selectedIndex',0);
});
jQuery('#jj_register_bp').change(function(){
    jQuery('#jj_register_page').prop('selectedIndex',0);
    jQuery('#jj_register_cats').prop('selectedIndex',0);
    jQuery('#jj_register_tag').prop('selectedIndex',0);
});
});




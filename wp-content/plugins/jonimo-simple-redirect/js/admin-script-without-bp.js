//excuse the terrible code here. 
//Its horrible but it works.  

jQuery(document).ready(function(jQuery) {
    
     if(jQuery(".ji-welcome-panel-close").length) {
       jQuery(".ji-welcome-panel-close").click(function() {
          jQuery.post(ajaxurl,
             {action:'ji_redirect_welcome_panel_close',welcomepanelnonce_ji:jQuery('#welcomepanelnonce_ji').val()},
             function(data) {
                if(data == 1) jQuery(".welcome-panel").hide();
             }
          );
          return false;
       });
    }
    
//    var input = jQuery( "#ji_register_custom" );
//    input.val( "http://" + input.val() );
    
    
    if (jQuery('#ji_register_page').val() != 0){
    jQuery( ".form-table tr:nth-child(5)" ).show();
    }
    else if (jQuery('#ji_register_tag').val() != 0){
    jQuery( ".form-table tr:nth-child(5)" ).show();  
    }
    else if (jQuery('#ji_register_cats').val() != 0){
    jQuery( ".form-table tr:nth-child(5)" ).show();  
    }
    else if (jQuery('#ji_register_custom').val() != ''){
    jQuery( ".form-table tr:nth-child(5)" ).show();  
    }
    else {
    jQuery( ".form-table tr:nth-child(5)" ).hide();     
    }

jQuery('#ji_register_page').change(function(){
    jQuery('#ji_register_cats').prop('selectedIndex',0);
    jQuery('#ji_register_tag').prop('selectedIndex',0);
    jQuery('#ji_register_custom').attr('value', "");
    if (jQuery(this).val() != '0'){
    jQuery(".form-table tr:nth-child(5)").show(200);
    }
    else{
    jQuery(".form-table tr:nth-child(5)").hide(200);   
    }
});

jQuery('#ji_register_cats').change(function(){
    jQuery('#ji_register_page').prop('selectedIndex',0);
    jQuery('#ji_register_tag').prop('selectedIndex',0);
    jQuery('#ji_register_custom').attr('value', "");
    if (jQuery(this).val() != '0'){
    jQuery(".form-table tr:nth-child(5)").show(200);
    }
    else{
    jQuery(".form-table tr:nth-child(5)").hide(200);   
    }
});

jQuery('#ji_register_tag').change(function(){
    jQuery('#ji_register_page').prop('selectedIndex',0);
    jQuery('#ji_register_cats').prop('selectedIndex',0);
    jQuery('#ji_register_custom').attr('value', "");
    if (jQuery(this).val() != '0'){
    jQuery(".form-table tr:nth-child(5)").show(200);
    }
    else{
    jQuery(".form-table tr:nth-child(5)").hide(200);   
    }
});

jQuery('#ji_register_custom').on('input',function(){
    jQuery('#ji_register_page').prop('selectedIndex',0);
    jQuery('#ji_register_cats').prop('selectedIndex',0);
    jQuery('#ji_register_tag').prop('selectedIndex',0);
    if (jQuery(this).val() != ''){
    jQuery(".form-table tr:nth-child(5)").show(200);
    }
    else{
    jQuery(".form-table tr:nth-child(5)").hide(200);   
    }
});


});


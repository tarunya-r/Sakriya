/*
 * Settings of the sticky menu
 */

jQuery(document).ready(function(){
   var wpAdminBar = jQuery('#wpadminbar');
   if (wpAdminBar.length) {
      jQuery("#sportsmag-menu-wrap").sticky({topSpacing:wpAdminBar.height()});
   } else {
      jQuery("#sportsmag-menu-wrap").sticky({topSpacing:0});
   }
});
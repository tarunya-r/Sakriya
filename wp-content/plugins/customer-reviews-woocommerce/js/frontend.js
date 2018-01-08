jQuery(document).ready(function($) {
  //enable attachment of images to comments
  jQuery('form#commentform').attr( "enctype", "multipart/form-data" ).attr( "encoding", "multipart/form-data" );
  //prevent review submission if captcha is not solved
  jQuery("#commentform").submit(function(event) {
    var recaptcha = jQuery("#g-recaptcha-response").val();
    if (recaptcha === "") {
      event.preventDefault();
      alert("Please confirm that you are not a robot");
    }
  });
  //show lightbox when click on images attached to reviews
  jQuery(".ivole-comment-a").click(function(t) {
    t.preventDefault();
    var o = jQuery(".pswp")[0];
    var pics = jQuery(this).parent().parent().find("img");
    var this_pic = jQuery(this).find("img");
    var inx = 0;
    if(pics.length > 0 && this_pic.length > 0) {
      var a = [];
      for(i=0; i<pics.length; i++) {
        a.push({
          src: pics[i].src,
          w: pics[i].naturalWidth,
          h: pics[i].naturalHeight,
          title: pics[i].alt
        });
        if(this_pic[0].src == pics[i].src) {
          inx = i;
        }
      }
      var r = {
        index: inx
      };
      new PhotoSwipe(o,PhotoSwipeUI_Default,a,r).init();
    }
  });
});

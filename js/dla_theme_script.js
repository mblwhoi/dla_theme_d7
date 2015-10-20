(function ($) {
  Drupal.behaviors.responsiveBartik = {
    attach: function(context, settings) {
      $('#main-menu > ul', context).superfish(function () {
            hoverClass:  'sfHover'
            speed:       'fast'
            autoArrows:  false
            dropShadows: false
            disableHI:   true
     
      }).supposition();
    }
  };


})(jQuery);

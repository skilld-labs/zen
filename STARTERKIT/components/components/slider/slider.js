(function (Drupal, $, debounce, drupalSettings) {
  console.log('bu');
  Drupal.behaviors.slider = {
    attach: function (context, settings) {
      var opt = [
        {
          slider: $('.slick-slider .field__items'),
          options: {
            dots: true,
            infinite: false,
            slidesToScroll: 1,
            slidesToShow: 1,
            speed: 300
          }
        }
      ];

      for (var i = 0; i < opt.length; i++) {
        this.initialize(opt[i].slider, opt[i].options);
      }
    },
    initialize: function (slider, options) {
      slider.slick(options);
    }
  };
})(Drupal, jQuery, Drupal.debounce, drupalSettings);

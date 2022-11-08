import { initSlick } from './utils/helper';

export default {
  finalize() {
    /**
     * Featured Collection Slider
     * @author Rhythm Shahriar <me@rhy.io>
     */
    $('.featured-slider-slide:not(.featured-slider-slide--active) .featured-slider-slide-col-left').fadeOut('slow');
    $('.featured-slider-slide:not(.featured-slider-slide--active) .featured-slider-slide-col-right').fadeOut('slow');
    $('body').on('click', '.featured-slider-slide-title h3', function() {
      $('.featured-slider-slide--active .featured-slider-slide-col-left').fadeOut('slow');
      $('.featured-slider-slide--active .featured-slider-slide-col-right').fadeOut('slow');

      const parentSlide = $(this).parents('.featured-slider-slide');

      setTimeout(() => {
        $('.featured-slider-slide').each(function() {
          $(this).removeClass('featured-slider-slide--active');
          const slideTitle = $(this).find('.featured-slider-slide-title h3');
          slideTitle.removeClass('featured-slider-slide-title--disabled');
          slideTitle.text(slideTitle.data('title'));
        });
        parentSlide.addClass('featured-slider-slide--effect featured-slider-slide--active');
        parentSlide.find('.featured-slider-slide-title h3').addClass('featured-slider-slide-title--disabled').text('Featured collection');
        $('.featured-slider-slide--active .featured-slider-slide-col-left').fadeIn('slow');
        $('.featured-slider-slide--active .featured-slider-slide-col-right').fadeIn('slow');
      }, 500);
    });

    /**
     * Editor's Picks slider
     */
    initSlick('.home-editors-picks-left');
  },
};

export default {
  init() {
    // Find all YouTube & Vimeo videos
    const $allVideos = $(
      `.single-content iframe[src^='http://player.vimeo.com'], .single-content iframe[src^='https://player.vimeo.com'],
       .single-content iframe[src^='http://www.youtube.com'], .single-content iframe[src^='https://www.youtube.com']`,
    );
    // The element that is fluid width
    const $fluidEl = $('.single .single-content');

    // Figure out and save aspect ratio for each video
    $allVideos.each(function() {
      $(this)
        .attr('data-aspectRatio', this.height / this.width)
        // and remove the hard coded width/height
        .removeAttr('height')
        .removeAttr('width');
    });

    // When the window is resized
    $(window)
      .resize(() => {
        const SCREEN_WIDTH = window.innerWidth
          || document.documentElement.clientWidth
          || document.body.clientWidth;

        let newWidth = $fluidEl.width();
        newWidth = newWidth > 1168 ? 1168 : newWidth;

        if (SCREEN_WIDTH > 1023) {
          newWidth -= 516;
        }

        // Resize all videos according to their own aspect ratio
        $allVideos.each(function() {
          const $el = $(this);
          $el.width(newWidth).height(newWidth * $el.attr('data-aspectRatio'));
        });
      })
      .resize();
  },
  finalize() {
    /* Expand table of contents */
    $('.single-toc__title, .single-toc__expand').on('click', function() {
      $(this).parents('.single-toc').toggleClass('active');
    });

    $('body').on('click', 'a[href^="#"]', function(e) {
      e.preventDefault();

      $('html, body').animate({
        scrollTop: $($.attr(this, 'href')).offset().top - 135,
      }, 300);
    });

    /* Progress bar */
    $(window).on('scroll', () => {
      const content = $('.single-content');
      const progress = (($(window).height() - content.get(0).getBoundingClientRect().top)
                       / content.innerHeight()) * 100;
      $('.single-nav__progress').css('width', `${progress}%`);
    });

    /* Article sticky nav */
    let lastScrolledPos = 0;
    $(window).on('scroll', () => {
      const currentScrollPos = $(window).scrollTop();
      const singleNav = $('.single-nav');
      if (currentScrollPos < lastScrolledPos) {
        singleNav.addClass('single-nav--slide-down');
        singleNav.removeClass('single-nav--slide-up');
      } else {
        singleNav.addClass('single-nav--slide-up');
        singleNav.removeClass('single-nav--slide-down');
      }
      if (currentScrollPos > 65) {
        singleNav.addClass('single-nav--border-bottom-fluid');
        singleNav.removeClass('single-nav--hide');
      } else {
        singleNav.addClass('single-nav--hide');
        singleNav.removeClass('single-nav--border-bottom-fluid');
      }
      lastScrolledPos = currentScrollPos;
    });

    /* Make sticky nav appear once scrolled past sidebar nav */
    $(window).on('scroll', () => {
      let contentAnchor;
      if (!$('.single-nav .single-toc').length) {
        return;
      }
      if ($('.single-nav .single-toc').length > 0) {
        contentAnchor = $('.single-content .single-toc');
      } else if ($('.single-nav .tools-download').length > 0) {
        contentAnchor = $('.single-content .tools-download:eq(0)');
      }
      if (contentAnchor.length > 0) {
        const scrollOffset = contentAnchor.offset().top + contentAnchor.outerHeight();
        if ($(window).scrollTop() > scrollOffset) {
          $('.single-nav').addClass('single-nav--toc-enter');
        } else {
          $('.single-nav').removeClass('single-nav--toc-enter');
        }
      }
    });

    /* Click outside sticky nav ToC, scroll to the top or click on ToC links to close */
    $('.single-nav .single-toc a').on('click', () => {
      $('.single-nav .single-toc').removeClass('active');
    });
    // Close when clicked outside.
    $('body').on('click', (e) => {
      if ($('.single-nav .single-toc').length && !$('.single-nav .single-toc').get(0).contains(e.target)) {
        $('.single-nav .single-toc').removeClass('active');
      }
    });
    // Close when scrolled to the top of content ToC.
    $(window).on('scroll', () => {
      const contentToc = $('.single-content .single-toc');
      if (contentToc.length > 0) {
        const scrollOffset = contentToc.offset().top + contentToc.outerHeight();
        if ($(window).scrollTop() < scrollOffset) {
          if ($('.single-nav .single-toc').length && $('.single-nav .single-toc').hasClass('active')) {
            $('.single-nav .single-toc').removeClass('active');
          }
        }
      }
    });

    /* Remove empty p tags */
    $('.single-content p').each(function() {
      if ($(this).html().replace(/\s| /g, '').length === 0) {
        $(this).remove();
      }
    });

    /**
     * Copy share link
     */
    $('body').on('click', '.single-hero__share-link', function(e) {
      e.preventDefault();
      const copiedLink = $(this).find('a').attr('href');
      navigator.clipboard.writeText(copiedLink);
      $(this).find('.tooltip__text').text('Copied');
    });

    $('body').on('mouseleave', '.single-hero__share-link', function() {
      $(this).find('.tooltip__text').text('Copy');
    });
  },
};

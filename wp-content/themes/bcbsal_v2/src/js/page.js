import { isMobile } from './utils/helper';

export default {
  finalize() {
    // prepare contributors list
    const perPage = 9;
    const contributors = [];
    let totalContributors = 0;
    $('.archive-contributors [class*=col-sm-]').each(function() {
      contributors.push(`<div class="col-sm-6 col-md-4">${$(this).html()}</div>`);
      totalContributors += 1;
    });

    let onViewPort = 0;
    const aboutPageContributorsHandler = () => {
      // remove all the contributors
      $('.archive-contributors [class*=col-sm-]').remove();

      if (isMobile() && contributors.length > 9) {
        // reset the count
        onViewPort = 0;
        const mobileContributors = contributors.slice(0, perPage);
        mobileContributors.forEach((contributor) => {
          $('.archive-contributors .container .row').append(contributor);
          onViewPort += 1;
        });
        $('.archive-contributors__load-more').fadeIn();
      } else {
        contributors.forEach((contributor) => {
          $('.archive-contributors .container .row').append(contributor);
        });
        $('.archive-contributors__load-more').fadeOut();
      }
    };

    // load more button click
    $('body').on('click', '.archive-contributors__load-more-button', () => {
      if (onViewPort < totalContributors) {
        const loadContributors = contributors.slice(onViewPort, onViewPort + perPage);
        loadContributors.forEach((contributor) => {
          $('.archive-contributors .container .row').append(contributor);
          onViewPort += 1;
        });

        if (onViewPort >= totalContributors) {
          $('.archive-contributors__load-more').fadeOut();
        }
      } else {
        $('.archive-contributors__load-more').fadeOut();
      }
    });

    // adjust contributors visibility based on screen size
    aboutPageContributorsHandler();
    $(window).resize(() => {
      aboutPageContributorsHandler();
    });
  },
};

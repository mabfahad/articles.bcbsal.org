export default {
  finalize() {
    /**
     * Category based filter load more
     * @type {*|jQuery|HTMLElement}
     */
    let filterByCategoriesOffset = $('.filter-by-categories__posts').data('offset');
    let filterByCategoriesTotal = $('.filter-by-categories__posts').data('total');
    const filterPostsPerPage = $('.filter-by-categories__posts').data('per-page');

    function ajaxfilterByCategories(isInitialPage = false, categoryId = false) {
      const filterContainer = $('.filter-by-categories__posts');

      // set for ajax request
      const postData = {
        offset: isInitialPage ? 0 : filterByCategoriesOffset,
        tag_id: filterContainer.data('tag'),
        category_id: categoryId || filterContainer.data('category'),
        per_page: isInitialPage ? filterPostsPerPage : 4,
        action: 'LOAD_MORE_FILTER_BY_CATEGORIES',
        // eslint-disable-next-line no-undef
        security: surge.filter_by_categories_load_more_nonce,
      };

      // eslint-disable-next-line no-undef
      $.post(surge.ajax_url, postData, (response) => {
        if (isInitialPage) {
          filterContainer.html(response);
        } else {
          filterContainer.append(response);
        }

        // update loaded posts count
        if (isInitialPage) {
          filterByCategoriesOffset = filterPostsPerPage;
          filterContainer.attr('data-offset', filterByCategoriesOffset);
        } else {
          filterByCategoriesOffset += 4;
        }
      });
    }

    // make ajax request
    $('body').on('click', '.filter-by-categories-load-more .button', () => {
      ajaxfilterByCategories(false);
    });

    // filter view all
    $('.filter-by-categories__view-all').on('click', function() {
      $(this).hide();
      $('.filter-by-categories__topics').addClass('filter-by-categories__topics--expanded');
    });

    //  filter show popup on mobile
    $('.filter-by-categories__popup-toggle').on('click', () => {
      if ($(window).width() < 768) {
        $('.filter-by-categories__popup').addClass('filter-by-categories__popup--active');
      }
    });

    // filter hide popup on mobile
    $('.filter-by-categories__popup--close').on('click', () => {
      $('.filter-by-categories__popup').removeClass('filter-by-categories__popup--active');
    });

    // filter selection
    $('.filter-by-categories__topic:not(.filter-by-categories__popup-toggle)').on('click', function() {
      const button = $(this);
      const categoryId = button.data('category-id');
      const totalPosts = button.data('total-posts');
      const filterContainer = $('.filter-by-categories__posts');

      // update filter data and call ajax
      filterContainer.attr('data-category', categoryId);
      filterContainer.attr('data-total', totalPosts);
      filterContainer.attr('data-offset', 0);
      filterByCategoriesTotal = totalPosts;

      if (totalPosts > 6 || categoryId === 'all') {
        $('.filter-by-categories-load-more').removeAttr('style');
      }

      ajaxfilterByCategories(true, categoryId);

      // handle mobile to desktop
      if ($(this).index() > 5) {
        if (!$('.filter-by-categories__topics').hasClass('filter-by-categories__topics--expanded')) {
          $('.filter-by-categories__view-all').hide();
          $('.filter-by-categories__topics').addClass('filter-by-categories__topics--expanded');
        }
      }

      // Remove `active` class from other buttons.
      $('.filter-by-categories__topic:not(.filter-by-categories__popup-toggle)').removeClass('filter-by-categories__topic--active');
      $(`.filter-by-categories__topic-${categoryId}`).addClass('filter-by-categories__topic--active');

      // Show selected topic in mobile popup toggle button.
      const topicName = button.find('.filter-by-categories__topic--name').text();
      const topicCount = button.find('.filter-by-categories__topic--count').text();
      $('.filter-by-categories__popup-toggle .filter-by-categories__topic--name').text(topicName);
      $('.filter-by-categories__popup-toggle .filter-by-categories__topic--count').text(topicCount);
      $('.filter-by-categories__popup').removeClass('filter-by-categories__popup--active');

      // Add `active` class to other instance of the button (mobile/desktop).
      $(`.filter-by-categories__topic[data-category-id=${button.data('category-id')}]`).not(button).addClass('filter-by-categories__topic--active');
    });

    // check if more posts available
    $('body').on('DOMSubtreeModified', '.filter-by-categories__posts', () => {
      let postsCounter = 0;

      // get total number of loaded posts
      $('.filter-by-categories__posts .filter-by-categories__post').each(() => {
        postsCounter += 1;
      });

      // if all the posts loaded hide the load more button to
      if (postsCounter >= parseInt(filterByCategoriesTotal, 10)) {
        $('.filter-by-categories-load-more').fadeOut();
      }
    });
  },
};

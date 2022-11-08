import { initSlick } from './utils/helper';

export default {
  finalize() {
    initSlick('.archive-featured-articles__posts.masonry');
    initSlick('.archive-tools__posts');
    initSlick('.archive-case-studies__posts');
    initSlick('.archive-featured-collection__posts');
    initSlick('.archive-featured-podcast__posts');

    /**
     * Tools page explore more
     * @type {*|jQuery|HTMLElement}
     */
    let toolsOffset = $('.archive-explore-tools-posts').data('offset');
    $('body').on('click', '.archive-explore-tools-load-more__button', () => {
      // set for ajax request
      const postData = {
        offset: toolsOffset,
        action: 'LOAD_MORE_EXPLORE_TOOLS',
        // eslint-disable-next-line no-undef
        security: surge.explore_tools_load_more_nonce,
      };

      // make ajax request
      // eslint-disable-next-line no-undef
      $.post(surge.ajax_url, postData, (response) => {
        $('.archive-explore-tools-posts').append(response);

        // update loaded posts count
        toolsOffset += 4;
      });

      // check if more posts available
      $('body').on('DOMSubtreeModified', '.archive-explore-tools-posts', () => {
        let postsCounter = 0;

        // get total number of loaded posts
        $('.archive-explore-tools-posts .archive-explore-tools-posts__post').each(() => {
          postsCounter += 1;
        });

        // if all the posts loaded hide the load more button to
        if (postsCounter >= parseInt($('.archive-explore-tools-posts').data('total'), 10)) {
          $('.archive-explore-tools-load-more').fadeOut();
        }
      });
    });

    /**
     * Collections page load more
     * @type {*|jQuery|HTMLElement}
     */
    let collectionsOffset = $('.archive-collections-posts').data('offset');
    $('body').on('click', '.archive-collections-load-more__button', () => {
      // set for ajax request
      const postData = {
        offset: collectionsOffset,
        featured: $('.archive-collections-posts').data('featured'),
        action: 'LOAD_MORE_COLLECTIONS',
        // eslint-disable-next-line no-undef
        security: surge.collections_load_more_nonce,
      };

      // make ajax request
      // eslint-disable-next-line no-undef
      $.post(surge.ajax_url, postData, (response) => {
        $('.archive-collections-posts').append(response);

        // update loaded posts count
        collectionsOffset += 4;
      });

      // check if more posts available
      $('body').on('DOMSubtreeModified', '.archive-collections-posts', () => {
        let postsCounter = 0;

        // get total number of loaded posts
        $('.archive-collections-posts .archive-collections-posts__post').each(() => {
          postsCounter += 1;
        });

        // if all the posts loaded hide the load more button to
        if (postsCounter >= parseInt($('.archive-collections-posts').data('total'), 10)) {
          $('.archive-collections-load-more').fadeOut();
        }
      });
    });

    /**
     * Podcasts page load more
     * @type {*|jQuery|HTMLElement}
     */
    let podcastsOffset = $('.archive-more-podcasts-posts').data('offset');
    $('body').on('click', '.archive-more-podcasts-load-more__button', () => {
      // set for ajax request
      const postData = {
        offset: podcastsOffset,
        featured: $('.archive-more-podcasts-posts').data('featured'),
        action: 'LOAD_MORE_PODCASTS',
        // eslint-disable-next-line no-undef
        security: surge.podcasts_load_more_nonce,
      };

      // make ajax request
      // eslint-disable-next-line no-undef
      $.post(surge.ajax_url, postData, (response) => {
        $('.archive-more-podcasts-posts').append(response);

        // update loaded posts count
        podcastsOffset += 4;
      });

      // check if more posts available
      $('body').on('DOMSubtreeModified', '.archive-more-podcasts-posts', () => {
        let postsCounter = 0;

        // get total number of loaded posts
        $('.archive-more-podcasts-posts .archive-more-podcasts-posts__post').each(() => {
          postsCounter += 1;
        });

        // if all the posts loaded hide the load more button to
        if (postsCounter >= parseInt($('.archive-more-podcasts-posts').data('total'), 10)) {
          $('.archive-more-podcasts-load-more').fadeOut();
        }
      });
    });

    /**
     * Tag based filter load more
     * @type {*|jQuery|HTMLElement}
     */
    let filterByTagsOffset = $('.filter-by-tags__posts').data('offset');
    let filterByTagsTotal = $('.filter-by-tags__posts').data('total');
    const filterPostsPerPage = $('.filter-by-tags__posts').data('per-page');

    function ajaxFilterByTags(isInitialPage = false, tagId = false) {
      const filterContainer = $('.filter-by-tags__posts');

      // set for ajax request
      const postData = {
        offset: isInitialPage ? 0 : filterByTagsOffset,
        tag_id: tagId || filterContainer.data('tag'),
        category_id: filterContainer.data('category'),
        per_page: isInitialPage ? filterPostsPerPage : 4,
        action: 'LOAD_MORE_FILTER_BY_TAGS',
        // eslint-disable-next-line no-undef
        security: surge.filter_by_tags_load_more_nonce,
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
          filterByTagsOffset = filterPostsPerPage;
          filterContainer.attr('data-offset', filterByTagsOffset);
        } else {
          filterByTagsOffset += 4;
        }
      });
    }

    // make ajax request
    $('body').on('click', '.filter-by-tags-load-more .button', () => {
      ajaxFilterByTags(false);
    });

    /**
     * Tags based filter
     * @type {*|jQuery|HTMLElement}
     */
    // filter view all
    $('.filter-by-tags__view-all').on('click', function() {
      $(this).hide();
      $('.filter-by-tags__topics').addClass('filter-by-tags__topics--expanded');
    });

    //  filter show popup on mobile
    $('.filter-by-tags__popup-toggle').on('click', () => {
      if ($(window).width() < 768) {
        $('.filter-by-tags__popup').addClass('filter-by-tags__popup--active');
      }
    });

    // filter hide popup on mobile
    $('.filter-by-tags__popup--close').on('click', () => {
      $('.filter-by-tags__popup').removeClass('filter-by-tags__popup--active');
    });

    // filter selection
    $('.filter-by-tags__topic:not(.filter-by-tags__popup-toggle)').on('click', function() {
      const button = $(this);
      const tagId = button.data('tag-id');
      const totalPosts = button.data('total-posts');
      const filterContainer = $('.filter-by-tags__posts');

      // update filter data and call ajax
      filterContainer.attr('data-tag', tagId);
      filterContainer.attr('data-total', totalPosts);
      filterContainer.attr('data-offset', 0);
      filterByTagsTotal = totalPosts;

      if (totalPosts > 6 || tagId === 'all') {
        $('.filter-by-tags-load-more').removeAttr('style');
      }

      ajaxFilterByTags(true, tagId);

      // handle mobile to desktop
      if ($(this).index() > 5) {
        if (!$('.filter-by-tags__topics').hasClass('filter-by-tags__topics--expanded')) {
          $('.filter-by-tags__view-all').hide();
          $('.filter-by-tags__topics').addClass('filter-by-tags__topics--expanded');
        }
      }

      // Remove `active` class from other buttons.
      $('.filter-by-tags__topic:not(.filter-by-tags__popup-toggle)').removeClass('filter-by-tags__topic--active');
      $(`.filter-by-tags__topic-${tagId}`).addClass('filter-by-tags__topic--active');

      // Show selected topic in mobile popup toggle button.
      const topicName = button.find('.filter-by-tags__topic--name').text();
      const topicCount = button.find('.filter-by-tags__topic--count').text();
      $('.filter-by-tags__popup-toggle .filter-by-tags__topic--name').text(topicName);
      $('.filter-by-tags__popup-toggle .filter-by-tags__topic--count').text(topicCount);
      $('.filter-by-tags__popup').removeClass('filter-by-tags__popup--active');

      // Add `active` class to other instance of the button (mobile/desktop).
      $(`.filter-by-tags__topic[data-tag-id=${button.data('tag-id')}]`).not(button).addClass('filter-by-tags__topic--active');
    });

    // check if more posts available
    $('body').on('DOMSubtreeModified', '.filter-by-tags__posts', () => {
      let postsCounter = 0;

      // get total number of loaded posts
      $('.filter-by-tags__posts .filter-by-tags__post').each(() => {
        postsCounter += 1;
      });

      // if all the posts loaded hide the load more button to
      if (postsCounter >= parseInt(filterByTagsTotal, 10)) {
        $('.filter-by-tags-load-more').fadeOut();
      }
    });
  },
};

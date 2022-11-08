import { taxValidation, isMobile } from './utils/helper';

export default {
  init() {
    // on page load set the data attributes
    const urlParams = new URLSearchParams(window.location.search);
    const s = urlParams.get('s');
    const cat = urlParams.get('cat');
    const ct = urlParams.get('ct');
    const t = urlParams.get('t');
    const ind = urlParams.get('ind');
    const soln = urlParams.get('soln');

    const datasetNode = $('.search-results-posts');
    if (s) {
      datasetNode.attr('data-s', s);
    } else {
      datasetNode.attr('data-s', '');
    }
    if (cat && taxValidation(cat, 'cat')) {
      datasetNode.attr('data-cat', cat);
    }
    if (ct && taxValidation(ct, 'ct')) {
      datasetNode.attr('data-ct', ct);
    }
    if (t && taxValidation(t, 't')) {
      datasetNode.attr('data-t', t);
    }
    if (ind && taxValidation(ind, 'ind')) {
      datasetNode.attr('data-ind', ind);
    }
    if (soln && taxValidation(soln, 'soln')) {
      datasetNode.attr('data-soln', soln);
    }
  },
  finalize() {
    // prepare the URL for search
    const prepareURL = (categorySlug = '', tagSlug = '', dt = '', query = '') => {
      const urlParams = new URLSearchParams(window.location.search);
      const s = urlParams.get('s').replace(' ', '+');
      const cat = urlParams.get('cat');
      const ct = urlParams.get('ct');
      const t = urlParams.get('t');
      const ind = urlParams.get('ind');
      const soln = urlParams.get('soln');

      let params = [];
      if (query || query === ' ') {
        params.push({ s: query.trim().replace(' ', '+') });
      } else if (s) {
        params.push({ s });
      } else {
        params.push({ s: '' });
      }

      if (dt === 'ct' && tagSlug) {
        params.push({ ct: tagSlug });
      } else if (ct) {
        params.push({ ct });
      }
      if (dt === 't' && tagSlug) {
        params.push({ t: tagSlug });
      } else if (t) {
        params.push({ t });
      }
      if (dt === 'ind' && tagSlug) {
        params.push({ ind: tagSlug });
      } else if (ind) {
        params.push({ ind });
      }
      if (dt === 'soln' && tagSlug) {
        params.push({ soln: tagSlug });
      } else if (soln) {
        params.push({ soln });
      }

      if (categorySlug) {
        params.push({ cat: categorySlug });
      } else if (cat) {
        params.push({ cat });
      }

      params = Object.assign({}, ...(params.map((p) => (
        { [Object.keys(p)[0]]: p[Object.keys(p)[0]] }))));

      // eslint-disable-next-line no-undef
      return `${surge.base_url}?${Object.keys(params).map((key) => `${key}=${params[key]}`).join('&')}`;
    };

    /**
     * Helper for search filters reset visibility
     */
    const searchFilterRestButton = () => {
      const dataSetNode = $('.search-results-posts');
      const cat = dataSetNode.attr('data-cat');
      const ct = dataSetNode.attr('data-ct');
      const t = dataSetNode.attr('data-t');
      const ind = dataSetNode.attr('data-ind');
      const soln = dataSetNode.attr('data-soln');

      if ((cat && cat !== 'all') || ct || t || ind || soln) {
        $('.search-results__clear-filter').removeClass('dnone');
      } else {
        $('.search-results__clear-filter').addClass('dnone');
      }
    };

    // on load check reset button visibility
    searchFilterRestButton();

    // on category filter select check reset button visibility
    $('body').on('click', '.search-categories__topic, .search-tags__select', () => {
      searchFilterRestButton();
    });

    /**
     * Helper function to update categories filter
     * @param categories
     * @param total
     */
    function updateCategoryFilter(categories, total) {
      const allCategoryPostsNode = $('.search-categories__topic-all:not(.search-categories__popup-toggle)');
      const popupSelector = $('.search-categories__popup-toggle');

      if (categories.length > 0) {
        $('.search-categories__topic').removeClass('no-posts');
        categories.forEach((category) => {
          if (popupSelector.find('.search-categories__topic--name').text() === category.name) {
            popupSelector.find('.search-categories__topic--count').text(category.posts_count);
          }

          const topic = $(`.search-categories__topic-${category.slug}`);
          topic.find('.search-categories__topic--count').text(category.posts_count);
          if (category.posts_count === 0) {
            topic.addClass('no-posts');
          }
        });
      }

      allCategoryPostsNode.removeClass('no-posts');
      allCategoryPostsNode.find('.search-categories__topic--count').text(total);

      popupSelector.attr('data-initial-total', total);
      if (popupSelector.find('.search-categories__topic--name').text() === 'All') {
        popupSelector.find('.search-categories__topic--count').text(total);
      }
      if (total === 0) {
        allCategoryPostsNode.addClass('no-posts');
      }
    }

    /**
     * Helper function to update tags filters
     * @param tags
     * @param type
     */
    function updateSingleSelectTags(tags, type) {
      if (tags.length > 0) {
        let total = 0;
        tags.forEach((tag) => {
          total += tag.count;
          const option = $(`.search-single-select-${type} .search-single-select__options-option-${tag.value}`);
          option.removeClass('no-posts');
          if (tag.count === 0) {
            $(`.search-single-select__options-option-${tag.value}`).addClass('no-posts');
          }
        });

        $(`.search-single-select-${type} .search-single-select__options-option-all`).removeClass('no-posts');
        if (total === 0) {
          $(`.search-single-select-${type} .search-single-select__options-option-all`).addClass('no-posts');
        }
      }
    }

    // search content load more
    let searchPostsOffset = $('.search-results-posts').data('offset') + 1;

    /**
     * Helper function for Ajax load more
     * @param updateFilter
     */
    function ajaxLoadMoreHandler(updateFilter = false) {
      const dataSetNode = $('.search-results-posts');

      // enable loader
      $('.search-results__loader').show();

      // set for ajax request
      const postData = {
        offset: searchPostsOffset,
        total: dataSetNode.attr('data-total'),
        search: dataSetNode.attr('data-s'),
        category: dataSetNode.attr('data-cat'),
        content_type: dataSetNode.attr('data-ct'),
        topic: dataSetNode.attr('data-t'),
        industry: dataSetNode.attr('data-ind'),
        solutions: dataSetNode.attr('data-soln'),
        update_filter: updateFilter,
        action: 'LOAD_MORE_SEARCH_PAGE',
        // eslint-disable-next-line no-undef
        security: surge.search_page_load_more_nonce,
      };

      // make ajax request
      // eslint-disable-next-line no-undef
      $.post(surge.ajax_url, postData, (data) => {
        // disable loader
        $('.search-results__loader').hide();

        const response = $.parseJSON(data);

        // update filter only if filter is selected
        // or just append the HTML
        if (updateFilter) {
          $('.search-results-posts').html(response.posts);
          $('.search-results-posts').attr('data-total', response.total);
          $('.search-results__count-text').text(response.total);
          updateCategoryFilter(response.categories, response.all_category_posts);
          updateSingleSelectTags(response.content_types, 'ct');
          updateSingleSelectTags(response.topics, 't');
          updateSingleSelectTags(response.industries, 'ind');
          updateSingleSelectTags(response.solutions, 'soln');
        } else {
          $('.search-results-posts').append(response.posts);
        }

        // update loaded posts count
        searchPostsOffset += 10;
        const postsLoaded = $('.search-results-posts__post').length;

        if (response.total > 0) {
          $('.search-results-no-posts').hide();
          // eslint-disable-next-line no-dupe-else-if
        }
        // show no posts.
        if (response.total === 0) {
          $('.search-results-no-posts').show();
        }
        if (postsLoaded < response.total) {
          $('.search-results-posts-load-more').fadeIn();
        }
        if (postsLoaded >= response.total - 1) {
          $('.search-results-posts-load-more').fadeOut();
        }
      });
    }

    // reset the search filters
    $('body').on('click', '.search-results__clear-filter', () => {
      // rest data set
      const dataSetNode = $('.search-results-posts');
      dataSetNode.attr('data-cat', '');
      dataSetNode.attr('data-ct', '');
      dataSetNode.attr('data-t', '');
      dataSetNode.attr('data-ind', '');
      dataSetNode.attr('data-soln', '');

      // call ajax
      searchPostsOffset = 0;
      ajaxLoadMoreHandler(true);

      // replace the current entry in the browser's history, without reloading
      // eslint-disable-next-line no-undef
      window.history.replaceState(null, $(document).find('title').text(), `${surge.base_url}/?s=${dataSetNode.attr('data-s').replace(' ', '+')}`);

      // reset categories
      $('.search-categories__topic').removeClass('search-categories__topic--active');
      $('.search-categories__topic-all').addClass('search-categories__topic--active');

      const defaultMobileNode = $('.search-categories__popup-toggle');
      defaultMobileNode.find('.search-categories__topic--name').text(defaultMobileNode.attr('data-initial-label'));
      defaultMobileNode.find('.search-categories__topic--count').text(defaultMobileNode.attr('data-initial-total'));

      // reset tags
      $('.search-single-select').removeClass('search-single-select--changed');
      $('.search-single-select__options-option').removeClass('search-single-select--selected');

      $('.search-single-select').each(function() {
        const selected = $(this).find('.search-single-select__selected-option');
        selected.text(selected.attr('data-default'));
        selected.attr('data-value', '');
      });
      $('.search-single-select__selected').removeClass('search-single-select--selected');

      $('.search-results__clear-filter').addClass('dnone');
    });

    // load more button event
    $('body').on('click', '.search-results-posts-load-more__button', () => {
      ajaxLoadMoreHandler();
    });

    // search form on submit event
    let searchQuery = '';
    $('body').on('submit', '.search-form', (e) => {
      e.preventDefault();

      // search input
      let query = $('.search-form__input--main').val();

      if (searchQuery !== query) {
        searchQuery = query;
        query = query || ' ';
        // replace the current entry in the browser's history, without reloading
        window.history.replaceState(null, $(document).find('title').text(), prepareURL('', '', '', query));
      }
      // update data set
      $('.search-results-posts').attr('data-s', query);

      // call ajax
      searchPostsOffset = 0;
      ajaxLoadMoreHandler(true);
    });

    //  filter show popup on mobile
    $('.search-categories__popup-toggle').on('click', () => {
      if (isMobile()) {
        $('.search-categories__popup').addClass('search-categories__popup--active');
      }
    });

    // filter hide popup on mobile
    $('.search-categories__popup--close').on('click', () => {
      $('.search-categories__popup').removeClass('search-categories__popup--active');
    });

    // filter selection
    $('.search-categories__topic:not(.search-categories__popup-toggle)').on('click', function() {
      const button = $(this);
      const categorySlug = button.attr('data-category-slug');

      // update results data set
      if (taxValidation(categorySlug, 'cat') || categorySlug === 'all') {
        $('.search-results-posts').attr('data-cat', categorySlug);

        // call ajax
        searchPostsOffset = 0;
        ajaxLoadMoreHandler(true);

        // replace the current entry in the browser's history, without reloading
        window.history.replaceState(null, $(document).find('title').text(), prepareURL(categorySlug));
      }

      // Remove `active` class from other buttons.
      $('.search-categories__topic:not(.search-categories__popup-toggle)').removeClass('search-categories__topic--active');
      $(`.search-categories__topic-${categorySlug}`).addClass('search-categories__topic--active');

      // Show selected topic in mobile popup toggle button.
      const topicName = button.find('.search-categories__topic--name').text();
      const topicCount = button.find('.search-categories__topic--count').text();
      $('.search-categories__popup-toggle .search-categories__topic--name').text(topicName);
      $('.search-categories__popup-toggle .search-categories__topic--count').text(topicCount);
      $('.search-categories__popup').removeClass('search-categories__popup--active');

      // Add `active` class to other instance of the button (mobile/desktop).
      $(`.search-categories__topic[data-category-slug=${button.data('category-slug')}]`).not(button).addClass('search-categories__topic--active');
    });

    // single select component
    $('.search-single-select__selected').on('click', function() {
      $(this).parent().toggleClass('search-single-select--expand');

      if (isMobile()) {
        $(this).parent().find('.search-single-select__options').fadeIn();
      } else {
        $(this).parent().find('.search-single-select__options').slideToggle();
      }
    });

    // tags filter popup close event
    $('.search-single-select__options-header-close').on('click', function() {
      $(this).parents('.search-single-select').removeClass('search-single-select--expand');
      $(this).parents('.search-single-select').find('.search-single-select__options').fadeOut();
    });

    // tags filter option select event
    $('.search-single-select__options-option').on('click', function() {
      $(this).siblings().removeClass('search-single-select__options-option--selected');
      const parentNode = $(this).parents('.search-single-select');
      const selectedNode = parentNode.find('.search-single-select__selected').find('.search-single-select__selected-option');
      const value = $(this).attr('data-value');
      const dataType = selectedNode.attr('data-type');

      selectedNode.text($(this).text());
      selectedNode.attr('data-value', value);

      if (taxValidation(value, dataType) || value === 'all') {
        $('.search-results-posts').attr(`data-${dataType}`, value);

        // call ajax
        searchPostsOffset = 0;
        ajaxLoadMoreHandler(true);

        // replace the current entry in the browser's history, without reloading
        window.history.replaceState(null, $(document).find('title').text(), prepareURL('', value, dataType));
      }

      $(this).addClass('search-single-select__options-option--selected');
      parentNode.removeClass('search-single-select--expand');
      parentNode.addClass('search-single-select--changed');

      if (isMobile()) {
        parentNode.find('.search-single-select__options').fadeOut();
      } else {
        parentNode.find('.search-single-select__options').slideUp();
      }
    });

    // outside click event for tags filter
    $(document).on('click', (e) => {
      if (!e.target.className.includes('search-single-select')) {
        $('.search-single-select').removeClass('search-single-select--expand');
        $('.search-single-select__options').slideUp();
      }
    });
  },
};

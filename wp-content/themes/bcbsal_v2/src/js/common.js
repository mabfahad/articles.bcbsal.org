import {
  debounce, isMobile, isEmail, initMktoForm,
} from './utils/helper';

export default {
  finalize() {
    /**
     * Site header menu interactions
     */
    function closePopularArticlesMenu() {
      $('.site-header-slide-menu-content-categories .menu-item').removeClass('menu-item--is-active');
      $('.site-header-slide-menu-popular').removeClass('site-header-slide-menu-popular--is-open');
      $('.site-header-slide-menu-popular-articles').removeClass('site-header-slide-menu-popular-articles--is-active');
    }

    // hamburger menu events
    $(document).on('click', '.site-header-hamburger', function() {
      $(this).toggleClass('site-header-hamburger--is-open');

      const btnSearch = $('.site-header-button-search');
      if (btnSearch.attr('style')) {
        btnSearch.removeAttr('style');
      } else {
        btnSearch.css({ 'margin-right': '2.88rem' });
      }
      $('.site-header-slide-menu').toggleClass('site-header-slide-menu--is-open');
      $('.site-header-slide-menu-popular').toggleClass('site-header-slide-menu-popular--is-enable');
      closePopularArticlesMenu();
      $('.site-header').toggleClass('site-header--backdrop');
    });

    // slide menu popular articles events
    $(document).on('mouseenter', '.site-header-slide-menu-content-categories li.menu-item', function() {
      $('.site-header-slide-menu-content-categories .menu-item').removeClass('menu-item--is-active');
      $(this).addClass('menu-item--is-active');

      $('.site-header-slide-menu-popular').addClass('site-header-slide-menu-popular--is-open');
      $('.site-header-slide-menu-popular-articles').removeClass('site-header-slide-menu-popular-articles--is-active');
      const menuItemId = $(this).attr('class').match(/menu-item-[\d]+/i)[0];
      $(`.site-header-slide-menu-popular-articles.${menuItemId}`).addClass('site-header-slide-menu-popular-articles--is-active');
    });

    // // popular articles slide menu on/off events
    $(document).on('mouseover', '.site-header.site-header--backdrop', (e) => {
      if (
        !e.target.className.includes('site-header-slide-menu--is-open')
        && e.target.className !== 'site-header-slide-menu-content'
        && !e.target.className.includes('site-header-slide-menu-content-categories')
        && !e.target.className.includes('site-header-slide-menu-popular')
        && !e.target.className.includes('menu-item-object-category')
        && e.target.tagName.toLowerCase() !== 'a'
      ) {
        closePopularArticlesMenu();
      }
    });

    $(document).on('click', '.site-header.site-header--backdrop', (e) => {
      if (
        !e.target.className.includes('site-header-slide-menu--is-open')
        && e.target.className !== 'site-header-slide-menu-content'
        && !e.target.className.includes('site-header-slide-menu-content-categories')
        && !e.target.className.includes('site-header-slide-menu-popular')
        && !e.target.className.includes('menu-item-object-category')
        && e.target.tagName.toLowerCase() !== 'a'
      ) {
        $('.site-header-hamburger').toggleClass('site-header-hamburger--is-open');
        $('.site-header-button-search').removeAttr('style');
        $('.site-header-slide-menu').toggleClass('site-header-slide-menu--is-open');
        $('.site-header-slide-menu-popular').toggleClass('site-header-slide-menu-popular--is-enable');
        $('.site-header').toggleClass('site-header--backdrop');
      }
    });

    // site header on scroll
    let lastScrolledPos = 0;
    $(window).on('scroll', () => {
      const currentScrollPos = $(window).scrollTop();
      const siteHeader = $('.site-header');
      if (currentScrollPos < lastScrolledPos) {
        siteHeader.addClass('site-header--slide-down');
        siteHeader.removeClass('site-header--slide-up');
      } else {
        siteHeader.addClass('site-header--slide-up');
        siteHeader.removeClass('site-header--slide-down');
      }

      if (currentScrollPos > 65) {
        siteHeader.addClass('site-header--border-bottom-fluid');
      } else {
        siteHeader.removeClass('site-header--border-bottom-fluid');
      }

      lastScrolledPos = currentScrollPos;
    });

    // card with no meta expand/collapse
    $('body').on('click', '.card-no-meta__arrow', function() {
      $(this).parents('.card-no-meta__content').find('.card-no-meta__deck').slideToggle();
      $(this).toggleClass('card-no-meta__arrow--collapse');
    });

    /**
     * Search modal specific snippets
     */
    $('body').on('click', '.search-modal__taxonomies-taxonomy', function() {
      if (isMobile()) {
        $(this).find('ul').slideToggle();
        $(this).find('.search-modal__taxonomies-taxonomy-arrow').toggleClass('search-modal__taxonomies-taxonomy-arrow--expand');

        const siblings = $(this).siblings();
        siblings.find('ul').slideUp();
        siblings.find('.search-modal__taxonomies-taxonomy-arrow').removeClass('search-modal__taxonomies-taxonomy-arrow--expand');
      }
    });

    $(window).resize(() => {
      if (!isMobile()) {
        $('.search-modal__taxonomies-taxonomy').each(function() {
          $(this).find('ul').removeAttr('style');
        });

        $('.search-modal__taxonomies-taxonomy-arrow').removeClass('search-modal__taxonomies-taxonomy-arrow--expand');
      }
    });

    $('.site-header-button-search, .page-404-header__buttons-search').on('click', () => {
      $('body, html').addClass('nscroll');
      $('.search-modal').show();
    });

    const searchModalReset = () => {
      $('.search-modal__results-articles').html('').addClass('dnone');
      $('.search-modal__results-post-types-podcasts').html('').addClass('dnone');
      $('.search-modal__results-post-types-case-studies').html('').addClass('dnone');
      $('.search-modal__results-post-types-tools').html('').addClass('dnone');
      $('.search-modal__taxonomies').show();
    };

    $('.search-modal__close').on('click', () => {
      $('body, html').removeClass('nscroll');
      $('.search-modal').hide();
      $('.search-modal__form-input--main').val('');
      $('.search-modal__form-input--autocomplete').val('');
      $('.search-modal__results-for--keywords').text('');
      $('.search-modal__results-for').hide();

      searchModalReset();
    });

    /**
     * Ajax based search with autocomplete/autosuggestions
     *
     * @package square
     * @since 1.0.0
     */

    /**
     * Convert the character case
     * @param comp
     * @param char
     * @returns {string|*}
     */
    const convertCharCase = (comp, char) => {
      if (char) {
        if (comp === char.toUpperCase()) {
          return char.toUpperCase();
        }
        if (comp === char.toLowerCase()) {
          return char.toLowerCase();
        }
      }

      return char;
    };

    /**
     * Convert the case of the string
     * @param typed
     * @param result
     * @returns {*}
     */
    const getMatchingString = (typed, result) => {
      let i = 0;
      const caseSensitveResult = [...result];
      while (i <= typed.length) {
        caseSensitveResult[i] = convertCharCase(typed[i], result[i]);
        i += 1;
      }
      return caseSensitveResult.join('');
    };

    // check if user edit the main text
    let modalKeypressCount = 0;
    $('.search-modal__form-input--main').keyup((e) => {
      const mainInput = $('.search-modal__form-input--main');
      const autoCompleteInput = $('.search-modal__form-input--autocomplete');

      // reset the suggestion if user backspace
      const keywords = e.target.value.trim();
      if (modalKeypressCount > mainInput.val().length) {
        autoCompleteInput.val('');
      }

      modalKeypressCount = mainInput.val().length;

      if (keywords === '') {
        // Show placeholder text if input is empty.
        mainInput.val('');
        autoCompleteInput.val('');
        searchModalReset();
      }
    });

    let searchModalKeywords = '';
    $('.search-modal__form-input--main').keyup(debounce((e) => {
      const searchInput = $('.search-modal__form-input--main');
      const autocompleteInput = $('.search-modal__form-input--autocomplete');
      const searchResultsFor = $('.search-modal__results-for');
      const searchResultsForKeywords = $('.search-modal__results-for--keywords');
      const keywords = e.target.value.trim();

      if (keywords === '') {
        // Show placeholder text if input is empty.
        searchResultsForKeywords.text('');
        searchResultsFor.hide();
        searchModalReset();
      }

      // probable special characters
      const symbols = ['!', '”', '#', '$', '%', '&', '’', '(', ')', '*', '+', ',', '-', '.', '/', ':', ';', '<', '=', '>', '?', '@', '[', '/', '`', '{', '|', '}', '~', ']', '^', '_'];

      if (keywords?.length > 0) {
        const keywordsChars = [...keywords];

        // check number of special character used
        let specialCharFound = 0;
        $.each(keywordsChars, (key, value) => {
          const i = $.inArray(value, symbols);
          if (i !== -1) {
            specialCharFound += 1;
          }
        });

        if (specialCharFound >= 5) {
          searchInput.val('');
          searchResultsForKeywords.text('');
          searchResultsFor.hide();
          return;
        }

        if (keywordsChars.length > 2 && keywords !== searchModalKeywords) {
          const postData = {
            action: 'SEARCH_MODAL_AUTOCOMPLETE',
            keywords,
            // eslint-disable-next-line no-undef
            security: surge.search_modal_autocomplete_nonce,
          };

          // eslint-disable-next-line no-undef
          $.post(surge.ajax_url, postData, (data) => {
            // Keyword suggestion.
            const response = $.parseJSON(data);
            if (response.articles || response.podcasts || response.case_studies || response.tools) {
              $('.search-modal__taxonomies').hide();
            }

            if (response.articles?.length > 0) {
              $('.search-modal__results-articles').html(response.articles).removeClass('dnone');
            } else {
              $('.search-modal__results-articles').addClass('dnone');
            }
            if (response.podcasts?.length > 0) {
              $('.search-modal__results-post-types-podcasts').html(response.podcasts).removeClass('dnone');
            } else {
              $('.search-modal__results-post-types-podcasts').addClass('dnone');
            }

            if (response.case_studies?.length > 0) {
              $('.search-modal__results-post-types-case-studies').html(response.case_studies).removeClass('dnone');
            } else {
              $('.search-modal__results-post-types-case-studies').addClass('dnone');
            }

            if (response.tools?.length > 0) {
              $('.search-modal__results-post-types-tools').html(response.tools).removeClass('dnone');
            } else {
              $('.search-modal__results-post-types-tools').addClass('dnone');
            }

            autocompleteInput.val(getMatchingString(searchInput.val(), response.search_for));
            searchResultsForKeywords.text(searchInput.val() || response.search_for);
            searchResultsFor.show();
          });
          searchModalKeywords = keywords;
        }

        // Perform autocomplete on right arrow key press.
        if (e.key === 'ArrowRight') {
          searchInput.val(autocompleteInput.val());
          searchResultsForKeywords.text(searchInput.val());
          searchResultsFor.show();
        }
      }
    }, 1000));

    // check if user edit the main text
    let formKeypressCount = 0;
    $('.search-form__input--main').keyup((e) => {
      const mainInput = $('.search-form__input--main');
      const autoCompleteInput = $('.search-form__input--autocomplete');

      // reset the suggestion if user backspace
      const keywords = e.target.value.trim();
      if (formKeypressCount > mainInput.val().length) {
        autoCompleteInput.val('');
      }
      formKeypressCount = mainInput.val().length;

      if (keywords === '') {
        // Show placeholder text if input is empty.
        mainInput.val('');
        autoCompleteInput.val('');
      }
    });

    let searchKeywords = '';
    $('.search-form__input--main').keyup(debounce((e) => {
      const searchInput = $('.search-form__input--main');
      const autocompleteInput = $('.search-form__input--autocomplete');

      const keywords = e.target.value.trim();

      if (keywords === '') {
        // Show placeholder text if input is empty.
        searchInput.val('');
        autocompleteInput.val('');
      }

      // probable special characters
      const symbols = ['!', '”', '#', '$', '%', '&', '’', '(', ')', '*', '+', ',', '-', '.', '/', ':', ';', '<', '=', '>', '?', '@', '[', '/', '`', '{', '|', '}', '~', ']', '^', '_'];

      if (keywords?.length > 0) {
        const keywordsChars = [...keywords];

        // check number of special character used
        let specialCharFound = 0;
        $.each(keywordsChars, (key, value) => {
          const i = $.inArray(value, symbols);
          if (i !== -1) {
            specialCharFound += 1;
          }
        });

        if (specialCharFound >= 5) {
          searchInput.val('');
          return;
        }

        if (keywordsChars.length > 2 && keywords !== searchKeywords) {
          const postData = {
            action: 'SEARCH_PAGE_AUTOCOMPLETE',
            keywords,
            // eslint-disable-next-line no-undef
            security: surge.search_page_autocomplete_nonce,
          };

          // eslint-disable-next-line no-undef
          $.post(surge.ajax_url, postData, (data) => {
            // Keyword suggestion.
            const response = $.parseJSON(data);
            autocompleteInput.val(getMatchingString(searchInput.val(), response.search_for));
          });
          searchKeywords = keywords;
        }

        // Perform autocomplete on right arrow key press.
        if (e.key === 'ArrowRight') {
          searchInput.val(autocompleteInput.val());
        }
      }
    }, 1000));

    /**
     * Newsletter forms
     */
    initMktoForm(9716); // newsletter section
    initMktoForm(7927); // newsletter footer

    // email validation
    $('.newsletter-email__form-input').keyup((e) => {
      if (isEmail(e.target.value)) {
        $('.newsletter-email__form-msg')
          .removeAttr('style')
          .text('').fadeOut();
      }
    });

    $('form.newsletter-email__form').on('submit', function(e) {
      e.preventDefault();
      // get email value
      const emailInput = $(this).find('.newsletter-email__form-input');
      const emailMsg = $(this).parent().find('.newsletter-email__form-msg');
      const submitButton = $(this).find('.newsletter-email__form-button');
      const submitSuccess = $(this).find('.newsletter-email__form-success');
      const email = emailInput.val();

      if (isEmail(email)) {
        submitButton.text('Please Wait');
        submitButton.addClass('nclick');
        // eslint-disable-next-line no-undef
        const form = MktoForms2.getForm($(this).data('form-id'));
        form.vals({ Email: email });
        form.submit();
        form.onSuccess((values, followUpUrl) => {
          emailMsg.text('Thanks for subscribing!');
          submitButton.fadeOut('fast');
          submitSuccess.fadeIn();
          emailMsg.fadeIn();

          // reset the form
          setTimeout(() => {
            emailInput.val('');
            emailMsg.text('');
            emailMsg.fadeOut();
            submitButton.fadeIn();
            submitSuccess.fadeOut();
            submitButton.text('Subscribe');
            submitButton.removeClass('nclick');
          }, 5000);

          // Return false to prevent the follow-up url
          return false;
        });
      } else {
        emailMsg.text('Enter a valid email.')
          .css({ color: '#bf0020' })
          .fadeIn();
      }
    });

    // get started button hover
    $('.site-header-button-get-started').hover(
      () => {
        $('.get-started--header').slideDown('fast');
      },
      () => {
        $('.get-started--header').slideUp('fast');
      },
    );

    // Tools popup modal
    /* button enable disable */
    $(".tool-modal__form input[name='optin']").on('click', () => {
      const isChecked = $(".tool-modal__form input[name='optin']").prop('checked');
      if (isChecked) {
        $('.tools-download-button').removeClass('tools-download-button--disabled');
      } else {
        $('.tools-download-button').addClass('tools-download-button--disabled');
      }
    });

    let isBackdropEnabled = false;
    const toggleBackdrop = () => {
      if (isBackdropEnabled) {
        $('.backdrop').remove();
        isBackdropEnabled = false;
      } else {
        $('body').append('<div class="backdrop"></div>');
        isBackdropEnabled = true;
      }

      $('body, html').toggleClass('nscroll');
    };

    // open tool modal
    $('.show-tool-modal').on('click', function() {
      if ($('body').hasClass('category-tools')) {
        // replace the current entry in the browser's history, without reloading
        const pageName = $(this).data('slug');
        // eslint-disable-next-line no-undef
        window.history.replaceState(null, $(document).find('title').text(), `${surge.base_url}/tools/?page=${pageName}`);
      }
      $('.tool-modal').addClass('active');
      toggleBackdrop();
    });

    // close tools modal handler
    const closeToolModal = () => {
      $('.tool-modal').removeClass('active tool-modal--show-thank-you');
      $('.tool-modal input').val('');
      $('.tool-modal select option:first').prop('selected', true);
      $(".tool-modal__form input[name='optin']").prop('checked', false);
      $('.tool-modal__err').addClass('tool-modal__err--none');
      toggleBackdrop();

      if ($('body').hasClass('category-tools')) {
        // eslint-disable-next-line no-undef
        window.history.replaceState(null, $(document).find('title').text(), `${surge.base_url}/tools/`);
      }
    };

    // close tool modal
    $('.tool-modal__close').on('click', () => {
      closeToolModal();
    });

    // close tools modal if click outside
    $(document).on('click', (e) => {
      if (e.target.className.includes('backdrop')) {
        closeToolModal();
      }
    });

    const hasValidData = (key) => {
      const value = $(`#${key}`).val();

      if (!value.length) {
        const parent = $(`#${key}`).parents('.col');
        parent.find('.form-field').addClass('form-field--err');
        parent.find('.tool-modal__err').removeClass('tool-modal__err--none');
      } else {
        const parent = $(`#${key}`).parents('.col');
        parent.find('.form-field').removeClass('form-field--err');
        parent.find('.tool-modal__err').addClass('tool-modal__err--none');
      }
    };

    // tool marketo form
    initMktoForm(5764);
    setTimeout(() => {
      $('.tool-modal__form #revenue').html($('#Revenue_Range__c_account').html());
    }, 2000);

    $('.tool-modal__form').on('submit', function(e) {
      e.preventDefault();

      // mapping fields
      // FirstName LastName Email Phone Company Revenue_Range__c_account
      const fname = $('#fname').val();
      const lname = $('#lname').val();
      const email = $('#email').val();
      const phone = $('#phone').val();
      const company = $('#company').val();
      const revenue = $('#revenue option:selected').val();

      // data validation
      hasValidData('fname');
      hasValidData('lname');
      hasValidData('phone');
      hasValidData('company');

      // email
      if (!isEmail(email)) {
        const parent = $('#email').parents('.col');
        parent.find('.form-field').addClass('form-field--err');
        parent.find('.tool-modal__err').removeClass('tool-modal__err--none');
      } else {
        const parent = $('#email').parents('.col');
        parent.find('.form-field').removeClass('form-field--err');
        parent.find('.tool-modal__err').addClass('tool-modal__err--none');
      }

      // revenue
      if (!revenue.length) {
        const parent = $('#revenue').parents('.col');
        parent.find('.form-field').addClass('form-field--err');
        parent.find('.tool-modal__err').removeClass('tool-modal__err--none');
      } else {
        const parent = $('#revenue').parents('.col');
        parent.find('.form-field').removeClass('form-field--err');
        parent.find('.tool-modal__err').addClass('tool-modal__err--none');
      }

      if (fname && lname && email && isEmail(email) && phone && company && revenue) {
        $('.tools-download-button').addClass('nclick');
        $('.tools-download-button__content span').text('Please Wait');
        // eslint-disable-next-line no-undef
        const form = MktoForms2.getForm($(this).data('form-id'));
        form.vals({
          FirstName: fname,
          LastName: lname,
          Email: email,
          Phone: phone,
          Company: company,
          Revenue_Range__c_account: revenue,
        });
        form.submit();
        form.onSuccess((values, followUpUrl) => {
          $('.tool-modal').addClass('tool-modal--show-thank-you');
          $('.tools-download-button').removeClass('nclick');
          $('.tools-download-button__content span').text('Download');
          // Return false to prevent the follow-up url
          return false;
        });
      }
    });
  },
};

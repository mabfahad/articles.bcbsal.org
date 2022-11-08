import 'slick-carousel';

/**
 * Square Data set
 */
export const sqDataSet = () => {
  // eslint-disable-next-line no-undef, no-underscore-dangle
  return $.parseJSON(surge.__SQ_DATA__);
};

/**
 * Initialize slick slider
 *
 * @param element
 */
export const initSlick = (element) => {
  $(window).on('load resize orientationchange', () => {
    const carousel = $(element);
    if ($(window).width() > 768) {
      if (carousel.hasClass('slick-initialized')) {
        carousel.slick('unslick');
      }
    } else if (!carousel.hasClass('slick-initialized')) {
      carousel.slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
      });
    }
  });
};

/**
 * Validate search taxonomies with backend data
 *
 * @param tax
 * @param type
 * @returns {boolean|*}
 */
export const taxValidation = (tax, type) => {
  const sqData = sqDataSet();

  if (type === 'cat') {
    return sqData.taxonomies.categories.includes(tax);
  }
  if (type === 'ct') {
    return sqData.taxonomies.content_types.includes(tax);
  }
  if (type === 't') {
    return sqData.taxonomies.topics.includes(tax);
  }
  if (type === 'ind') {
    return sqData.taxonomies.industries.includes(tax);
  }
  if (type === 'soln') {
    return sqData.taxonomies.square_solutions.includes(tax);
  }
  if (type === 'bt') {
    return sqData.taxonomies.business_types.includes(tax);
  }
  return false;
};

/**
 * Execute a function given a delay time
 *
 * @param func
 * @param wait
 * @param immediate
 * @returns {(function(): void)|*}
 */
export const debounce = function(func, wait, immediate = false) {
  let timeout;
  return function() {
    const context = this;
    const args = arguments; // eslint-disable-line prefer-rest-params
    const later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};

/**
 * Check if the current size is mobile
 *
 * @returns {boolean}
 */
export const isMobile = () => {
  const SCREEN_WIDTH = window.innerWidth
    || document.documentElement.clientWidth
    || document.body.clientWidth;

  return SCREEN_WIDTH < 768;
};

/**
 * Set the cookie
 *
 * @param cname
 * @param cvalue
 * @param expiryDays
 */
export const setCookie = (cname, cvalue, expiryDays) => {
  const d = new Date();
  d.setTime(d.getTime() + expiryDays * 24 * 60 * 60 * 1000);
  const expires = `expires=${d.toUTCString()}`;
  document.cookie = `${cname}=${cvalue};${expires};path=/`;
};

/**
 * Get the cookie
 *
 * @param cname
 * @returns {string}
 */
export const getCookie = (cname) => {
  const name = `${cname}=`;
  const decodedCookie = decodeURIComponent(document.cookie);
  const ca = decodedCookie.split(';');
  for (let i = 0; i < ca.length; i += 1) {
    let c = ca[i];
    while (c.charAt(0) === ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) === 0) {
      return c.substring(name.length, c.length);
    }
  }
  return '';
};

/**
 * Get the OneTrust consent groups from cookie value of OptanonConsent
 * @returns {string}
 */
export const getOneTrustActiveGroupFromCookie = () => {
  // extract consent group from cookie `OptanonConsent`
  // &groups=C0001:1,C0002:1,C0003:1,C0004:0
  const optanonConsentCookieString = getCookie('OptanonConsent');
  if (optanonConsentCookieString === undefined) {
    return 'no consent data';
  }
  const cookieDataAsArray = optanonConsentCookieString.split('&');
  const groupCookieDataString = cookieDataAsArray.find((dataString) => {
    return dataString.includes('groups=');
  });
  // i.e 'groups=C0001:1,C0002:1,C0003:1,C0004:0'
  if (!groupCookieDataString) {
    return 'no consent data';
  }

  const groupPreferencesStringAsArray = groupCookieDataString
    .split('=')[1]
    .split(',');
  // ['C0001:1','C0002:1','C0003:1','C0004:0']
  const oneTrustActiveGroups = [];
  const len = groupPreferencesStringAsArray.length;
  // eslint-disable-next-line no-plusplus
  for (let i = 0; i < len; i++) {
    const [groupId, consentState] = groupPreferencesStringAsArray[i].split(
      ':',
    );
    // eslint-disable-next-line radix
    if (parseInt(consentState) === 1) {
      oneTrustActiveGroups.push(groupId);
    }
  }
  return oneTrustActiveGroups.join(',');
};

/**
 * Generate uuid
 * @returns {string}
 */
export const generateUUID = () => {
  // UUID generator
  // RFC4122v4 compliant uuid generator
  const chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
  const uuid = new Array(36);
  let rnd = 0;
  let r;
  // eslint-disable-next-line no-plusplus
  for (let i = 0; i < 36; i++) {
    if (i === 8 || i === 13 || i === 18 || i === 23) {
      uuid[i] = '-';
    } else if (i === 14) {
      uuid[i] = '4';
    } else {
      if (rnd <= 0x02) { rnd = Math.trunc(0x2000000 + (Math.random() * 0x1000000)); }
      // eslint-disable-next-line no-bitwise
      r = rnd & 0xf;
      // eslint-disable-next-line no-bitwise
      rnd >>= 4;
      // eslint-disable-next-line no-bitwise
      uuid[i] = chars[(i === 19) ? (r & 0x3) | 0x8 : r];
    }
  }
  return uuid.join('');
};

/* Submit tool modal form */
export const isEmail = (email) => {
  // eslint-disable-next-line no-useless-escape
  const regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
};

/* Initialize Marketo Form */
export const initMktoForm = (formId) => {
  // eslint-disable-next-line no-undef
  MktoForms2.loadForm('//www.workwithsquare.com', '424-IAB-218', formId);
};

import Router from './utils/Router';
import common from './common';

const routes = new Router({
  common,
});
(function($) {
  routes.loadEvents();
}(jQuery));

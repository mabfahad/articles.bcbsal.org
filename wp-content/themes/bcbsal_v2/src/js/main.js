import Router from './utils/Router';
import analytics from './analytics';
import common from './common';
import home from './home';
import single from './single';
import page from './page';
import category from './category';
import tag from './tag';
import search from './search';
import author from './author';

const routes = new Router({
  analytics,
  common,
  home,
  single,
  page,
  category,
  tag,
  author,
  search,
});
(function($) {
  routes.loadEvents();
}(jQuery));

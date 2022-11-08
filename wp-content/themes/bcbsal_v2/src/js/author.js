export default {
  finalize() {
    let offset = $('.author-posts').data('offset');
    const authorId = $('.author-posts').data('author-id');
    const total = $('.author-posts').data('total');

    $('body').on('click', '.author-load-more__button', () => {
      // set for ajax request
      const postData = {
        offset,
        author_id: authorId,
        action: 'LOAD_MORE_AUTHOR_POSTS',
        // eslint-disable-next-line no-undef
        security: surge.author_load_more_nonce,
      };

      // make ajax request
      // eslint-disable-next-line no-undef
      $.post(surge.ajax_url, postData, (response) => {
        $('.author-posts').append(response);

        // update loaded posts count
        offset += 4;
      });

      // check if more posts available
      $('body').on('DOMSubtreeModified', '.author-posts', () => {
        let postsCounter = 0;

        // get total number of loaded posts
        $('.author-posts .author-posts__post').each(() => {
          postsCounter += 1;
        });

        // if all the posts loaded hide the load more button to
        if (postsCounter >= parseInt(total, 10)) {
          $('.author-load-more__button').fadeOut();
        }
      });
    });
  },
};

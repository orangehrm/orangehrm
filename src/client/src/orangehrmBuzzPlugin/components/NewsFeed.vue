<template>
  <div class="orangehrm-buzz-newsfeed">
    <oxd-text type="card-title" class="orangehrm-buzz-newsfeed-title">
      {{ $t('buzz.buzz_newsfeed') }}
    </oxd-text>

    <create-post></create-post>

    <!-- todo: add post filters here -->
    <oxd-grid :cols="1" class="orangehrm-buzz-newsfeed-posts">
      <oxd-grid-item v-for="post in posts" :key="post">
        <base-post
          :post-id="post.id"
          :content="post.text"
          :employee="post.employee"
        ></base-post>
      </oxd-grid-item>
    </oxd-grid>
    <oxd-loading-spinner
      v-if="isLoading"
      class="orangehrm-buzz-newsfeed-loader"
    />
  </div>
</template>

<script>
import {onBeforeMount, reactive, toRefs} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import BasePost from '@/orangehrmBuzzPlugin/components/BasePost.vue';
import CreatePost from '@/orangehrmBuzzPlugin/components/CreatePost.vue';
import useInfiniteScroll from '@/core/util/composable/useInfiniteScroll';

const defaultFilters = {
  priority: 'most_recent', // most_recent | most_likes | most_comments
};

export default {
  name: 'NewsFeed',

  components: {
    'base-post': BasePost,
    'create-post': CreatePost,
    'oxd-loading-spinner': Spinner,
  },

  setup() {
    const POST_LIMIT = 10;
    const http = new APIService(window.appGlobal.baseUrl, 'api/v2/buzz/posts');

    const state = reactive({
      total: 0,
      offset: 0,
      posts: [],
      filters: {
        ...defaultFilters,
      },
      isLoading: false,
    });

    const fetchData = () => {
      state.isLoading = true;
      http
        .getAll({
          limit: POST_LIMIT,
          offset: state.offset,
          priority: state.filters.priority,
        })
        .then(response => {
          const {data, meta} = response.data;
          state.total = meta?.total || 0;
          if (Array.isArray(data)) {
            state.posts = [...state.posts, ...data];
          }
        })
        .finally(() => (state.isLoading = false));
    };

    useInfiniteScroll(() => {
      if (state.posts.length >= state.total) return;
      state.offset += POST_LIMIT;
      fetchData();
    });

    onBeforeMount(() => fetchData());

    return {
      fetchData,
      ...toRefs(state),
    };
  },
};
</script>

<style src="./news-feed.scss" lang="scss" scoped></style>

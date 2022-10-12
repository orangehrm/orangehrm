<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
 -->

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
        >
          <template #actionButton>
            <post-actions></post-actions>
          </template>
          <template #postStatus>
            <post-status
              :post-id="postIds"
              :is-mobile="isMobile"
              :no-of-likes="1"
              :no-of-shares="1"
              :no-of-comments="noOfComments"
            ></post-status>
          </template>
        </base-post>
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
import PostStatus from '@/orangehrmBuzzPlugin/components/PostStatus.vue';
import PostActions from '@/orangehrmBuzzPlugin/components/PostActions.vue';

const defaultFilters = {
  priority: 'most_recent', // most_recent | most_likes | most_comments
};

export default {
  name: 'NewsFeed',

  components: {
    'base-post': BasePost,
    'create-post': CreatePost,
    'oxd-loading-spinner': Spinner,
    'post-status': PostStatus,
    'post-actions': PostActions,
  },

  props: {
    isMobile: {
      type: Boolean,
      default: false,
    },
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

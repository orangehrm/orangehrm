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

    <create-post :employee="employee"></create-post>
    <post-filters
      :mobile="mobile"
      :filter="filters.sortField"
      @updatePriority="onUpdatePriority"
    ></post-filters>

    <oxd-grid :cols="1" class="orangehrm-buzz-newsfeed-posts">
      <oxd-grid-item v-for="(post, index) in posts" :key="post">
        <post-container :post="post">
          <template #content>
            <post-body
              :post="post"
              @selectPhoto="onSelectPhoto($event, index)"
            ></post-body>
          </template>
          <template #actionButton>
            <post-actions
              :like="post.liked"
              @like="onLike(index)"
              @share="onShare(index)"
              @comment="onComment(index)"
            ></post-actions>
          </template>
          <template #postStats>
            <post-stats
              :mobile="mobile"
              :post-id="post.id"
              :no-of-likes="post.stats.numOfLikes"
              :no-of-shares="post.stats.numOfShares"
              :no-of-comments="post.stats.numOfComments"
            ></post-stats>
          </template>
          <!-- TODO: Add Post Comment Component -->
        </post-container>
      </oxd-grid-item>
    </oxd-grid>
    <oxd-loading-spinner
      v-if="isLoading"
      class="orangehrm-buzz-newsfeed-loader"
    />
  </div>
  <share-post-modal
    v-if="showShareModal"
    :employee="employee"
    :data="shareModalState"
    @close="onCloseShareModal"
  ></share-post-modal>
  <photo-carousel
    v-if="showPhotoCarousel"
    :photo-index="0"
    :mobile="mobile"
    :post="photoCarouselState"
    @close="onClosePhotoCarousel"
  ></photo-carousel>
</template>

<script>
import {onBeforeMount, reactive, toRefs} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import PostBody from '@/orangehrmBuzzPlugin/components/PostBody.vue';
import PostStats from '@/orangehrmBuzzPlugin/components/PostStats.vue';
import CreatePost from '@/orangehrmBuzzPlugin/components/CreatePost.vue';
import useInfiniteScroll from '@/core/util/composable/useInfiniteScroll';
import PostActions from '@/orangehrmBuzzPlugin/components/PostActions.vue';
import PostFilters from '@/orangehrmBuzzPlugin/components/PostFilters.vue';
import PhotoCarousel from '@/orangehrmBuzzPlugin/components/PhotoCarousel.vue';
import PostContainer from '@/orangehrmBuzzPlugin/components/PostContainer.vue';
import SharePostModal from '@/orangehrmBuzzPlugin/components/SharePostModal.vue';

const defaultFilters = {
  sortOrder: 'DESC',
  sortField: 'share.createdAtUtc',
};

export default {
  name: 'NewsFeed',

  components: {
    'post-body': PostBody,
    'post-stats': PostStats,
    'create-post': CreatePost,
    'post-actions': PostActions,
    'post-filters': PostFilters,
    'oxd-loading-spinner': Spinner,
    'photo-carousel': PhotoCarousel,
    'post-container': PostContainer,
    'share-post-modal': SharePostModal,
  },

  props: {
    employee: {
      type: Object,
      required: true,
    },
    mobile: {
      type: Boolean,
      default: false,
    },
  },

  setup() {
    const POST_LIMIT = 10;
    const http = new APIService(window.appGlobal.baseUrl, 'api/v2/buzz/feed');

    const state = reactive({
      total: 0,
      offset: 0,
      posts: [],
      filters: {
        ...defaultFilters,
      },
      isLoading: false,
      showShareModal: false,
      shareModalState: null,
      showPhotoCarousel: false,
      photoCarouselState: null,
    });

    const fetchData = () => {
      state.isLoading = true;
      http
        .getAll({
          limit: POST_LIMIT,
          offset: state.offset,
          sortOrder: state.filters.sortOrder,
          sortField: state.filters.sortField,
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

    const onUpdatePriority = $event => {
      if ($event) {
        state.posts = [];
        state.offset = 0;
        state.filters.sortField = $event;
        fetchData();
      }
    };

    const onLike = index => {
      http
        .update(state.posts[index].id, {
          like: !state.posts[index].liked,
        })
        .then(() => {
          state.posts[index].liked = !state.posts[index].liked;
          // todo - update like count etc
        });
    };

    const onShare = index => {
      state.showShareModal = true;
      state.shareModalState = state.posts[index];
      document.body.style.overflow = 'hidden';
    };

    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const onComment = index => {
      // todo
    };

    const resetFeed = () => {
      state.posts = [];
      state.offset = 0;
      state.filters = [...defaultFilters];
      fetchData();
    };

    const onSelectPhoto = ($event, index) => {
      state.photoCarouselState = state.posts[index];
      state.showPhotoCarousel = true;
      document.body.style.overflow = 'hidden';
    };

    const onClosePhotoCarousel = () => {
      state.showPhotoCarousel = false;
      state.photoCarouselState = null;
      document.body.style.overflow = 'auto';
    };

    const onCloseShareModal = $event => {
      state.showShareModal = false;
      state.shareModalState = null;
      document.body.style.overflow = 'auto';
      if ($event) resetFeed();
    };

    onBeforeMount(() => fetchData());

    return {
      onLike,
      onShare,
      onComment,
      fetchData,
      onSelectPhoto,
      onUpdatePriority,
      onCloseShareModal,
      onClosePhotoCarousel,
      ...toRefs(state),
    };
  },
};
</script>

<style src="./news-feed.scss" lang="scss" scoped></style>

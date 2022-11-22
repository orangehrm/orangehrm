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
              :post="post"
              @like="onLike(index)"
              @share="onShare(index)"
              @comment="onComment(index)"
            ></post-actions>
          </template>
          <template #postStats>
            <post-stats
              :post="post"
              :mobile="mobile"
              @comment="onComment(index)"
            ></post-stats>
          </template>
          <template v-if="post.showComments" #comments>
            <oxd-divider />
            <post-comment-container
              :post-id="post.id"
              :employee="employee"
            ></post-comment-container>
          </template>
        </post-container>
      </oxd-grid-item>

      <oxd-grid-item
        v-show="!isLoading && posts.length === 0"
        class="orangehrm-buzz-newsfeed-noposts"
      >
        <img :src="noPostsPic" alt="No Posts" />
        <oxd-text tag="p">
          {{ $t('buzz.no_posts_available') }}
        </oxd-text>
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
import useBuzzAPIs from '@/orangehrmBuzzPlugin/util/composable/useBuzzAPIs';
import PhotoCarousel from '@/orangehrmBuzzPlugin/components/PhotoCarousel.vue';
import PostContainer from '@/orangehrmBuzzPlugin/components/PostContainer.vue';
import SharePostModal from '@/orangehrmBuzzPlugin/components/SharePostModal.vue';
import PostCommentContainer from '@/orangehrmBuzzPlugin/components/PostCommentContainer.vue';

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
    'post-comment-container': PostCommentContainer,
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
    const {fetchPosts} = useBuzzAPIs(
      new APIService(window.appGlobal.baseUrl, ''),
    );
    const noPostsPic = `${window.appGlobal.baseUrl}/../images/buzz_no_posts.svg`;

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
      fetchPosts(
        POST_LIMIT,
        state.offset,
        state.filters.sortOrder,
        state.filters.sortField,
      )
        .then(response => {
          const {data, meta} = response.data;
          state.total = meta.total || 0;
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
      state.posts[index].liked = !state.posts[index].liked;
    };

    const onShare = index => {
      state.showShareModal = true;
      state.shareModalState = state.posts[index];
      document.body.style.overflow = 'hidden';
    };

    const onComment = index => {
      if (state.posts[index].showComments) {
        state.posts[index].showComments = false;
      } else {
        state.posts[index].showComments = true;
      }
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
      noPostsPic,
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

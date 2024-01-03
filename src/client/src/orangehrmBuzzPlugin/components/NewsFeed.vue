<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
 -->

<template>
  <div class="orangehrm-buzz-newsfeed">
    <oxd-text type="card-title" class="orangehrm-buzz-newsfeed-title">
      {{ $t('buzz.buzz_newsfeed') }}
    </oxd-text>

    <create-post
      :key="posts"
      :employee="employee"
      @refresh="resetFeed"
    ></create-post>
    <slot></slot>

    <oxd-grid :cols="1" class="orangehrm-buzz-newsfeed-posts">
      <oxd-grid-item v-for="(post, index) in posts" :key="post">
        <post-container
          :post="post"
          @edit="onEdit(index)"
          @delete="onDelete(index)"
        >
          <template #content>
            <post-body
              :post="post"
              @select-photo="onSelectPhoto($event, index)"
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
              @create="onCreateComment(index)"
              @delete="onDeleteComment(index)"
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

  <edit-post-modal
    v-if="showEditModal"
    :employee="employee"
    :data="editModalState.post"
    @close="onCloseEditModal"
  ></edit-post-modal>
  <share-post-modal
    v-if="showShareModal"
    :employee="employee"
    :data="shareModalState"
    @close="onCloseShareModal"
  ></share-post-modal>
  <photo-carousel
    v-if="showPhotoCarousel"
    :mobile="mobile"
    :post="photoCarouselState.post"
    :photo-index="photoCarouselState.photoIndex"
    @close="onClosePhotoCarousel"
    @like="onLike(photoCarouselState.postIndex)"
    @create-comment="onCreateComment(photoCarouselState.postIndex)"
    @delete-comment="onDeleteComment(photoCarouselState.postIndex)"
  ></photo-carousel>
  <delete-confirmation
    ref="deleteDialog"
    :message="$t('buzz.post_delete_confirmation_message')"
  ></delete-confirmation>
</template>

<script>
import useToast from '@/core/util/composable/useToast';
import {APIService} from '@/core/util/services/api.service';
import {onBeforeMount, reactive, ref, toRefs, watch} from 'vue';
import PostBody from '@/orangehrmBuzzPlugin/components/PostBody.vue';
import PostStats from '@/orangehrmBuzzPlugin/components/PostStats.vue';
import CreatePost from '@/orangehrmBuzzPlugin/components/CreatePost.vue';
import useInfiniteScroll from '@/core/util/composable/useInfiniteScroll';
import PostActions from '@/orangehrmBuzzPlugin/components/PostActions.vue';
import useBuzzAPIs from '@/orangehrmBuzzPlugin/util/composable/useBuzzAPIs';
import EditPostModal from '@/orangehrmBuzzPlugin/components/EditPostModal.vue';
import PhotoCarousel from '@/orangehrmBuzzPlugin/components/PhotoCarousel.vue';
import PostContainer from '@/orangehrmBuzzPlugin/components/PostContainer.vue';
import SharePostModal from '@/orangehrmBuzzPlugin/components/SharePostModal.vue';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import PostCommentContainer from '@/orangehrmBuzzPlugin/components/PostCommentContainer.vue';
import {OxdSpinner} from '@ohrm/oxd';

export default {
  name: 'NewsFeed',

  components: {
    'post-body': PostBody,
    'post-stats': PostStats,
    'create-post': CreatePost,
    'post-actions': PostActions,
    'oxd-loading-spinner': OxdSpinner,
    'photo-carousel': PhotoCarousel,
    'post-container': PostContainer,
    'edit-post-modal': EditPostModal,
    'share-post-modal': SharePostModal,
    'post-comment-container': PostCommentContainer,
    'delete-confirmation': DeleteConfirmationDialog,
  },

  props: {
    employee: {
      type: Object,
      required: true,
    },
    sortField: {
      type: String,
      required: true,
    },
    mobile: {
      type: Boolean,
      default: false,
    },
  },

  setup(props) {
    const POST_LIMIT = 10;
    const deleteDialog = ref();
    const {deleteSuccess} = useToast();
    const {fetchPosts, deletePost} = useBuzzAPIs(
      new APIService(window.appGlobal.baseUrl, ''),
    );
    const noPostsPic = `${window.appGlobal.publicPath}/images/buzz_no_posts.svg`;

    const state = reactive({
      total: 0,
      offset: 0,
      posts: [],
      isLoading: false,
      showEditModal: false,
      editModalState: null,
      showShareModal: false,
      shareModalState: null,
      showPhotoCarousel: false,
      photoCarouselState: null,
    });

    const fetchData = () => {
      state.isLoading = true;
      fetchPosts(POST_LIMIT, state.offset, 'DESC', props.sortField)
        .then((response) => {
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

    const onLike = (index) => {
      state.posts[index].liked = !state.posts[index].liked;
      if (state.posts[index].liked) {
        state.posts[index].stats.numOfLikes++;
      } else {
        state.posts[index].stats.numOfLikes--;
      }
    };

    const onEdit = (index) => {
      state.showEditModal = true;
      state.editModalState = {
        postIndex: index,
        post: state.posts[index],
      };
      document.body.style.overflow = 'hidden';
    };

    const onShare = (index) => {
      state.showShareModal = true;
      state.shareModalState = state.posts[index];
      document.body.style.overflow = 'hidden';
    };

    const onComment = (index) => {
      if (state.posts[index].showComments) {
        state.posts[index].showComments = false;
      } else {
        state.posts[index].showComments = true;
      }
    };

    const resetFeed = () => {
      state.posts = [];
      state.offset = 0;
      fetchData();
    };

    const onSelectPhoto = ($event, index) => {
      state.photoCarouselState = {
        postIndex: index,
        photoIndex: $event,
        post: state.posts[index],
      };
      state.showPhotoCarousel = true;
      document.body.style.overflow = 'hidden';
    };

    const onClosePhotoCarousel = () => {
      state.showPhotoCarousel = false;
      state.photoCarouselState = null;
      document.body.style.overflow = 'auto';
    };

    const onCloseShareModal = ($event) => {
      state.showShareModal = false;
      state.shareModalState = null;
      document.body.style.overflow = 'auto';
      if ($event) resetFeed();
    };

    const onCloseEditModal = ($event) => {
      const {data} = $event;
      if (data) state.posts[state.editModalState.postIndex] = {...data};
      state.showEditModal = false;
      state.editModalState = null;
      document.body.style.overflow = 'auto';
    };

    const onDelete = (index) => {
      deleteDialog.value.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          deletePost(state.posts[index].id).then(() => {
            resetFeed();
            deleteSuccess();
          });
        }
      });
    };

    const onCreateComment = (index) => {
      state.posts[index].stats.numOfComments++;
    };

    const onDeleteComment = (index) => {
      state.posts[index].stats.numOfComments--;
    };

    onBeforeMount(() => fetchData());

    watch(
      () => props.sortField,
      () => {
        state.posts = [];
        state.offset = 0;
        fetchData();
      },
    );

    return {
      onLike,
      onEdit,
      onShare,
      onDelete,
      resetFeed,
      onComment,
      noPostsPic,
      deleteDialog,
      onSelectPhoto,
      onCreateComment,
      onDeleteComment,
      onCloseEditModal,
      onCloseShareModal,
      onClosePhotoCarousel,
      ...toRefs(state),
    };
  },
};
</script>

<style src="./news-feed.scss" lang="scss" scoped></style>

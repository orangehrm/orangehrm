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
  <div class="orangehrm-buzz-comment">
    <div class="orangehrm-buzz-comment-add">
      <profile-image :employee="employee"></profile-image>
      <oxd-form @submit-valid="onSubmit">
        <oxd-input-field
          v-model="text"
          v-autofocus
          :placeholder="$t('buzz.write_your_comment')"
        />
      </oxd-form>
    </div>
    <br v-if="total > 0" />
    <post-comment
      v-for="comment in comments"
      :key="comment"
      :data="comment"
      :post-id="postId"
      @edit="onEditComment"
      @like="onLikeComment"
      @delete="onDeleteComment"
    ></post-comment>
    <oxd-text
      v-if="total > 4"
      tag="p"
      class="orangehrm-buzz-comment-readmore"
      @click="onClickShowMore"
    >
      {{ showAllComments ? $t('general.show_less') : $t('general.show_more') }}
    </oxd-text>
    <delete-confirmation
      ref="deleteDialog"
      :message="$t('buzz.post_delete_confirmation_message')"
    ></delete-confirmation>
  </div>
</template>

<script>
import useToast from '@/core/util/composable/useToast';
import {onBeforeMount, reactive, ref, toRefs} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import useAutoFocus from '@/core/util/composable/useAutoFocus';
import PostComment from '@/orangehrmBuzzPlugin/components/PostComment';
import ProfileImage from '@/orangehrmBuzzPlugin/components/ProfileImage';
import useBuzzAPIs from '@/orangehrmBuzzPlugin/util/composable/useBuzzAPIs';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';

export default {
  name: 'PostCommentContainer',

  components: {
    'post-comment': PostComment,
    'profile-image': ProfileImage,
    'delete-confirmation': DeleteConfirmationDialog,
  },

  directives: {...useAutoFocus()},

  props: {
    postId: {
      type: Number,
      required: true,
    },
    employee: {
      type: Object,
      required: true,
    },
  },

  emits: ['create', 'delete'],

  setup(props, context) {
    const deleteDialog = ref();
    const state = reactive({
      text: null,
      total: 0,
      comments: [],
      showAllComments: false,
    });
    const {saveSuccess, updateSuccess, deleteSuccess} = useToast();
    const {fetchPostComments, savePostComment, deletePostComment} = useBuzzAPIs(
      new APIService(window.appGlobal.baseUrl, ''),
    );

    const loadComments = () => {
      fetchPostComments(props.postId, state.showAllComments ? 0 : 4, true).then(
        (response) => {
          const {data, meta} = response.data;
          state.total = meta.total;
          state.comments = [...data];
        },
      );
    };

    const onSubmit = () => {
      if (!state.text) return;
      savePostComment(props.postId, state.text).then(() => {
        state.text = null;
        loadComments();
        saveSuccess();
        context.emit('create');
      });
    };

    const onClickShowMore = () => {
      state.showAllComments = !state.showAllComments;
      loadComments();
    };

    const onEditComment = () => {
      loadComments();
      updateSuccess();
    };

    const onDeleteComment = (commentId) => {
      deleteDialog.value.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          deletePostComment(props.postId, commentId).then(() => {
            loadComments();
            deleteSuccess();
            context.emit('delete');
          });
        }
      });
    };

    const onLikeComment = () => {
      loadComments();
    };

    onBeforeMount(() => loadComments());

    return {
      onSubmit,
      deleteDialog,
      onLikeComment,
      onEditComment,
      onClickShowMore,
      onDeleteComment,
      ...toRefs(state),
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-comment {
  &-add {
    gap: 10px;
    display: flex;
    form {
      width: 100%;
    }
  }
  &-readmore {
    margin: 0 auto;
    cursor: pointer;
    font-size: 0.9rem;
    text-align: center;
    color: $oxd-primary-one-color;
    &:hover {
      text-decoration: underline;
    }
  }
  ::v-deep(.oxd-input-group__label-wrapper) {
    display: none;
  }
  ::v-deep(.oxd-input-field-bottom-space) {
    margin-bottom: unset;
  }
}
</style>

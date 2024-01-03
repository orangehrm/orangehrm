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
  <div class="orangehrm-buzz-pill">
    <div class="orangehrm-buzz-pill-actions">
      <post-like :like="post.liked" @click="onClickLike"></post-like>
      <post-comment @click="onClickComment"></post-comment>
    </div>
    <div class="orangehrm-buzz-pill-stats">
      <div class="orangehrm-buzz-pill-stats-likes">
        <oxd-icon name="heart-fill"></oxd-icon>
        <oxd-text tag="p">
          {{ $t('buzz.n_like', {likesCount: post.stats.numOfLikes}) }}
        </oxd-text>
      </div>
      <div class="orangehrm-buzz-pill-stats-other">
        <oxd-text tag="p">{{ combinedPostStats }}</oxd-text>
      </div>
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import useBuzzAPIs from '@/orangehrmBuzzPlugin/util/composable/useBuzzAPIs';
import PostLikeButton from '@/orangehrmBuzzPlugin/components/PostLikeButton';
import PostCommentButton from '@/orangehrmBuzzPlugin/components/PostCommentButton';
import {OxdIcon} from '@ohrm/oxd';

export default {
  name: 'PostActionsPill',

  components: {
    'oxd-icon': OxdIcon,
    'post-like': PostLikeButton,
    'post-comment': PostCommentButton,
  },

  props: {
    post: {
      type: Object,
      required: true,
    },
  },

  emits: ['like', 'comment'],

  setup(props, context) {
    let loading = false;
    const {updatePostLike} = useBuzzAPIs(
      new APIService(window.appGlobal.baseUrl, ''),
    );

    const onClickComment = () => {
      context.emit('comment');
    };

    const onClickLike = () => {
      if (!loading) {
        loading = true;
        updatePostLike(props.post.id, props.post.liked).then(() => {
          loading = false;
          context.emit('like');
        });
      }
    };

    return {
      onClickLike,
      onClickComment,
    };
  },

  computed: {
    combinedPostStats() {
      const commentsCount = this.$t('buzz.n_comment', {
        commentCount: this.post.stats?.numOfComments || 0,
      });

      const sharesCount = this.$t('buzz.n_share', {
        shareCount: this.post.stats?.numOfShares || 0,
      });

      return this.post.stats?.numOfShares === null
        ? commentsCount
        : `${commentsCount}, ${sharesCount}`;
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-pill {
  display: flex;
  padding: 0.5rem;
  border-radius: 1rem;
  align-items: flex-start;
  background: $oxd-white-color;
  justify-content: space-between;
  &-actions {
    gap: 5px;
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: space-between;
    ::v-deep(.oxd-icon-button) {
      width: 36px;
      height: 36px;
    }
  }
  &-stats {
    &-likes {
      display: flex;
      font-size: 1rem;
      font-weight: 700;
      align-items: flex-end;
      justify-content: flex-end;
      ::v-deep(.oxd-icon) {
        margin-right: 5px;
        color: $oxd-feedback-danger-color;
      }
    }
    &-other {
      display: flex;
      font-size: 0.75rem;
      justify-content: flex-end;
    }
  }
}
</style>

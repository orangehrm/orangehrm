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
  <div class="orangehrm-buzz-pill">
    <div class="orangehrm-buzz-pill-actions">
      <post-like :like="false" @click="onClickLike"></post-like>
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
        <oxd-text tag="p">
          {{
            $t('buzz.n_comment', {commentCount: post.stats.numOfShares})
          }},&nbsp;{{
            $t('buzz.n_share', {shareCount: post.stats.numOfComments})
          }}
        </oxd-text>
      </div>
    </div>
  </div>
</template>

<script>
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
import PostLikeButton from '@/orangehrmBuzzPlugin/components/PostLikeButton';
import PostCommentButton from '@/orangehrmBuzzPlugin/components/PostCommentButton';

export default {
  name: 'PostActionsPill',

  components: {
    'oxd-icon': Icon,
    'post-like': PostLikeButton,
    'post-comment': PostCommentButton,
  },

  props: {
    post: {
      type: Object,
      required: true,
    },
  },

  emits: ['comment'],

  setup(_, context) {
    const onClickComment = () => {
      context.emit('comment');
    };

    const onClickLike = () => {
      // todo
    };

    return {
      onClickLike,
      onClickComment,
    };
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
    justify-content: space-between;
  }
  &-stats {
    &-likes {
      gap: 5px;
      font-size: 1rem;
      font-weight: 700;
      display: flex;
      justify-content: flex-end;
      ::v-deep(.oxd-icon) {
        color: $oxd-feedback-danger-color;
      }
    }
    &-other {
      display: flex;
      font-size: 0.75rem;
    }
  }
}
</style>

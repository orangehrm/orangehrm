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
  <div class="orangehrm-buzz-post-actions">
    <post-like :like="isLiked" @click="onClickAction('like')"></post-like>
    <post-comment @click="onClickAction('comment')"></post-comment>
    <post-share @click="onClickAction('share')"></post-share>
  </div>
</template>

<script>
import PostLikeButton from '@/orangehrmBuzzPlugin/components/PostLikeButton.vue';
import PostShareButton from '@/orangehrmBuzzPlugin/components/PostShareButton.vue';
import PostCommentButton from '@/orangehrmBuzzPlugin/components/PostCommentButton.vue';

export default {
  name: 'PostActions',

  components: {
    'post-like': PostLikeButton,
    'post-share': PostShareButton,
    'post-comment': PostCommentButton,
  },

  props: {
    isLiked: {
      type: Boolean,
      required: true,
    },
  },

  emits: ['like', 'comment', 'share'],

  setup(_, context) {
    const onClickAction = actionType => {
      switch (actionType) {
        case 'comment':
          context.emit('comment');
          break;

        case 'share':
          context.emit('share');
          break;

        default:
          context.emit('like');
          break;
      }
    };

    return {
      onClickAction,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-post-actions {
  width: 8rem;
  display: flex;
  justify-content: space-between;
}
</style>

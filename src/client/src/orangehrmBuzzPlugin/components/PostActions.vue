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
    <div class="orangehrm-action-button-like">
      <like-button :post-id="postId" :buzz-user-id="buzzUserId"></like-button>
    </div>
    <div
      class="orangehrm-action-button-comment"
      @click="$emit('showComment', $event)"
    >
      <comment-button></comment-button>
    </div>
    <div class="orangehrm-action-button-share">
      <share-button></share-button>
    </div>
  </div>
</template>
<script>
import LikeButton from '@/orangehrmBuzzPlugin/components/PostActionLikeButton.vue';
import ShareButton from '@/orangehrmBuzzPlugin/components/PostActionShareButton.vue';
import CommentButton from '@/orangehrmBuzzPlugin/components/PostActionCommentButton.vue';

export default {
  name: 'PostActions',

  components: {
    'like-button': LikeButton,
    'share-button': ShareButton,
    'comment-button': CommentButton,
  },

  props: {
    postId: {
      type: Number,
      required: true,
    },
    buzzUserId: {
      type: Number,
      required: true,
    },
  },
  emits: ['showComment'],
};
</script>
<style lang="scss" scoped>
.orangehrm-buzz-post-actions {
  display: flex;
  justify-content: space-between;
  width: 8rem;
}
</style>

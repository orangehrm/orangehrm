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
      <like-button
        :post-id="postId"
        :emp-number="empNumber"
        @click="onClickAction('like')"
      ></like-button>
    </div>
    <div class="orangehrm-action-button-comment">
      <comment-button @click="onClickAction('comment')"></comment-button>
    </div>
    <div class="orangehrm-action-button-share">
      <share-button @click="onClickAction('share')"></share-button>
    </div>
  </div>
</template>
<script>
import {APIService} from '@/core/util/services/api.service';
import {freshDate, formatDate} from '@/core/util/helper/datefns';
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
    empNumber: {
      type: Number,
      required: true,
    },
  },

  emits: ['like'],

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/buzz/posts/${props.postId}/like`,
    );

    return {
      http,
    };
  },

  data() {
    return {
      isActive: false,
    };
  },

  methods: {
    onClickAction(type) {
      switch (type) {
        case 'like':
          this.isActive = !this.isActive;
          this.addLike();
          break;

        case 'comment':
          break;

        default:
          break;
      }
    },
    addLike() {
      const currentDate = freshDate();
      const likedTime =
        formatDate(currentDate, 'yyyy-MM-dd') +
        ' ' +
        formatDate(new Date(), 'HH:mm:ss');
      if (this.isActive) {
        this.http
          .create({
            userId: this.buzzUserId,
            likedTime: likedTime,
          })
          .then(() => {
            this.$emit('like', true);
          });
      } else {
        this.http
          .deleteAll({
            userId: this.buzzUserId,
          })
          .then(() => {
            this.$emit('like', false);
          });
      }
    },
  },
};
</script>
<style lang="scss" scoped>
.orangehrm-buzz-post-actions {
  display: flex;
  justify-content: space-between;
  width: 8rem;
}
</style>

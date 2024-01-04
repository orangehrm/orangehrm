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
  <div v-click-outside="onClose" class="orangehrm-buzz-stats">
    <div class="orangehrm-buzz-stats-row">
      <oxd-icon
        name="heart-fill"
        class="orangehrm-buzz-stats-like-icon"
      ></oxd-icon>
      <oxd-text tag="p" :class="likesClasses" @click="onShowLikeList">
        {{ likesCount }}
      </oxd-text>
      <post-stats-modal
        v-if="showLikeList"
        type="likes"
        icon="heart-fill"
        :mobile="mobile"
        :post-id="post.id"
        @close="onClose"
      ></post-stats-modal>
    </div>
    <div class="orangehrm-buzz-stats-row">
      <oxd-text
        tag="p"
        class="orangehrm-buzz-stats-active"
        @click="onShowComments"
      >
        {{ commentsCount }}
      </oxd-text>
      <template v-if="sharesCount">
        &sbquo;&nbsp;
        <oxd-text tag="p" :class="sharesClasses" @click="onShowSharesList">
          {{ sharesCount }}
        </oxd-text>
      </template>
      <post-stats-modal
        v-if="showSharesList"
        type="shares"
        icon="share-fill"
        :mobile="mobile"
        :post-id="post.post.id"
        @close="onClose"
      ></post-stats-modal>
    </div>
  </div>
</template>
<script>
import PostStatsModal from '@/orangehrmBuzzPlugin/components/PostStatsModal.vue';
import {clickOutsideDirective, OxdIcon} from '@ohrm/oxd';

export default {
  name: 'PostStats',

  components: {
    'oxd-icon': OxdIcon,
    'post-stats-modal': PostStatsModal,
  },

  directives: {
    'click-outside': clickOutsideDirective,
  },

  props: {
    post: {
      type: Object,
      required: true,
    },
    mobile: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['comment'],

  data() {
    return {
      showLikeList: false,
      showSharesList: false,
    };
  },

  computed: {
    likesCount() {
      return this.$t('buzz.n_like', {
        likesCount: this.post.stats?.numOfLikes || 0,
      });
    },
    sharesCount() {
      if (this.post.stats?.numOfShares === null) return null;
      return this.$t('buzz.n_share', {
        shareCount: this.post.stats?.numOfShares || 0,
      });
    },
    commentsCount() {
      return this.$t('buzz.n_comment', {
        commentCount: this.post.stats?.numOfComments || 0,
      });
    },
    likesClasses() {
      return {
        'orangehrm-buzz-stats-active': this.post.stats?.numOfLikes > 0,
      };
    },
    sharesClasses() {
      return {
        'orangehrm-buzz-stats-active': this.post.stats?.numOfShares > 0,
      };
    },
  },

  methods: {
    onShowComments() {
      this.$emit('comment');
    },
    onShowLikeList() {
      this.showSharesList = false;
      if (!this.post.stats?.numOfLikes) return;
      this.showLikeList = true;
    },
    onShowSharesList() {
      this.showLikeList = false;
      if (!this.post.stats?.numOfShares) return;
      this.showSharesList = true;
    },
    onClose() {
      this.showLikeList = false;
      this.showSharesList = false;
    },
  },
};
</script>

<style lang="scss" src="./post-stats.scss" scoped></style>

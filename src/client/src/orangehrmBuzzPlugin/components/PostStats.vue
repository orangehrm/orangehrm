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
  <div class="orangehrm-buzz-post-footer">
    <div class="orangehrm-buzz-post-footer-right">
      <div class="orangehrm-buzz-post-footer-summery-top">
        <oxd-icon name="heart-fill" class="orangehrm-buzz-like-icon"></oxd-icon>
        <oxd-text
          tag="p"
          @mouseleave="onShowLikeListMobile"
          @mouseover="onShowLikeList"
          @click="onShowLikeList"
        >
          {{ $t('buzz.n_like', {likesCount: noOfLikes}) }}
        </oxd-text>
        <div v-if="showLikeList">
          <post-stats-modal
            :post-id="postId"
            :mobile="mobile"
            icon-name="heart-fill"
            status-name="likes"
          ></post-stats-modal>
        </div>
      </div>
      <div class="orangehrm-buzz-post-footer-summery-bottom">
        <oxd-text
          tag="p"
          class="orangehrm-buzz-post-footer-comment"
          @click="$emit('showComment', $event)"
        >
          {{ $t('buzz.n_comment', {commentCount: noOfComments}) }} ,
        </oxd-text>
        <div>
          <oxd-text
            tag="p"
            @mouseleave="onShowSharesListMobile"
            @mouseover="onShowSharesList"
            @click="onShowSharesList"
          >
            {{ $t('buzz.n_share', {shareCount: noOfShares}) }}
          </oxd-text>
          <div v-if="showSharesList">
            <post-stats-modal
              :post-id="postId"
              :mobile="mobile"
              icon-name="share-fill"
              status-name="shares"
            ></post-stats-modal>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
import {APIService} from '@/core/util/services/api.service';
import PostStatsModal from '@/orangehrmBuzzPlugin/components/PostStatsModal.vue';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'PostStats',

  components: {
    'oxd-icon': Icon,
    'post-stats-modal': PostStatsModal,
  },

  props: {
    postId: {
      type: Number,
      required: true,
    },
    noOfLikes: {
      type: Number,
      default: 0,
    },
    noOfComments: {
      type: Number,
      default: 0,
    },
    noOfShares: {
      type: Number,
      default: 0,
    },
    mobile: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['showComment'],

  setup() {
    const {$tEmpName} = useEmployeeNameTranslate();
    const http = new APIService(window.appGlobal.baseUrl, '');

    return {
      http,
      tEmpName: $tEmpName,
    };
  },

  data() {
    return {
      isLoading: false,
      showLikeList: false,
      showSharesList: false,
      likedList: [],
      sharesList: [],
    };
  },

  methods: {
    onShowLikeList() {
      this.showLikeList = true;
    },
    onShowSharesList() {
      this.showSharesList = true;
    },
    onShowSharesListMobile() {
      if (!this.mobile) {
        this.showSharesList = false;
      }
    },
    onShowLikeListMobile() {
      if (!this.mobile) {
        this.showLikeList = false;
      }
    },
  },
};
</script>

<style lang="scss" scoped src="./post-stats.scss"></style>

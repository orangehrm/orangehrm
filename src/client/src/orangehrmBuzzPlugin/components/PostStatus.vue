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
        <div v-if="showLikeList && !isMobile">
          <oxd-sheet
            :gutters="false"
            type="white"
            class="orangehrm-buzz-post-footer-like-list"
          >
            <div
              v-for="like in likedList"
              :key="like"
              class="orangehrm-buzz-post-footer-like-employee"
            >
              <div class="orangehrm-buzz-post-profile-image">
                <img
                  alt="profile picture"
                  class="employee-image"
                  :src="`../pim/viewPhoto/empNumber/${like.empNumber}`"
                />
              </div>
              <oxd-text tag="p">
                {{ like.empName }}
              </oxd-text>
            </div>
            <oxd-loading-spinner
              v-if="isLoading"
              class="orangehrm-buzz-loader"
            />
          </oxd-sheet>
        </div>
        <div v-if="showLikeList && isMobile">
          <oxd-post-status
            :post-id="1"
            icon-name="heart-fill"
            status-name="likes"
          ></oxd-post-status>
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
          <div v-if="showSharesList && !isMobile">
            <oxd-sheet
              :gutters="false"
              type="white"
              class="orangehrm-buzz-post-footer-share-list"
            >
              <div
                v-for="share in sharesList"
                :key="share"
                class="orangehrm-buzz-post-footer-share-employee"
              >
                <div class="orangehrm-buzz-post-profile-image">
                  <img
                    alt="profile picture"
                    class="employee-image"
                    :src="`../pim/viewPhoto/empNumber/${share.empNumber}`"
                  />
                </div>
                <oxd-text tag="p">
                  {{ share.empName }}
                </oxd-text>
              </div>
              <oxd-loading-spinner
                v-if="isLoading"
                class="orangehrm-buzz-loader"
              />
            </oxd-sheet>
          </div>
          <div v-if="showSharesList && isMobile">
            <oxd-post-status
              :post-id="1"
              icon-name="share-fill"
              status-name="shares"
            ></oxd-post-status>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import {APIService} from '@/core/util/services/api.service';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import PostStatusDialog from '@/orangehrmBuzzPlugin/components/PostStatusDialog.vue';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'PostStatus',

  components: {
    'oxd-icon': Icon,
    'oxd-sheet': Sheet,
    'oxd-loading-spinner': Spinner,
    'oxd-post-status': PostStatusDialog,
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
    isMobile: {
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
      this.isLoading = true;
      if (this.noOfLikes > 0) {
        this.http
          .request({
            method: 'GET',
            url: `api/v2/buzz/posts/${this.postId}/likes`,
          })
          .then(response => {
            const {data} = response.data;
            this.likedList = data.map(item => {
              const {employee} = item;
              return {
                empNumber: employee.empNumber,
                empName: this.tEmpName(employee, {
                  includeMiddle: false,
                  excludePastEmpTag: false,
                }),
              };
            });
          })
          .finally(() => {
            this.isLoading = false;
          });
      }
    },
    onShowSharesList() {
      this.showSharesList = true;
      this.isLoading = true;
      if (this.noOfShares > 0) {
        this.http
          .request({
            method: 'GET',
            url: `api/v2/buzz/posts/${this.postId}/shares`,
          })
          .then(response => {
            const {data} = response.data;
            this.sharesList = data.map(item => {
              const {employee} = item;
              return {
                empNumber: employee.empNumber,
                empName: this.tEmpName(employee, {
                  includeMiddle: false,
                  excludePastEmpTag: false,
                }),
              };
            });
          })
          .finally(() => {
            this.isLoading = false;
          });
      }
    },
    onShowSharesListMobile() {
      if (!this.isMobile) {
        this.showSharesList = false;
      }
    },
    onShowLikeListMobile() {
      if (!this.isMobile) {
        this.showLikeList = false;
      }
    },
  },
};
</script>
<style lang="scss" scoped src="./post-status.scss"></style>

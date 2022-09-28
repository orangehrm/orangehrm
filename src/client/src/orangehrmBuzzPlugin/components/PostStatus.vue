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
  <oxd-sheet :gutters="false" type="white" class="orangehrm-buzz">
    <div class="orangehrm-buzz-post">
      <div class="orangehrm-buzz-post-header">
        <div class="orangehrm-buzz-post-header-details">
          <div class="orangehrm-buzz-post-profile-image">
            <img
              alt="profile picture"
              class="employee-image"
              :src="`../pim/viewPhoto/empNumber/1`"
            />
          </div>
          <div class="orangehrm-buzz-post-header-text">
            <oxd-text tag="p" class="orangehrm-buzz-post-emp-name">
              <!-- need translation -->
              Employee Name
            </oxd-text>
            <oxd-text tag="p" class="orangehrm-buzz-post-time">
              <!-- need translation -->
              Yesterday at 6:01 AM
            </oxd-text>
          </div>
        </div>
        <div class="orangehrm-buzz-post-header-config">
          <oxd-icon-button class="" name="three-dots" withContainer="true" />
        </div>
      </div>
      <oxd-divider />
    </div>
    <div class="orangehrm-buzz-post-body">
      this is post body
      <slot name="body"></slot>
    </div>
    <div class="orangehrm-buzz-post-footer">
      <div class="orangehrm-buzz-post-footer-left">
        <action-button></action-button>
        <!-- <slot name="action-button"></slot> -->
      </div>
      <div class="orangehrm-buzz-post-footer-right">
        <div class="orangehrm-buzz-post-footer-summery-top">
          <oxd-icon
            name="heart-fill"
            class="orangehrm-buzz-like-icon"
          ></oxd-icon>
          <oxd-text tag="p">
            <!-- add translation -->
            X Likes
          </oxd-text>
        </div>
        <div class="orangehrm-buzz-post-footer-summery-bottom">
          <oxd-text tag="p" class="orangehrm-buzz-post-footer-comment">
            <!-- add translation -->
            Y Comments ,
          </oxd-text>
          <oxd-text tag="p">
            <!-- add translation -->
            Z Shares
          </oxd-text>
        </div>
      </div>
    </div>
    <div class="orangehrm-buzz-post-comment">

    </div>
  </oxd-sheet>
</template>
<script>
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import PostActionButtons from '@/orangehrmBuzzPlugin/components/PostActionButtons.vue'

export default {
  name: 'PostContainer',
  components: {
    'oxd-icon': Icon,
    'oxd-sheet': Sheet,
    'action-button': PostActionButtons,
  },
};
</script>
<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-buzz {
  padding: 1rem;
  width: 30rem;
  &-like-icon {
    color: $oxd-feedback-danger-color;
  }
}
.orangehrm-buzz-post {
  &-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    &-details {
      display: flex;
      justify-content: center;
    }
    &-text {
      margin-left: 1rem;
    }
  }
  &-profile-image {
    & img {
      width: 40px;
      height: 40px;
      border-radius: 100%;
      display: flex;
      flex-shrink: 0;
      justify-content: center;
      box-sizing: border-box;
    }
  }
  &-emp-name {
    font-size: 1.1rem;
    font-weight: 600;
  }
  &-time {
    font-size: 0.75rem;
  }
  &-footer {
    display: flex;
    padding-top: 1rem;
    align-items: center;
    justify-content: space-between;
    &-summery-top {
      display: flex;
      font-size: 1rem;
      font-weight: 700;
      align-items: center;
      justify-content: end;
      & p {
        margin-left: 0.5rem;
      }
    }
    &-summery-bottom {
      display: flex;
      font-size: 0.75rem;
      cursor: pointer;
    }
  }
}
</style>

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
  <div class="orangehrm-comment">
    <div class="orangehrm-comment-profile">
      <div class="orangehrm-comment-profile-image-wrapper">
        <img
          alt="profile picture"
          class="orangehrm-comment-profile-image"
          :src="imgSrc"
        />
      </div>
    </div>
    <div class="orangehrm-comment-body">
      <div class="orangehrm-comment-profile-name">
        <oxd-text type="subtitle-2">
          {{ data.employee.firstName }} {{ data.employee.lastName }}
        </oxd-text>
      </div>
      <div class="orangehrm-comment-message">
        <oxd-text type="subtitle-2">
          {{ data.comment }}
        </oxd-text>
      </div>
      <div class="orangehrm-comment-timestamp">
        <oxd-text type="subtitle-2">{{ data.date }} - {{ data.time }}</oxd-text>
      </div>
    </div>
  </div>
</template>

<script>
import {computed} from 'vue';
const defaultPic = `${window.appGlobal.baseUrl}/../dist/img/user-default-400.png`;

export default {
  name: 'leave-comment',
  props: {
    data: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const imgSrc = computed(() => {
      return props.data?.employee?.empNumber
        ? `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${props.data.employee.empNumber}`
        : defaultPic;
    });

    return {
      imgSrc,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-comment {
  box-sizing: border-box;
  padding: 0.5rem 0.5rem 0 0;
  display: flex;
  &-profile-image-wrapper {
    margin-right: 0.5rem;
  }
  &-profile-image {
    width: 70px;
    height: 70px;
    display: flex;
    overflow: hidden;
    justify-content: center;
    box-sizing: border-box;
    border-radius: 100%;
    border: 0.5rem solid $oxd-background-pastel-white-color;
  }
  &-profile-name p {
    font-weight: 700;
    margin-bottom: 0.25rem;
  }
  &-message {
    padding: 0.5rem 1rem;
    background-color: $oxd-background-pastel-white-color;
    border-radius: 0.5rem;
    margin-bottom: 0.25rem;
  }
  &-timestamp {
    text-align: right;
  }
}
</style>

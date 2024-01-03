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
  <div class="orangehrm-buzz-profile-image">
    <img alt="profile picture" :src="imageSrc" />
  </div>
</template>

<script>
export default {
  name: 'ProfileImage',
  props: {
    employee: {
      type: Object,
      required: true,
    },
  },
  computed: {
    imageSrc() {
      return `../pim/viewPhoto/empNumber/${this.employee.empNumber}`;
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-profile-image {
  & img {
    width: 45px;
    height: 45px;
    display: flex;
    flex-shrink: 0;
    border-radius: 100%;
    box-sizing: border-box;
    justify-content: center;
  }
}
</style>

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
  <div class="orangehrm-profile-picture">
    <img
      :src="imgSrc"
      alt="Profile Picture"
      class="orangehrm-profile-picture-img"
    />
  </div>
</template>

<script>
import {computed} from 'vue';

const defaultPic = `${window.appGlobal.publicPath}/images/default-photo.png`;

export default {
  name: 'ProfilePicture',
  props: {
    id: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    const imgSrc = computed(() => {
      return props.id
        ? `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${props.id}`
        : defaultPic;
    });
    return {
      imgSrc,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-profile-picture {
  display: block;
  height: 90px;
  width: 90px;
  margin: 0 auto;
  overflow: hidden;
  border-radius: 50%;
  & img {
    height: 90px;
    width: 90px;
  }
}
</style>

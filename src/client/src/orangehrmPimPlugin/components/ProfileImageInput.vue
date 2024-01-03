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
  <oxd-input-field ref="fileInput" v-bind="$attrs" type="file">
    <div class="employee-image-wrapper">
      <img alt="profile picture" class="employee-image" :src="imgSrc" />
    </div>
    <oxd-icon-button
      name="plus"
      role="none"
      display-type="solid-main"
      class="employee-image-action"
    />
  </oxd-input-field>
  <oxd-text class="orangehrm-input-hint" tag="p">
    {{
      $t('general.accept_jpg_png_upto_1mb_recomended_dimentions_200px_x_200px')
    }}
  </oxd-text>
</template>

<script>
export default {
  name: 'ProfileImageInput',
  inheritAttrs: false,
  props: {
    imgSrc: {
      type: String,
      required: true,
    },
  },
};
</script>

<style lang="scss" scoped>
.employee-image {
  height: 8rem;
}
.orangehrm-input-hint {
  margin: 0 auto;
  text-align: center;
}
::v-deep(.oxd-file-div) {
  margin: 0 auto;
  display: flex;
  position: relative;
  align-items: center;
  justify-content: center;
  width: 8rem !important;
  height: 8rem !important;
  border-radius: 100% !important;
  border: $oxd-border $oxd-interface-gray-lighten-2-color;
}
.employee-image-wrapper {
  height: 90%;
  width: 90%;
  display: flex;
  overflow: hidden;
  border-radius: 100%;
  justify-content: center;
  align-items: flex-start;
  border: $oxd-border $oxd-interface-gray-lighten-2-color;
}
.employee-image-action {
  right: 0;
  bottom: 0;
  position: absolute;
}
</style>

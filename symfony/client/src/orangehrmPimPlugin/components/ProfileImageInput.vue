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
  <oxd-input-field ref="fileInput" v-bind="$attrs" type="file">
    <div class="employee-image-wrapper">
      <img alt="profile picture" class="employee-image" :src="imgSrc" />
    </div>
    <oxd-icon-button class="employee-image-action" name="plus" role="none" />
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
@import '@ohrm/oxd/core/components/Button/_variables.scss';
@import '@ohrm/oxd/core/components/Button/_mixins.scss';

.employee-image {
  height: 8rem;
}
.orangehrm-input-hint {
  text-align: center;
  margin: 0 auto;
}
::v-deep(.oxd-file-div) {
  border: $oxd-input-control-border--active;
  border-radius: 100% !important;
  width: 8rem !important;
  height: 8rem !important;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
.employee-image-wrapper {
  height: 90%;
  width: 90%;
  overflow: hidden;
  border: $oxd-input-control-border--active;
  border-radius: 100%;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}
.employee-image-action {
  position: absolute;
  right: 0;
  bottom: 0;
  @include oxd-button-solid('main');
  ::v-deep(.oxd-icon) {
    color: $oxd-white-color;
  }
}
</style>

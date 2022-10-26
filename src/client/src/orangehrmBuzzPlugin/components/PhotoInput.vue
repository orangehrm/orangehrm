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
  <div class="orangehrm-photo-input">
    <photo-upload-area
      v-if="showUploadArea"
      :model-value="value"
      @update:modelValue="onFileChange"
    >
    </photo-upload-area>
    <div class="orangehrm-photo-input-field">
      <oxd-input-field
        v-if="showUploadButton"
        type="file"
        @update:modelValue="onFileChange"
      >
        <oxd-button icon-name="file-image" label="Add Photos" />
      </oxd-input-field>
    </div>

    <photo-frame :media="modelValue"></photo-frame>
  </div>
</template>

<script>
import {computed} from 'vue';
import PhotoFrame from '@/orangehrmBuzzPlugin/components/PhotoFrame';
import PhotoUploadArea from '@/orangehrmBuzzPlugin/components/PhotoUploadArea';

export default {
  name: 'PhotoInput',

  components: {
    'photo-frame': PhotoFrame,
    'photo-upload-area': PhotoUploadArea,
  },

  props: {
    modelValue: {
      type: Array,
      required: true,
    },
  },

  emits: ['update:modelValue'],

  setup(props, context) {
    const onFileChange = $event => {
      $event &&
        context.emit('update:modelValue', [
          ...(props.modelValue || []),
          $event,
        ]);
    };

    const value = computed(() =>
      Array.isArray(props.modelValue) ? props.modelValue[0] : null,
    );
    const showUploadArea = computed(
      () => Array.isArray(props.modelValue) && props.modelValue.length < 1,
    );
    const showUploadButton = computed(
      () =>
        Array.isArray(props.modelValue) &&
        props.modelValue.length > 0 &&
        props.modelValue.length < 5,
    );

    return {
      value,
      onFileChange,
      showUploadArea,
      showUploadButton,
    };
  },
};
</script>

<style src="./photo-input.scss" lang="scss" scoped></style>

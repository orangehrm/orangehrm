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
  <!-- Always use inside OXD-Form -->
  <oxd-input-group v-if="file" :label="`Current ${label}`">
    <div class="orangehrm-file-current">
      <div class="orangehrm-file-preview" @click="downloadFile">
        <oxd-icon class="orangehrm-file-icon" name="file-earmark-arrow-down" />
        <oxd-text class="orangehrm-file-name" tag="p" :title="file.filename">
          {{ file.filename }}
        </oxd-text>
      </div>
      <div class="orangehrm-file-options">
        <oxd-input-field
          type="radio"
          optionLabel="Keep Current"
          value="keepCurrent"
          :modelValue="method"
          @update:modelValue="$emit('update:method', $event)"
        />
        <oxd-input-field
          type="radio"
          optionLabel="Delete Current"
          value="deleteCurrent"
          :modelValue="method"
          @update:modelValue="$emit('update:method', $event)"
        />
        <oxd-input-field
          type="radio"
          optionLabel="Replace Current"
          value="replaceCurrent"
          :modelValue="method"
          @update:modelValue="$emit('update:method', $event)"
        />
      </div>
    </div>
  </oxd-input-group>
  <div v-if="method === 'replaceCurrent' || !file" class="orangehrm-file-input">
    <oxd-input-field
      v-bind="$attrs"
      type="file"
      :label="label"
      :modelValue="newFile"
      @update:modelValue="$emit('update:newFile', $event)"
    />
    <oxd-text class="orangehrm-input-hint" tag="p">{{ hint }}</oxd-text>
  </div>
</template>

<script>
import Icon from '@orangehrm/oxd/core/components/Icon/Icon.vue';

export default {
  name: 'file-upload-input',
  inheritAttrs: false,
  components: {
    'oxd-icon': Icon,
  },
  props: {
    label: {
      type: String,
      default: '',
    },
    hint: {
      type: String,
      default: '',
    },
    url: {
      type: String,
      required: true,
    },
    method: {
      type: String,
      required: true,
    },
    file: {
      required: true,
    },
    newFile: {
      required: true,
    },
  },
  methods: {
    downloadFile() {
      if (!this.file?.id) return;
      const downUrl = `${window.appGlobal.baseUrl}/${this.url}/${this.file.id}`;
      window.open(downUrl, '_blank');
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-file {
  &-current {
    display: flex;
    margin-bottom: 1rem;
  }
  &-icon {
    display: block;
    font-size: 3rem;
    margin-bottom: 0.5rem;
  }
  &-name {
    display: block;
    text-align: center;
    font-size: 12px;
    width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  &-preview,
  &-options {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  &-preview {
    margin-right: 0.5rem;
    background-color: $oxd-background-pastel-white-color;
    padding: 0.5rem;
    border-radius: 0.65rem;
    cursor: pointer;
  }
}
</style>

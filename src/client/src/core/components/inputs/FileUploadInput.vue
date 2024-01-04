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
  <!-- Always use inside OXD-Form -->
  <oxd-input-group v-if="fileSelected" :label="label">
    <div class="orangehrm-file-current">
      <div class="orangehrm-file-preview" @click="downloadFile">
        <oxd-icon class="orangehrm-file-icon" name="file-earmark-text" />
        <oxd-text class="orangehrm-file-name" tag="p" :title="file.filename">
          {{ file.filename }}
          <oxd-icon class="orangehrm-file-download" name="download" />
        </oxd-text>
      </div>
      <div v-if="!disabled" class="orangehrm-file-options">
        <oxd-input-field
          type="radio"
          :option-label="$t('general.keep_current')"
          value="keepCurrent"
          :model-value="method"
          @update:model-value="$emit('update:method', $event)"
        />
        <oxd-input-field
          v-if="deletable"
          type="radio"
          :option-label="$t('general.delete_current')"
          value="deleteCurrent"
          :model-value="method"
          @update:model-value="$emit('update:method', $event)"
        />
        <oxd-input-field
          type="radio"
          :option-label="$t('general.replace_current')"
          value="replaceCurrent"
          :model-value="method"
          @update:model-value="$emit('update:method', $event)"
        />
      </div>
    </div>
  </oxd-input-group>
  <div
    v-if="method === 'replaceCurrent' || !fileSelected"
    class="orangehrm-file-input"
  >
    <oxd-input-field
      v-bind="$attrs"
      type="file"
      :label="fieldLabel"
      :model-value="newFile"
      :disabled="disabled"
      :placeholder="$t('general.no_file_selected')"
      @update:model-value="$emit('update:newFile', $event)"
    />
    <oxd-text class="orangehrm-input-hint" tag="p">{{ hint }}</oxd-text>
  </div>
</template>

<script>
import {OxdIcon} from '@ohrm/oxd';

export default {
  name: 'FileUploadInput',
  components: {
    'oxd-icon': OxdIcon,
  },
  inheritAttrs: false,
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
      type: [String, Function],
      required: true,
    },
    method: {
      type: String,
      required: true,
    },
    file: {
      type: Object,
      required: false,
      default: () => null,
    },
    newFile: {
      type: Object,
      required: false,
      default: () => null,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    deletable: {
      type: Boolean,
      default: true,
    },
  },
  emits: ['update:method', 'update:newFile'],
  computed: {
    fileSelected() {
      return this.file && Object.keys(this.file).length > 0;
    },
    fieldLabel() {
      return this.method === 'replaceCurrent' && this.fileSelected
        ? this.$t('general.upload_n_file', {
            fileName: this.label,
          })
        : this.label;
    },
  },
  methods: {
    downloadFile() {
      let downUrl;
      if (typeof this.url === 'function') {
        downUrl = this.url(this.$props);
      } else {
        if (!this.file?.id) return;
        downUrl = `${window.appGlobal.baseUrl}/${this.url}/${this.file.id}`;
      }
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
  &-download {
    font-size: 12px;
    margin-left: 10px;
    vertical-align: middle;
  }
  &-name {
    display: block;
    font-size: 12px;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: break-word;
  }
  &-options {
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
  }
  &-preview {
    padding: 0.5rem;
    cursor: pointer;
    min-height: 90px;
    min-width: 150px;
    text-align: center;
    border-radius: 0.5rem;
    margin-right: 1rem;
    justify-content: center;
    flex-direction: column;
    display: flex;
    align-items: center;
    border: 1px solid $oxd-interface-gray-lighten-1-color;
    background-color: $oxd-background-pastel-white-color;
  }
}

::v-deep(.--label-right) {
  flex-shrink: 0;
  align-self: center;
}
</style>

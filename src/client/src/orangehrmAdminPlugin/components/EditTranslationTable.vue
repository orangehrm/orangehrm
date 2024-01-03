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
  <div class="orangehrm-translation-container">
    <oxd-divider />
    <oxd-grid :cols="3" class="orangehrm-translation-grid">
      <oxd-grid-item class="orangehrm-translation-grid-header">
        <oxd-text type="card-title">{{ $t('admin.source_text') }}</oxd-text>
      </oxd-grid-item>
      <oxd-grid-item class="orangehrm-translation-grid-header">
        <oxd-text type="card-title">{{ $t('admin.source_note') }}</oxd-text>
      </oxd-grid-item>
      <oxd-grid-item class="orangehrm-translation-grid-header">
        <oxd-text type="card-title">{{ $t('admin.translated_text') }}</oxd-text>
      </oxd-grid-item>
      <template v-for="(langstring, index) in langstrings" :key="index">
        <oxd-grid-item class="orangehrm-translation-grid-text">
          <oxd-text
            class="orangehrm-translation-grid-langstring-header"
            type="card-title"
          >
            {{ $t('admin.source_text') }}
          </oxd-text>
          <oxd-text :title="langstring.source">
            {{ langstring.source }}
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item class="orangehrm-translation-grid-text">
          <oxd-text
            class="orangehrm-translation-grid-langstring-header-note"
            type="card-title"
          >
            {{ $t('admin.source_note') }}
          </oxd-text>
          <oxd-text
            :title="langstring.note"
            class="orangehrm-translation-grid-header"
          >
            {{ langstring.note }}
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item class="orangehrm-translation-grid-text">
          <oxd-text
            class="orangehrm-translation-grid-langstring-header"
            type="card-title"
          >
            {{ $t('admin.translated_text') }}
          </oxd-text>
          <oxd-input-field
            type="input"
            :placeholder="langstring.target"
            :model-value="langstring.target"
            :rules="rules.langString"
            @update:model-value="onUpdateTranslation($event, index)"
          />
          <oxd-divider class="orangehrm-translation-grid-langstring-header" />
        </oxd-grid-item>
      </template>
    </oxd-grid>
    <oxd-divider />
  </div>
</template>
<script>
import {validLangString} from '@/core/util/validation/rules';

export default {
  props: {
    langstrings: {
      type: Array,
      required: true,
    },
  },

  emits: ['update:langstrings'],

  setup(props, context) {
    const onUpdateTranslation = (value, index) => {
      context.emit(
        'update:langstrings',
        props.langstrings.map((item, _index) => {
          if (_index === index) {
            return {...item, target: value, modified: true};
          }
          return item;
        }),
      );
    };

    return {
      onUpdateTranslation,
      rules: {
        langString: [validLangString],
      },
    };
  },
};
</script>
<style src="./edit-translation-table.scss" lang="scss" scoped></style>

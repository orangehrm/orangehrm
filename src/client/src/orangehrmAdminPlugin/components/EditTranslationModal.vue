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
  <div class="orangehrm-translation-container">
    <oxd-divider />
    <oxd-grid :cols="3">
      <oxd-grid-item>
        <oxd-text type="card-title">{{ $t('admin.source_text') }}</oxd-text>
      </oxd-grid-item>
      <oxd-grid-item>
        <oxd-text type="card-title">{{ $t('admin.source_note') }}</oxd-text>
      </oxd-grid-item>
      <oxd-grid-item>
        <oxd-text type="card-title">{{ $t('admin.translated_text') }}</oxd-text>
      </oxd-grid-item>
      <template v-for="(langstring, index) in langstrings" :key="index">
        <oxd-grid-item>
          <oxd-text :title="langstring.source">
            {{ langstring.source }}
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-text :title="langstring.source">
            {{ langstring.note }}
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-field
            type="input"
            :placeholder="langstring.target"
            :model-value="langstring.target"
            @update:modelValue="onUpdateTranslation($event, index)"
          />
        </oxd-grid-item>
      </template>
    </oxd-grid>
    <oxd-divider />
  </div>
</template>
<script>
import Divider from '@ohrm/oxd/core/components/Divider/Divider.vue';

export default {
  components: {
    'oxd-divider': Divider,
  },
  props: {
    langstrings: {
      type: Array,
      required: true,
    },
  },

  emit: ['update:langstrings'],

  setup(props, context) {
    const onUpdateTranslation = (value, index) => {
      context.emit(
        'update:langstrings',
        props.langstrings.map((item, _index) => {
          if (_index === index) {
            return {...item, target: value};
          }
          return item;
        }),
      );
    };
    return {onUpdateTranslation};
  },
};
</script>
<style lang="scss" scoped>
.orangehrm-translation-container {
  padding-left: 25px;
  padding-right: 25px;
}
.orangehrm-table-header {
  background-color: #fff;
  padding: 0.6rem;
  border-top-right-radius: 1.2rem;
  border-top-left-radius: 1.2rem;
}

.orangehrm-table-footer {
  background-color: #fff;
  padding: 1.2rem;
  border-bottom-right-radius: 1.2rem;
  border-bottom-left-radius: 1.2rem;
}

.orangehrm-container {
  background-color: #e8eaef;
  border-radius: unset;
  padding: 0.5rem;
}

::v-deep(.card-footer-slot) {
  .oxd-table-cell-actions {
    justify-content: flex-end;
  }
}
</style>

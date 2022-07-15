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
  <div class="orangehrm-paper-container">
    <div class="orangehrm-table-header">
      <div>
        <oxd-button
          display-type="secondary"
          :label="$t('general.save')"
          type="reset"
        />
        <oxd-button
          class="orangehrm-left-space"
          display-type="ghost"
          :label="$t('general.cancel')"
          type="submit"
        />
      </div>
    </div>
    <table-header
      :loading="isLoading"
      :total="total"
      :selected="0"
    ></table-header>
    <oxd-grid :cols="3" class="orangehrm-evaluation-grid">
      <template
        v-for="langstrings in langstrings"
        :key="langstrings.langStringId"
      >
        <oxd-grid-item class="orangehrm-evaluation-grid-kpi">
          <!-- <oxd-text
            class="orangehrm-evaluation-grid-kpi-header"
            type="subtitle-2"
          >
            {{ $t('admin.source_text') }}
          </oxd-text> -->
          <oxd-text
            :title="langstrings.source"
            tag="p"
            class="orangehrm-evaluation-grid-kpi-label"
          >
            {{ langstrings.source }}
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item>
          <!-- <oxd-text
            class="orangehrm-evaluation-grid-kpi-header"
            type="subtitle-2"
          >
            {{ $t('admin.source_note') }}
          </oxd-text> -->
          <oxd-text
            :title="langstrings.source"
            tag="p"
            class="orangehrm-evaluation-grid-kpi-label"
          >
            {{ langstrings.note }}
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item>
          <!-- <oxd-text
            class="orangehrm-evaluation-grid-kpi-header"
            type="subtitle-2"
          >
            {{ $t('admin.translated_text') }}
          </oxd-text> -->
          <oxd-input-field
            class="orangehrm-evaluation-grid-comment"
            rows="2"
            type="textarea"
            :placeholder="langstrings.target"
            :disabled="!editable"
          />
        </oxd-grid-item>
        <oxd-grid-item
          class="orangehrm-evaluation-grid-spacer-md"
        ></oxd-grid-item>
      </template>
    </oxd-grid>
  </div>
</template>
<script>
export default {
  props: {
    langstrings: {
      type: Array,
      required: true,
    },
  },
  setup(props) {
    console.log('langstring');
    console.log(props);
  },
};
</script>
<style lang="scss" scoped>
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

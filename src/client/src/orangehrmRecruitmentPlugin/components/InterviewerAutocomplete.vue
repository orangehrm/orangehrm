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
  <div class="orangehrm-recruitment-interviewer-input">
    <employee-autocomplete
      :label="!showDelete ? $t('recruitment.interviewer') : null"
      :disabled="disabled"
      v-bind="$attrs"
      api-path="/api/v2/recruitment/interviewers"
    />
    <oxd-icon-button
      v-if="showDelete && !disabled"
      name="trash-fill"
      class="orangehrm-recruitment-delete-icon"
      :with-container="false"
      @click="remove"
    />
  </div>
</template>

<script>
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete.vue';

export default {
  name: 'InterviewerAutocomplete',
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
  },
  inheritAttrs: false,
  props: {
    showDelete: {
      type: Boolean,
      required: true,
    },
    includeEmployee: {
      type: String,
      default: 'currentAndPast',
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  emits: ['remove'],
  methods: {
    remove() {
      this.$emit('remove');
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-recruitment {
  &-interviewer-input {
    display: flex;
    align-items: flex-start;
    ::v-deep(.oxd-input-group__label-wrapper:empty) {
      display: none;
    }
  }
  &-delete-icon {
    margin-left: 1rem;
    margin-top: 1rem;
  }
}
</style>

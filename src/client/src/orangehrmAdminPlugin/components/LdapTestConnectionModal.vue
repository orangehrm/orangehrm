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
  <oxd-dialog class="orangehrm-dialog-modal" @update:show="onCancel">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('admin.connection_status') }}
      </oxd-text>
    </div>
    <oxd-divider />

    <div v-for="item in data" :key="item" class="orangehrm-ldap-test">
      <oxd-text tag="p" class="orangehrm-ldap-test-title">
        {{ item.category }}
      </oxd-text>
      <div
        v-for="(check, index) in item.checks"
        :key="index"
        class="orangehrm-ldap-test-row"
      >
        <oxd-text class="orangehrm-ldap-test-content">
          {{ check.label }}
        </oxd-text>
        <oxd-text :class="getClass(check.value.status)">
          {{ check.value.message }}
        </oxd-text>
      </div>
    </div>
  </oxd-dialog>
</template>

<script>
import {OxdDialog} from '@ohrm/oxd';

export default {
  name: 'LdapTestConnectionModal',
  components: {
    'oxd-dialog': OxdDialog,
  },
  props: {
    data: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['close'],
  methods: {
    getClass(id) {
      return id === 1
        ? 'orangehrm-ldap-test-value --success'
        : 'orangehrm-ldap-test-value --error';
    },
    onCancel() {
      this.$emit('close');
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-ldap-test {
  margin-bottom: 0.75rem;
  &-title {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 0.2rem;
  }
  &-value {
    &.--success {
      color: $oxd-feedback-success-color;
    }
    &.--error {
      color: $oxd-feedback-danger-color;
    }
  }
  &-row {
    width: 100%;
    display: flex;
    font-size: 14px;
    margin-bottom: 0.2rem;
  }
  &-content {
    flex: 1;
  }
}
</style>

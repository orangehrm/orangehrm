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
  <div v-if="instanceIdentifier !== null" class="orangehrm-maintenance-note">
    <div class="orangehrm-maintenance-note-header">
      <oxd-text>{{ $t('general.note') }}</oxd-text>
    </div>
    <oxd-text>{{
      $t('maintenance.gdpr_note', {instanceIdentifier: instanceIdentifier})
    }}</oxd-text>
  </div>
</template>

<script>
export default {
  name: 'MaintenanceNote',

  props: {
    instanceIdentifier: {
      type: String,
      required: true,
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-maintenance-note {
  display: flex;
  flex-direction: column;
  overflow-wrap: break-word;
  padding: 1.2rem;
  border-radius: 1.2rem;
  color: $oxd-input-control-font-color;
  background-color: $oxd-interface-gray-lighten-2-color;
  font-size: $oxd-input-control-font-size;

  &-header {
    font-weight: bold;
    margin-bottom: 0.4rem;
  }
}
</style>

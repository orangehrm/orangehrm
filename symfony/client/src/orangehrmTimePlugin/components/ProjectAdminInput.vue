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
  <div class="orangehrm-project-admin-input">
    <employee-autocomplete
      :label="id === 1 ? 'Project Admins' : null"
      class="orangehrm-employee-autocomplete"
      v-bind="$attrs"
    />
    <oxd-icon-button
      name="trash"
      :withContainer="false"
      v-if="id !== 1"
      @click="remove"
    />
  </div>
</template>

<script>
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete.vue';

export default {
  name: 'project-admin-input',
  inheritAttrs: false,
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
  },
  props: {
    id: {
      type: Number,
      required: true,
    },
  },
  methods: {
    remove() {
      this.$emit('remove', this.id);
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-project-admin-input {
  display: flex;
  flex-wrap: nowrap;
  align-items: center;
  justify-content: space-between;
}
</style>

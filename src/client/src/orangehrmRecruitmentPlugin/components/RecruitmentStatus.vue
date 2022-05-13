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
    <div class="orangehrm-timesheet-header">
      <div class="orangehrm-timesheet-header--title">
        <slot name="header-title"></slot>
      </div>
      <div class="orangehrm-timesheet-header--options">
        <slot name="header-options"></slot>
      </div>
    </div>
    <div class="orangehrm-timesheet-body">
      <table class="orangehrm-timesheet-table">
        <thead class="orangehrm-timesheet-table-header">
          <tr class="orangehrm-timesheet-table-header-row">
            <th class="orangehrm-timesheet-table-header-cell">
              Name
            </th>
            <th class="orangehrm-timesheet-table-header-cell">
              Vacancy
            </th>
            <th class="orangehrm-timesheet-table-header-cell">
              Hiring Manager
            </th>
          </tr>
        </thead>

        <tbody class="orangehrm-timesheet-table-body">
          <!-- timesheet activities -->
          <tr class="orangehrm-timesheet-table-body-row">
            <td
              v-for="(text, index) in data"
              :key="index"
              class="orangehrm-timesheet-table-header-cell"
            >
              {{ text }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="orangehrm-timesheet-footer">
      <div class="orangehrm-timesheet-footer--title">
        <slot name="footer-title"></slot>
      </div>
      <div class="orangehrm-timesheet-footer--options">
        <slot name="footer-options"></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RecruitmentStatus',
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style
  src="@/orangehrmTimePlugin/components/timesheet.scss"
  lang="scss"
  scoped
></style>
<style scoped lang="scss">
.orangehrm-timesheet-table-header-row {
  border-bottom: none;
}
</style>

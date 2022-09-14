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
  <base-widget
    icon="ui-checks"
    icon-type="svg"
    :empty="isEmpty"
    :empty-text="emptyText"
    :loading="isLoading"
    :title="$t('general.to_do')"
  >
    <div class="orangehrm-todo-list">
      <div class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="clock"
          display-type="success"
        />
        <oxd-text tag="p">
          (2) Leave Request To Aprove
        </oxd-text>
      </div>
      <div class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="calendar3"
          display-type="warn"
        />
        <oxd-text tag="p">
          (2) Timesheet to aprove
        </oxd-text>
      </div>
      <div class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="person-fill"
          display-type="danger"
        />
        <oxd-text tag="p">
          (2) Performance Review to aprove
        </oxd-text>
      </div>
      <div class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="people-fill"
          display-type="info"
        />
        <oxd-text tag="p">
          (2) candidate to interview
        </oxd-text>
      </div>
    </div>
  </base-widget>
</template>
<script>
import {APIService} from '@/core/util/services/api.service';
import BaseWidget from '@/orangehrmDashboardPlugin/components/BaseWidget.vue';

export default {
  name: 'MyActionSummeryViewWidget',

  components: {
    'base-widget': BaseWidget,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/dashboard/',
    );

    return {
      http,
    };
  },

  beforeMount() {
    this.isLoading = false;
  },
};
</script>
<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-todo-list{
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;

   &-item{
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;

    & p{
        font-size: 12px;
        margin-left: 0.5rem;
    }
   } 
}
</style>

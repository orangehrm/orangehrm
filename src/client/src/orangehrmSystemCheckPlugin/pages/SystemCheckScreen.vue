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
  <oxd-form
    :loading="isLoading"
    :class="
      isLoading
        ? 'orangehrm-system-check-form-loading'
        : 'orangehrm-system-check-form'
    "
  >
    <div class="orangehrm-system-check">
      <oxd-text tag="h5" class="orangehrm-system-check-title">
        System Check
      </oxd-text>
      <br />
      <oxd-text class="orangehrm-system-check-content">
        To properly function the system, please ensure that all of the system
        check items listed below are green. If any are red, please take the
        necessary steps to fix them.
      </oxd-text>
      <br />
      <oxd-text
        v-if="error?.message"
        class="orangehrm-system-check-content --error"
      >
        An unexpected error occurred. Please provide the file write permission
        to <b>/src/log</b> directory and check the error log in
        <b>/src/log/orangehrm.log</b> file for more details.
      </oxd-text>
      <flex-table
        v-for="item in items"
        :key="item.category"
        :items="item.checks"
        :title-name="item.category"
      ></flex-table>
      <oxd-form-actions class="orangehrm-system-check-action">
        <oxd-button
          class="orangehrm-left-space"
          display-type="ghost"
          label="Re-Check"
          type="submit"
          @click="reCheck"
        />
      </oxd-form-actions>
    </div>
    <slot name="footer"></slot>
  </oxd-form>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import FlexTable from '@/orangehrmSystemCheckPlugin/components/FlexTable';
export default {
  name: 'SystemCheckScreen',
  components: {
    'flex-table': FlexTable,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/core/system-check`,
    );
    return {
      http,
    };
  },
  data() {
    return {
      items: [],
      isLoading: false,
      isInterrupted: false,
      error: null,
    };
  },
  beforeMount() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.isLoading = true;
      this.http
        .getAll()
        .then((response) => {
          const {data, meta} = response.data;
          this.items = data;
          this.isInterrupted = meta.isInterrupted;
          this.error = meta.error;
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    reCheck() {
      this.fetchData();
    },
  },
};
</script>

<style scoped lang="scss">
.orangehrm-system-check {
  font-size: $oxd-input-control-font-size;
  &-title {
    font-weight: 700;
    color: $oxd-primary-one-color;
  }
  &-content {
    &.--error {
      color: $oxd-feedback-danger-color;
    }
  }
  &-action {
    padding: 1rem 0;
  }
  &-form {
    margin: 5%;
    &-loading {
      height: 100%;
    }
  }
}
</style>

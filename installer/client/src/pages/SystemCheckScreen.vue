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
  <oxd-form :loading="isLoading">
    <div class="orangehrm-system-check orangehrm-upgrader-container">
      <oxd-text
        tag="h5"
        class="orangehrm-system-check-content orangehrm-upgrader-container-content orangehrm-system-check-title"
      >
        System Check
      </oxd-text>
      <oxd-text
        class="orangehrm-system-check-content orangehrm-upgrader-container-content"
        >In order for your OrangeHRM installation to function properly, please
        ensure that all of the system check items listed below are green. If any
        are red, please take the necessary steps to fix them.</oxd-text
      >
      <div v-if="items">
        <flex-table
          v-for="item in items"
          :key="item?.category"
          :items="item?.checks"
          :title-name="item?.category"
        ></flex-table>
      </div>
      <oxd-form-actions
        class="orangehrm-system-check-action orangehrm-upgrader-container-action"
      >
        <oxd-button display-type="ghost" label="Back" @click="navigateUrl" />
        <oxd-button
          display-type="ghost"
          label="Re-Check"
          type="submit"
          @click="reCheck"
        />
        <oxd-button
          display-type="ghost"
          :disabled="interrupted"
          label="Next"
          @click="goToScreen()"
        />
      </oxd-form-actions>
    </div>
  </oxd-form>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import FlexTable from '@/components/FlexTable.vue';
import {navigate} from '@/core/util/helper/navigation.ts';

export default {
  name: 'SystemCheckScreen',
  components: {
    'flex-table': FlexTable,
  },
  setup() {
    const http = new APIService(
      'https://884b404a-f4d0-4908-9eb5-ef0c8afec15c.mock.pstmn.io',
      'upgrader/systemChecks',
    );
    return {
      http,
    };
  },
  data() {
    return {
      selected: 'Orange',
      items: null,
      isLoading: false,
      isInterrupted: true,
    };
  },
  computed: {
    interrupted: {
      get() {
        return this.isInterrupted;
      },
    },
  },
  beforeMount() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.isLoading = true;
      this.http
        .getAll()
        .then(({data}) => {
          this.items = data.data;
          this.isInterrupted = data.meta.isInterrupted;
          this.isLoading = false;
        })
        .catch(() => {
          this.isLoading = false;
        });
    },
    navigateUrl() {
      navigate('/upgrader/database-config');
    },
    goToScreen() {
      navigate('/upgrader/current-version');
    },
    reCheck() {
      this.fetchData();
    },
  },
};
</script>

<style src="./installer-page.scss" lang="scss" scoped></style>
<style scoped lang="scss">
.orangehrm-system-check {
  &-title {
    padding-top: 0;
    color: $oxd-primary-one-color;
  }
  &-action {
    padding-right: 0;
    button {
      margin-right: 0.5rem;
    }
  }
}
</style>

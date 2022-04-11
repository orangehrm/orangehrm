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
  <installer-layout>
    <oxd-form :loading="isLoading">
      <div class="orangehrm-system-check">
        <oxd-text
          tag="h4"
          class="orangehrm-system-check-content orangehrm-system-check-title"
          >System Check</oxd-text
        >
        <oxd-text class="orangehrm-system-check-content"
          >In order for your orangeHRM installation to function properly,please
          ensure that all the system check items listed below are green.if any
          are red please take the necessary steps to fix them.</oxd-text
        >
        <flex-table
          :title-name="items?.environment.title"
          :items="items?.environment.items"
        >
        </flex-table>

        <flex-table
          :title-name="items?.permission.title"
          :items="items?.permission.items"
        >
        </flex-table>
        <flex-table
          :title-name="items?.extension.title"
          :items="items?.extension.items"
        >
        </flex-table>
      </div>
      <oxd-form-actions class="orangehrm-system-check-action">
        <oxd-button display-type="ghost" label="Back" />
        <oxd-button display-type="ghost" label="Re-Check" type="submit" />
        <oxd-button
          display-type="ghost"
          :disabled="checkErrors"
          label="Install"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </installer-layout>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import FlexTable from '../components/FlexTable.vue';
import InstallerLayout from '@/components/InstallerLayout';

export default {
  name: 'SystemCheckScreen',
  components: {
    'flex-table': FlexTable,
    'installer-layout': InstallerLayout,
  },
  setup() {
    const http = new APIService(
      'https://8fdc0dda-8987-4f6f-9014-cb8c49a3a717.mock.pstmn.io',
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
    };
  },
  computed: {
    checkErrors() {
      if (this.items) {
        const itemArr = [
          ...this.items?.environment?.items,
          ...this.items?.permission?.items,
          ...this.items?.extension?.items,
        ];
        return !itemArr?.every(({type}) => type !== 3);
      }
      return false;
    },
  },
  mounted() {
    this.isLoading = true;
    this.http
      .request({method: 'get'})
      .then(res => {
        this.items = res.data;
        this.isLoading = false;
      })
      .catch(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style scoped lang="scss">
.orangehrm-system-check {
  height: 100%;
  font-size: $oxd-input-control-font-size;
  &-action {
    padding: 1rem;
  }
  &-title {
    padding-top: 0;
    color: $oxd-primary-one-color;
  }
  &-content {
    padding: 0.75rem;
  }
  &-action {
    padding-right: 0;
    button {
      margin-left: 1rem;
    }
  }
}
</style>

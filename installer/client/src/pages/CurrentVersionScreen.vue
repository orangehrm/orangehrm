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
  <oxd-form class="orangehrm-current-version orangehrm-upgrader-container">
    <oxd-text tag="h5" class="orangehrm-current-version-title">
      Current Version Details
    </oxd-text>

    <oxd-text
      class="orangehrm-current-version-content orangehrm-upgrader-container-content"
    >
      Select your current OrangeHRM version here.You can't find the version at
      the bottom of OrangeHRM login page.OrangeHRM upgrader only supports
      versions listed in the dropdown selecting a different version would lead
      to an upgrader failure and a database corruption.
    </oxd-text>

    <oxd-form-row class="orangehrm-current-version-row">
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <oxd-input-field
            v-model="config.version"
            type="select"
            label="Current OrangeHRM Version"
            :options="getItems"
            :show-empty-selector="false"
            required
          ></oxd-input-field>
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>

    <Notice title="Notice" class="orangehrm-upgrader-container-notice">
      <oxd-text tag="p">
        If you have enabled data encrypted in your current version, you need to
        copy the file 'lib/confs/key.ohrm' from your current installation to
        corresponding location in the new version
      </oxd-text>
    </Notice>

    <oxd-text
      tag="p"
      class="orangehrm-current-version-content orangehrm-upgrader-container-content"
    >
      Click <b>Next</b> to commence upgrading your instance
    </oxd-text>

    <oxd-form-actions
      class="orangehrm-current-version-action orangehrm-upgrader-container-action"
    >
      <required-text />
      <oxd-button display-type="ghost" label="Back" @click="navigateUrl" />
      <oxd-button display-type="secondary" label="Next" type="submit" />
    </oxd-form-actions>
  </oxd-form>
</template>

<script>
import Notice from '@/components/Notice.vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation.ts';

export default {
  name: 'CurrentVersionScreen',
  components: {
    Notice,
  },
  setup() {
    const http = new APIService(
      'https://8fdc0dda-8987-4f6f-9014-cb8c49a3a717.mock.pstmn.io',
      'upgrader/current-version',
    );
    return {
      http,
    };
  },
  data() {
    return {
      config: {
        version: '',
      },
      items: () => [],
    };
  },
  computed: {
    getItems() {
      if (this.items.length == 0) {
        return [];
      }
      return JSON.parse(JSON.stringify(this.items));
    },
  },
  mounted() {
    this.http.request({method: 'get'}).then((res) => {
      this.items = res.data;
    });
  },
  methods: {
    navigateUrl() {
      navigate('/upgrader/system-check');
    },
  },
};
</script>
<style src="./installer-page.scss" lang="scss" scoped></style>
<style lang="scss" scoped>
.orangehrm-current-version {
  &-action {
    button {
      margin-left: 0.5rem;
    }
  }
  &-title {
    color: $oxd-primary-one-color;
    padding: 0 0.75rem 0.75rem 0.75rem;
  }
  ::v-deep(.oxd-grid-3) {
    width: 100%;
    margin: 0 !important;
  }
}
</style>

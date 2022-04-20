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
  <oxd-form
    class="orangehrm-database-info orangehrm-upgrader-container"
    :loading="isLoading"
    @submit-valid="onSubmit"
  >
    <oxd-text tag="h5" class="orangehrm-database-info-title">
      Database Information
    </oxd-text>
    <oxd-text
      class="orangehrm-database-info-content orangehrm-upgrader-container-content"
    >
      Please provide the database information of the database you are going to
      upgrade.
    </oxd-text>
    <Notice
      title="important"
      class="orangehrm-database-info-note orangehrm-upgrader-container-notice"
    >
      <oxd-text tag="p" class="orangehrm-current-version-content">
        Make sure it's a copy of the database of your current OrangeHRM
        installation and not the original database. It's highly discouraged to
        use the original database for upgrading since it won't be recoverable if
        an error occurred during the upgrade.
      </oxd-text>
    </Notice>
    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <oxd-input-field
            v-model="database.dbHost"
            label="Database Host Name"
            :rules="rules.dbHost"
            required
          />
        </oxd-grid-item>
        <oxd-grid-item class="orangehrm-database-info-port-grid">
          <oxd-input-field
            v-model="database.dbPort"
            label="Database Host Port"
            :rules="rules.dbPort"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>
    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <oxd-input-field
            v-model="database.dbName"
            label="Database Name"
            :rules="rules.dbName"
            required
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>
    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <oxd-input-field
            v-model="database.dbUser"
            label="Database Username"
            :rules="rules.dbUser"
            required
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-field
            v-model="database.dbPassword"
            label="Database User Password"
            type="password"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>
    <oxd-text
      class="orangehrm-database-info-content orangehrm-upgrader-container-content"
    >
      Click <b>Next</b>
      to continue
    </oxd-text>
    <oxd-text
      v-if="errorMessage"
      class="orangehrm-database-info-content --error orangehrm-upgrader-container-content"
    >
      {{ errorMessage }}
    </oxd-text>
    <oxd-form-actions
      class="orangehrm-database-info-action orangehrm-upgrader-container-action"
    >
      <required-text />
      <oxd-button
        class="orangehrm-database-info-button"
        display-type="ghost"
        label="Back"
        type="button"
        @click="navigateUrl"
      />
      <oxd-button
        class="orangehrm-database-info-button"
        display-type="secondary"
        label="Next"
        type="submit"
      />
    </oxd-form-actions>
  </oxd-form>
</template>

<script>
import {required, validRange} from '@/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation.ts';
import Notice from '@/components/Notice.vue';
export default {
  name: 'DatabaseConfigScreen',
  components: {
    Notice,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/upgrader/api/database-config',
    );
    return {
      http,
    };
  },
  data() {
    return {
      rules: {
        dbHost: [required],
        dbPort: [required, validRange(5, 0, 65535)],
        dbName: [required],
        dbUser: [required],
      },
      isLoading: false,
      database: {
        dbHost: null,
        dbPort: null,
        dbName: null,
        dbUser: null,
        dbPassword: null,
      },
      errorMessage: '',
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http.getAll().then(({data: {data}}) => {
      this.database = data;
      this.database.dbPassword = null;
      this.isLoading = false;
    });
  },
  methods: {
    onSubmit() {
      this.errorMessage = '';
      this.isLoading = true;
      const {dbHost, dbPort, dbName, dbUser, dbPassword} = this.database;
      this.http
        .create({dbHost, dbPort, dbName, dbUser, dbPassword})
        .then(() => {
          navigate('/upgrader/system-check');
        })
        .catch((err) => {
          this.isLoading = false;
          this.errorMessage =
            err.response.data.error?.message || err.response.data.error;
        });
    },
    navigateUrl() {
      navigate('/welcome');
    },
  },
};
</script>
<style src="./installer-page.scss" lang="scss" scoped></style>
<style lang="scss" scoped>
.orangehrm-database-info {
  &-port {
    &-grid {
      ::v-deep(.oxd-input) {
        width: 50%;
      }
    }
  }
  &-content {
    &.--error {
      color: $oxd-feedback-danger-color;
      padding: 1rem;
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
  &-action {
    button {
      margin-left: 0.5rem;
    }
  }
}
</style>

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
  <oxd-form class="orangehrm-database-info" @submit-valid="onSubmit">
    <oxd-text tag="h4" class="orangehrm-database-info-title"
      >Database Information</oxd-text
    >
    <oxd-text class="orangehrm-database-info-content"
      >Please provide the database information of the database you are going to
      upgrade.</oxd-text
    >
    <Notice title="important">
      <oxd-text tag="p" class="orangehrm-current-version-content">
        If yey.ohrmou have enabled data encrypted in your current version, you
        need to copy the file 'lib/confs/key.ohrm' from your current
        installation to corresponding location in the new version
      </oxd-text>
    </Notice>
    <oxd-text class="orangehrm-database-info-content"
      >Make sure it's copy of the database of your current OrangeHRm
      installation and not the original database.it's highly discouraged to use
      the original database for upgrading since it won't be recoverable if an
      error occurred during the upgrade.</oxd-text
    >

    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <oxd-input-field
            v-model="database.hostName"
            label="Database Host Name"
            :rules="rules.hostName"
            required
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-field
            v-model="database.hostPort"
            label="Database Host Port"
            :rules="rules.hostPort"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>
    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <oxd-input-field
            v-model="database.databaseName"
            label="Database Name"
            :rules="rules.databaseName"
            required
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>
    <oxd-form-row>
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <oxd-input-field
            v-model="database.userName"
            label="Database UserName"
            :rules="rules.userName"
            required
          />
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-field
            v-model="database.userPassword"
            label="Database User Password"
            :rules="rules.userPassword"
            type="password"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>
    <oxd-text class="orangehrm-database-info-content"
      >Click
      <b>Next</b>
      to continue</oxd-text
    >
    <oxd-form-actions class="orangehrm-database-info-action">
      <required-text />
      <oxd-button
        class="orangehrm-database-info-button"
        display-type="ghost"
        label="Back"
        type="button"
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
import {
  digitsOnly,
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import Notice from '@/components/Notice.vue';
export default {
  name: 'DatabaseConfigScreen',
  components: {
    Notice,
  },
  setup() {
    const http = new APIService(
      'https://8fdc0dda-8987-4f6f-9014-cb8c49a3a717.mock.pstmn.io',
      'upgrader/databaseConfig',
    );
    return {
      http,
    };
  },
  data() {
    return {
      rules: {
        hostName: [required],
        hostPort: [required, digitsOnly],
        databaseName: [required],
        userName: [required],
        userPassword: [required, shouldNotExceedCharLength(64)],
      },
      database: {
        hostName: '',
        hostPort: '',
        databaseName: '',
        userName: '',
        userPassword: '',
      },
    };
  },
  methods: {
    onSubmit() {
      const {hostName, hostPort, databaseName, userName, userPassword} =
        this.database;
      this.http.request({
        method: 'post',
        data: {hostName, hostPort, databaseName, userName, userPassword},
      });
    },
  },
};
</script>
<style src="./installer-page.scss" lang="scss" scoped></style>
<style lang="scss" scoped>
.orangehrm-database-info {
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

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
  <div class="orangehrm-background-container">
    <oxd-text
      tag="h4"
      class="orangehrm-database-info-title orangehrm-database-info-content"
      >Database Information</oxd-text
    >
    <oxd-text class="orangehrm-database-info-content"
      >Please provide the database information of the database you are going to
      upgrade.</oxd-text
    >
    <oxd-text class="orangehrm-database-info-content"
      >Make sure it's copy of the database of your current OrangeHRm
      installation and not the original database.it's highly discouraged to use
      the original database for upgrading since it won't be recoverable if an
      error occurred during the upgrade.</oxd-text
    >
    <oxd-form ref="databaseForm" method="post">
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
        <oxd-text class="orangehrm-database-info-content-bold" tag="span"
          >Next</oxd-text
        >
        to continue</oxd-text
      >
      <oxd-form-actions class="orangehrm-database-info-action">
        <required-text />
        <oxd-button
          class="orangehrm-database-info-button"
          display-type="secondary"
          label="Next"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import {checkPassword} from '@ohrm/core/util/helper/password';
export default {
  name: 'DatabaseInformationScreen',
  data() {
    return {
      rules: {
        hostName: [required],
        hostPort: [required],
        databaseName: [required],
        userName: [required],
        userPassword: [required, shouldNotExceedCharLength(64), checkPassword],
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
};
</script>

<style scoped lang="scss">
.orangehrm-database-info {
  &-action {
    padding: 1rem;
  }
  &-title {
    color: $oxd-primary-one-color;
  }
  &-content {
    padding: 0.75rem;
    &-bold {
      font-weight: 700;
    }
    &--note {
      padding-top: 0;
      padding-left: 2rem;
    }
  }
}
::v-deep(.oxd-radio-wrapper label) {
  font-size: 16px;
  font-weight: 700;
}
</style>

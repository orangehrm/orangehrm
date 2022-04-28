<!--
  - OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  - all the essential functionalities required for any enterprise.
  - Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
  -
  - OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  - the GNU General Public License as published by the Free Software Foundation; either
  - version 2 of the License, or (at your option) any later version.
  -
  - OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  - without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  - See the GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License along with this program;
  - if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  - Boston, MA  02110-1301, USA
  -->
<template>
  <oxd-form
    class="orangehrm-installer-page"
    :loading="isLoading"
    @submit-valid="onSubmit"
  >
    <oxd-text tag="h5" class="orangehrm-installer-page-title">
      Confirmation
    </oxd-text>
    <br />
    <oxd-text tag="p" class="orangehrm-installer-page-content">
      All the information required for OrangeHRM installation collected in the
      earlier steps are given below. On confirmation the installer will create
      the database, database users, configuration file, etc
    </oxd-text>
    <br />
    <oxd-text class="confirmation-text-header">Details</oxd-text>
    <br />
    <oxd-text class="confirmation-text-section"> Host Name </oxd-text>
    <oxd-text class="confirmation-text-info">{{ database.dbHost }}</oxd-text>
    <br />
    <oxd-text class="confirmation-text-section"> Database Host Port </oxd-text>
    <oxd-text class="confirmation-text-info">{{ database.dbPort }}</oxd-text>
    <br />
    <oxd-text class="confirmation-text-section"> Database Name </oxd-text>
    <oxd-text class="confirmation-text-info">{{ database.dbName }}</oxd-text>
    <br />
    <oxd-text class="confirmation-text-section">
      Privileged Database Username
    </oxd-text>
    <oxd-text class="confirmation-text-info">{{ database.dbUser }}</oxd-text>
    <br />
    <oxd-text class="confirmation-text-section">
      OrangeHRM Admin Username
    </oxd-text>
    <oxd-text class="confirmation-text-info">{{ adminUserName }}</oxd-text>
    <br />
    <br />
    <oxd-text class="orangehrm-installer-page-content">
      Click <b>Install</b> to continue
    </oxd-text>
    <oxd-form-actions class="orangehrm-installer-page-action">
      <oxd-button
        display-type="ghost"
        label="Back"
        type="button"
        @click="navigateUrl"
      />
      <oxd-button
        class="orangehrm-left-space"
        display-type="secondary"
        label="Install"
        type="submit"
      />
    </oxd-form-actions>
  </oxd-form>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';

const databaseModel = {
  dbHost: '',
  dbPort: '',
  dbName: '',
  dbUser: '',
};

export default {
  name: 'ConfirmationScreen',
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/installer/api/database-config',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      database: {...databaseModel},
      adminUserName: '',
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        const {data} = response.data;
        this.database = {...databaseModel, ...data};
        return this.http.request({
          method: 'GET',
          url: '/installer/api/admin-user',
        });
      })
      .then((response) => {
        const {data} = response.data;
        this.adminUserName = data.username;
        this.isLoading = false;
      });
  },
  methods: {
    onSubmit() {
      navigate('/installer/process');
    },
    navigateUrl() {
      navigate('/installer/admin-user-creation');
    },
  },
};
</script>

<style src="./installer-page.scss" lang="scss" scoped></style>
<style lang="scss" scoped>
.confirmation-text-header {
  font-weight: 700;
  font-size: 20px;
}

.confirmation-text-section {
  font-weight: 700;
}

.confirmation-text-info {
  font-size: 16px;
}
</style>

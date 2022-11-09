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
    class="orangehrm-installer-page"
    :loading="isLoading"
    @submit-valid="onSubmit"
  >
    <oxd-text tag="h5" class="orangehrm-installer-page-title">
      License Acceptance
    </oxd-text>
    <br />
    <oxd-text class="orangehrm-installer-page-content">
      Please review the licence terms before installing OrangeHRM Starter.
    </oxd-text>
    <br />

    <oxd-form-row>
      <gnu-licence></gnu-licence>
    </oxd-form-row>
    <br />

    <oxd-text tag="p" class="orangehrm-installer-page-content">
      If you accept the terms of the agreement, select the first option below.
      You must accept the agreement to install OrangeHRM. Click <b>Next</b> to
      continue
    </oxd-text>

    <br />
    <oxd-form-row>
      <oxd-input-field
        v-model="userConsent"
        type="checkbox"
        option-label="I accept the terms in the Licence Agreement"
      />
    </oxd-form-row>

    <oxd-form-actions class="orangehrm-installer-page-action">
      <oxd-button display-type="ghost" label="Back" @click="onClickBack" />
      <oxd-button
        :disabled="!userConsent"
        class="orangehrm-left-space"
        display-type="secondary"
        label="Next"
        type="submit"
      />
    </oxd-form-actions>
  </oxd-form>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation.ts';
import GNULicence from '@/components/GNULicence.vue';

export default {
  name: 'LicenceAcceptanceScreen',
  components: {
    'gnu-licence': GNULicence,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'installer/api/license',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      userConsent: false,
    };
  },
  methods: {
    onClickBack() {
      navigate('/welcome');
    },
    onSubmit() {
      navigate('/installer/database-config');
    },
  },
};
</script>
<style src="./installer-page.scss" lang="scss" scoped></style>

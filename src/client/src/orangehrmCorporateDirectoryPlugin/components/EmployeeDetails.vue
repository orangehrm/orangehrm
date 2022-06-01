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
  <oxd-divider></oxd-divider>
  <div class="orangehrm-directory-card-rounded-body">
    <div v-show="employeeWorkTelephone" class="orangehrm-directory-card-icon">
      <a :href="`tel:${employeeWorkTelephone}`">
        <oxd-icon-button
          display-type="success"
          name="telephone-fill"
        ></oxd-icon-button>
      </a>
    </div>
    <div v-show="employeeWorkEmail" class="orangehrm-directory-card-icon">
      <a :href="`tel:${employeeWorkEmail}`">
        <oxd-icon-button display-type="danger" name="mailbox"></oxd-icon-button>
      </a>
    </div>
  </div>
  <div
    class="orangehrm-directory-card-hover"
    @mouseleave="showTelephoneClip = false"
    @mouseover="showTelephoneClip = true"
  >
    <div class="orangehrm-directory-card-hover-body">
      <oxd-text type="toast-message">{{ $t('Work Telephone') }}</oxd-text>
      <oxd-text ref="cloneTelephone" type="toast-title">
        {{ employeeWorkTelephone }}
      </oxd-text>
    </div>
    <div
      class="orangehrm-directory-card-hover-body orangehrm-directory-card-icon"
    >
      <oxd-icon-button
        v-show="showTelephoneClip"
        name="clipboard-check"
        @click="copyTelephone"
      ></oxd-icon-button>
    </div>
  </div>
  <oxd-divider></oxd-divider>
  <div
    class="orangehrm-directory-card-hover"
    @mouseleave="showEmailClip = false"
    @mouseover="showEmailClip = true"
  >
    <div class="orangehrm-directory-card-hover-body">
      <oxd-text type="toast-message">{{ $t('Work Email') }}</oxd-text>
      <oxd-text ref="cloneEmail" type="toast-title">
        {{ employeeWorkEmail }}
      </oxd-text>
    </div>
    <div
      class="orangehrm-directory-card-hover-body orangehrm-directory-card-icon"
    >
      <oxd-icon-button
        v-show="showEmailClip"
        name="clipboard-check"
        @click="copyEmail"
      ></oxd-icon-button>
    </div>
  </div>
  <oxd-divider></oxd-divider>
</template>

<script>
import OxdDivider from '@ohrm/oxd/core/components/Divider/Divider';
import {APIService} from '@/core/util/services/api.service';

export default {
  name: 'EmployeeDetails',
  components: {
    'oxd-divider': OxdDivider,
  },
  props: {
    employeeId: {
      type: Number,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      'https://07bd2c2f-bd2b-4a9f-97c7-cb744a96e0f8.mock.pstmn.io',
      'api/v2/corporate-directory/employees',
    );
    return {
      http,
    };
  },
  data() {
    return {
      employeeWorkTelephone: null,
      employeeWorkEmail: null,
      showTelephoneClip: false,
      showEmailClip: false,
    };
  },
  beforeMount() {
    this.http.get(this.employeeId).then(response => {
      const {data} = response.data;
      this.employeeWorkTelephone = data.contactInfo?.workTelephone;
      this.employeeWorkEmail = data.contactInfo?.workEmail;
    });
  },
  methods: {
    copyEmail() {
      navigator.clipboard?.writeText(this.employeeWorkEmail);
    },
    copyTelephone() {
      navigator.clipboard?.writeText(this.employeeWorkTelephone);
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-directory-card {
  &-rounded-body {
    display: flex;
    justify-content: center;
    align-items: center;
    padding-right: 1rem;
    padding-left: 1rem;
    margin-top: 1rem;
    margin-bottom: 1rem;
    border-radius: 100px;
    width: 140px;
    height: 64px;
    box-shadow: 5px 5px 5px 5px #fafafc;
    margin-right: 8px;
  }

  &-hover {
    display: flex;
    justify-content: space-between;
    padding: 0.25rem 0.5rem;
    border-radius: 0.5rem;
    width: 156px;
    min-height: 48px;
    margin-right: 8px;

    &-body {
      display: block;
      align-items: center;
      overflow: hidden;
      word-wrap: break-word;
    }
  }

  &-hover:hover {
    background-color: #fafafc;
  }

  &-icon {
    margin-right: 0.5rem;
    color: #64728c;
    font-size: 24px;
    display: flex;
    justify-content: center;
  }
}

@media (max-width: 600px) {
  .orangehrm-directory-card {
    padding: 0.5rem 1rem;
    height: auto;
    overflow: hidden;

    &-rounded-body {
      display: flex;
      justify-content: center;
      align-items: center;
      padding-right: 1rem;
      padding-left: 1rem;
      margin-top: 1rem;
      margin-bottom: 1rem;
      border-radius: 100px;
      width: auto;
      height: 64px;
      box-shadow: 5px 5px 5px 5px #fafafc;
      margin-right: 8px;
    }

    &-hover {
      display: flex;
      justify-content: space-between;
      padding: 0.25rem 0.5rem;
      border-radius: 0.5rem;
      width: auto;
      min-height: 48px;
      margin-right: 8px;

      &-body {
        display: block;
        align-items: center;
        overflow: hidden;
        word-wrap: break-word;
      }
    }

    &-hover:hover {
      background-color: #fafafc;
    }
  }
}
</style>

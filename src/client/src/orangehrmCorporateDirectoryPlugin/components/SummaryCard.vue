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
  <oxd-sheet
    :gutters="false"
    class="orangehrm-directory-card"
    type="white"
    @click="showEmployeeDetailsFn(id)"
  >
    <div class="orangehrm-directory-card-header">
      <oxd-text type="card-title">
        {{ employeeName }}
      </oxd-text>
    </div>
    <profile-picture :id="id"></profile-picture>
    <div class="orangehrm-directory-card-header">
      <oxd-text type="toast-title">
        {{ employeeDesignation }}
      </oxd-text>
    </div>
    <div class="orangehrm-directory-card-body">
      <span class="orangehrm-directory-card-icon">
        <oxd-icon name="geo-alt-fill"></oxd-icon>
      </span>
      <span>
        <div class="orangehrm-directory-card-subunit">
          <oxd-text type="toast-message">
            {{ employeeSubUnit }}
          </oxd-text>
        </div>
        <div class="orangehrm-directory-card-location">
          <oxd-text type="toast-message">
            {{ employeeLocation }}
          </oxd-text>
        </div>
      </span>
    </div>
    <div v-show="changedShowDetailsStatus && toggled">
      <employee-details
        :employee-work-email="employeeInfoDetails[0].employeeWorkEmail"
        :employee-work-telephone="employeeInfoDetails[0].employeeWorkTelephone"
      >
      </employee-details>
    </div>
  </oxd-sheet>
</template>

<script>
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
import ProfilePicture from '@/orangehrmCorporateDirectoryPlugin/components/ProfilePicture';
import EmployeeDetails from '@/orangehrmCorporateDirectoryPlugin/components/EmployeeDetails';
import {APIService} from '@/core/util/services/api.service';

const employeeInfoDetailsDataNormalizer = data => {
  return data.map(item => {
    return {
      employeeId: item.id,
      employeeWorkTelephone: item.contactInfo?.workTelephone,
      employeeWorkEmail: item.contactInfo?.workEmail,
    };
  });
};

export default {
  name: 'SummaryCard',
  components: {
    'oxd-sheet': Sheet,
    'oxd-icon': Icon,
    'profile-picture': ProfilePicture,
    'employee-details': EmployeeDetails,
  },
  props: {
    id: {
      type: Number,
      required: true,
    },
    employeeName: {
      type: String,
      required: true,
    },
    employeeDesignation: {
      type: String,
      required: true,
    },
    employeeSubUnit: {
      type: String,
      default: '',
    },
    employeeLocation: {
      type: String,
      default: '',
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
      employeeInfoDetails: [
        {
          employeeWorkTelephone: null,
          employeeWorkEmail: null,
        },
      ],
      toggled: false,
    };
  },

  computed: {
    changedShowDetailsStatus() {
      return this.toggled === true ? true : false;
    },
  },
  methods: {
    showEmployeeDetailsFn(id) {
      if (this.toggled === false) {
        this.http.get(id).then(response => {
          this.employeeInfoDetails = employeeInfoDetailsDataNormalizer(
            response.data.data,
          );
          this.toggled = true;
        });
      } else {
        this.toggled = false;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-directory-card {
  padding: 0.5rem 1rem;
  height: 260px;
  overflow: hidden;

  &-header {
    padding-top: 1rem;
    padding-bottom: 0.75rem;
    text-align: center;
    justify-content: space-between;
    height: 32px;
  }

  &-body {
    display: flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    margin-top: -0.25rem;
    background-color: #fafafc;
    border-radius: 0.5rem;
    height: 44px;
  }

  &-icon {
    margin-right: 0.5rem;
    color: #64728c;
    font-size: 24px;
    display: flex;
    justify-content: center;
  }

  &-subunit {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
  }

  &-location {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
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

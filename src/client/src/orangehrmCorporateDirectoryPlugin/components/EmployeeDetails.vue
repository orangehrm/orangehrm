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
    <div class="orangehrm-directory-card-icon">
      <oxd-icon-button display-type="success" name="telephone-fill">
      </oxd-icon-button>
    </div>
    <div class="orangehrm-directory-card-icon">
      <oxd-icon-button display-type="danger" name="mailbox"> </oxd-icon-button>
    </div>
  </div>
  <div
    class="orangehrm-directory-card-hover"
    @mouseleave="showTelephoneClip = false"
    @mouseover="showTelephoneClip = true"
  >
    <div class="orangehrm-directory-card-hover-body">
      <oxd-text type="toast-message">{{ $t('Work Telephone') }}</oxd-text>
      <oxd-text type="toast-title"> {{ employeeWorkTelephone }}</oxd-text>
    </div>
    <div
      class="orangehrm-directory-card-hover-body orangehrm-directory-card-icon"
    >
      <oxd-icon v-show="showTelephoneClip" name="clipboard-check"></oxd-icon>
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
      <oxd-text type="toast-title"> {{ employeeWorkEmail }}</oxd-text>
    </div>
    <div
      class="orangehrm-directory-card-hover-body orangehrm-directory-card-icon"
    >
      <oxd-icon v-show="showEmailClip" name="clipboard-check"></oxd-icon>
    </div>
  </div>
  <oxd-divider></oxd-divider>
</template>

<script>
import OxdDivider from '@ohrm/oxd/core/components/Divider/Divider';
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
export default {
  name: 'EmployeeDetails',
  components: {
    'oxd-icon': Icon,
    'oxd-divider': OxdDivider,
  },
  props: {
    employeeWorkTelephone: {
      type: String,
      default: null,
    },
    employeeWorkEmail: {
      type: String,
      default: null,
    },
  },
  data() {
    return {
      showTelephoneClip: false,
      showEmailClip: false,
    };
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

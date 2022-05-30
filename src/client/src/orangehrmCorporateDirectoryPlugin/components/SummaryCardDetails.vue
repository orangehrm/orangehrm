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
  <oxd-sheet :gutters="false" class="orangehrm-directory-card" type="white">
    <div
      class="orangehrm-directory-card-top"
      @click="$emit('hide-details', false)"
    >
      <oxd-icon name="arrow-right"></oxd-icon>
    </div>
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
    <div class="orangehrm-directory-card-rounded-body">
      <div class="orangehrm-directory-card-icon">
        <oxd-icon-button display-type="success" name="telephone-fill">
        </oxd-icon-button>
      </div>
      <div class="orangehrm-directory-card-icon">
        <oxd-icon-button display-type="danger" name="mailbox">
        </oxd-icon-button>
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
    <div class="orangehrm-directory-card-qrcode">
      <img
        class="orangehrm-directory-card-qrcode-img"
        src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/QR_code_for_mobile_English_Wikipedia.svg/800px-QR_code_for_mobile_English_Wikipedia.svg.png"
      />
    </div>
  </oxd-sheet>
</template>

<script>
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
import OxdDivider from '@ohrm/oxd/core/components/Divider/Divider';
import ProfilePicture from '@/orangehrmCorporateDirectoryPlugin/components/ProfilePicture';

export default {
  name: 'SummaryCardDetails',
  components: {
    'oxd-sheet': Sheet,
    'oxd-icon': Icon,
    'oxd-divider': OxdDivider,
    'profile-picture': ProfilePicture,
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
    employeeWorkTelephone: {
      type: String,
      default: '',
    },
    employeeWorkEmail: {
      type: String,
      default: '',
    },
  },
  emits: ['hide-details'],
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
  padding: 0.5rem 1rem;
  height: 640px;
  width: 164px;
  overflow: hidden;

  &-top {
    padding-top: 0.5rem;
    padding-bottom: -0.5rem;
    text-align: left;
    justify-content: space-between;
    height: 16px;
  }

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
    width: 156px;
    margin-right: 8px;
  }

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

  &-subunit {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
  }

  &-location {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
  }

  &-qrcode {
    height: 128px;
    width: 128px;
    display: block;
    align-items: center;
    margin-left: 1rem;

    &-img {
      height: 128px;
      width: 128px;
    }
  }
}
</style>

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
    <div class="orangehrm-card-container">
      <div class="orangehrm-edit-employee">
        <div class="orangehrm-edit-employee-navigation">
          <div class="orangehrm-edit-employee-imagesection">
            <div class="orangehrm-edit-employee-name">
              <oxd-text tag="h6" class="--strong">{{ employeeName }}</oxd-text>
              <oxd-text v-if="!isCurrentEmp" type="subtitle-2">
                (Past Employee)
              </oxd-text>
            </div>
            <div class="orangehrm-edit-employee-image-wrapper">
              <div
                class="orangehrm-edit-employee-image"
                @click="onClickProfilePic"
              >
                <img
                  alt="profile picture"
                  class="employee-image"
                  :src="imgSrc"
                />
              </div>
            </div>
          </div>
          <tabs-navigation :tabs="tabs"></tabs-navigation>
        </div>
        <div class="orangehrm-edit-employee-content">
          <slot></slot>
          <profile-custom-fields
            v-if="screen !== 'default'"
            :employee-id="employeeId"
            :screen="screen"
          ></profile-custom-fields>
          <profile-attachments
            v-if="screen !== 'default'"
            :employee-id="employeeId"
            :allowed-file-types="allowedFileTypes"
            :screen="screen"
          ></profile-attachments>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import TabsNavigation from '@/orangehrmPimPlugin/components/TabsNavigation';
import ProfileAttachments from '@/orangehrmPimPlugin/components/ProfileAttachments';
import ProfileCustomFields from '@/orangehrmPimPlugin/components/ProfileCustomFields';

const defaultPic = `${window.appGlobal.baseUrl}/../dist/img/user-default-400.png`;

export default {
  name: 'EditEmployeeLayout',
  components: {
    'tabs-navigation': TabsNavigation,
    'profile-attachments': ProfileAttachments,
    'profile-custom-fields': ProfileCustomFields,
  },
  props: {
    employeeId: {
      type: String,
      required: true,
    },
    tabs: {
      type: Array,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    screen: {
      type: String,
      default: 'default',
      validator(value) {
        return [
          'default',
          'personal',
          'contact',
          'emergency',
          'dependents',
          'immigration',
          'qualifications',
          'tax',
          'salary',
          'job',
          'report-to',
          'membership',
        ].includes(value);
      },
    },
  },
  setup(props) {
    const employeeName = ref('');
    const isCurrentEmp = ref(true);
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/pim/employees',
    );

    http.get(props.employeeId).then(({data}) => {
      employeeName.value = `${data.data.firstName} ${data.data.lastName}`;
      isCurrentEmp.value = data.data.terminationId ? false : true;
    });

    const imgSrc = computed(() => {
      return props.employeeId
        ? `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${props.employeeId}`
        : defaultPic;
    });

    const onClickProfilePic = () => {
      navigate('/pim/viewPhotograph/empNumber/{empNumber}', {
        empNumber: props.employeeId,
      });
    };

    return {
      imgSrc,
      employeeName,
      isCurrentEmp,
      onClickProfilePic,
    };
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-card-container {
  padding: unset;
  overflow: hidden;
}

.orangehrm-edit-employee {
  display: flex;
  @include oxd-respond-to('xs') {
    flex-direction: column;
  }
  @include oxd-respond-to('md') {
    flex-direction: row;
  }
  &-navigation {
    width: 100%;
    padding: 1rem;
    box-sizing: border-box;
    @include oxd-respond-to('md') {
      width: 220px;
    }
  }
  &-content {
    flex: 1;
    @include oxd-respond-to('md') {
      border-left: $oxd-input-control-border--active;
    }
  }
  &-name {
    text-align: center;
    padding-left: 1rem;
    padding-right: 1rem;
    word-break: break-word;
    & .--strong {
      font-weight: 700;
      font-size: 1.2rem;
    }
  }
  &-image-wrapper {
    padding-bottom: 1.2rem;
    @include oxd-respond-to('md') {
      padding-top: 1.2rem;
    }
  }
  &-image {
    width: 120px;
    height: 120px;
    border-radius: 100%;
    display: flex;
    cursor: pointer;
    overflow: hidden;
    justify-content: center;
    box-sizing: border-box;
    border: 0.5rem solid $oxd-background-pastel-white-color;
    box-shadow: 1px 1px 18px 11px hsl(238deg 13% 76% / 24%);
  }
  &-imagesection {
    display: flex;
    align-items: center;
    @include oxd-respond-to('xs') {
      flex-direction: row-reverse;
      justify-content: flex-end;
    }
    @include oxd-respond-to('md') {
      flex-direction: column;
      justify-content: center;
    }
  }
}
</style>

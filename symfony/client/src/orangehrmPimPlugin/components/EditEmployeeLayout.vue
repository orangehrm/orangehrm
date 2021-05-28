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
            <oxd-text class="orangehrm-edit-employee-name" tag="h6">{{
              employeeName
            }}</oxd-text>
            <div
              class="orangehrm-edit-employee-image"
              @click="onClickProfilePic"
            >
              <img alt="profile picture" class="employee-image" :src="imgSrc" />
            </div>
          </div>
          <tabs-navigation :tabs="tabs"></tabs-navigation>
        </div>
        <div class="orangehrm-edit-employee-content">
          <slot></slot>
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
import {navigate} from '@orangehrm/core/util/helper/navigation';
import TabsNavigation from '@/orangehrmPimPlugin/components/TabsNavigation';
import ProfileAttachments from '@/orangehrmPimPlugin/components/ProfileAttachments';

const defaultPic = `${window.appGlobal.baseUrl}/../dist/img/user-default-400.png`;

export default {
  name: 'edit-employee-layout',
  components: {
    'tabs-navigation': TabsNavigation,
    'profile-attachments': ProfileAttachments,
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
          'contract',
          'report-to',
          'membership',
        ].includes(value);
      },
    },
  },
  setup(props) {
    const employeeName = ref('');
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/pim/employees',
    );

    http.get(props.employeeId).then(({data}) => {
      employeeName.value = `${data.data.firstName} ${data.data.lastName}`;
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
      onClickProfilePic,
    };
  },
};
</script>

<style lang="scss" scoped>
@import '@orangehrm/oxd/styles/_mixins.scss';

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
    flex: 1 1 25%;
    padding: 1rem;
  }
  &-content {
    flex: 1 1 75%;
    @include oxd-respond-to('md') {
      border-left: $oxd-input-control-border--active;
    }
  }
  &-name {
    text-align: center;
    font-weight: 700;
    font-size: 1.2rem;
    padding-left: 1rem;
    padding-right: 1rem;
    padding-bottom: 1rem;
  }
  &-image {
    overflow: hidden;
    border: 0.75rem solid #e8eaef;
    border-radius: 100%;
    width: 6rem;
    height: 6rem;
    display: flex;
    justify-content: center;
    align-items: flex-end;
    flex-shrink: 0;
    cursor: pointer;
    .employee-image {
      height: 6rem;
    }
    @include oxd-respond-to('md') {
      width: 8rem;
      height: 8rem;
      .employee-image {
        height: 8rem;
      }
    }
  }
  &-imagesection {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
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

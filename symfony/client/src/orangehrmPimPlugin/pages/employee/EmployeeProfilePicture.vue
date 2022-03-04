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
  <edit-employee-layout :employee-id="empNumber">
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('pim.change_profile_picture') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <div class="orangehrm-employee-picture">
            <profile-image-input
              v-model="empPicture"
              :rules="rules.empPicture"
              :img-src="profilePicUrl"
            />
          </div>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </edit-employee-layout>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import ProfileImageInput from '@/orangehrmPimPlugin/components/ProfileImageInput';
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import {
  maxFileSize,
  required,
  validFileTypes,
} from '@/core/util/validation/rules';
const defaultPic = `${window.appGlobal.baseUrl}/../dist/img/user-default-400.png`;

export default {
  components: {
    'profile-image-input': ProfileImageInput,
    'edit-employee-layout': EditEmployeeLayout,
  },

  props: {
    empNumber: {
      type: String,
      required: true,
    },
    allowedImageTypes: {
      type: Array,
      required: true,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/pim/employees/${props.empNumber}/picture`,
    );

    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      empPicture: null,
      rules: {
        empPicture: [
          required,
          maxFileSize(1024 * 1024),
          validFileTypes(this.allowedImageTypes),
        ],
      },
    };
  },

  computed: {
    profilePicUrl() {
      if (this.empPicture) {
        const file = this.empPicture.base64;
        const type = this.empPicture.type;
        const isPicture = this.allowedImageTypes.findIndex(
          item => item === type,
        );
        return isPicture > -1 ? `data:${type};base64,${file}` : defaultPic;
      } else {
        return defaultPic;
      }
    },
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            empPicture: this.empPicture,
          },
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          location.reload();
        });
    },
  },
};
</script>

<style src="./employee.scss" lang="scss" scoped></style>

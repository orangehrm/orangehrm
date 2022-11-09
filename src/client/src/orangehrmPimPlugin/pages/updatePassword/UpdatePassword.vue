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
      <oxd-text tag="h6" class="orangehrm-main-title">{{
        $t('pim.update_password')
      }}</oxd-text>
      <oxd-divider />

      <oxd-form ref="formRef" :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-group :label="$t('general.username')">
                <oxd-text class="orangehrm-user-name" tag="p">
                  {{ userName }}
                </oxd-text>
              </oxd-input-group>
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-field
                v-model="user.currentPassword"
                type="password"
                :label="$t('pim.current_password')"
                :rules="rules.currentPassword"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <password-input
          v-model:password="user.password"
          v-model:passwordConfirm="user.passwordConfirm"
        />

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            type="button"
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import PasswordInput from '@/core/components/inputs/PasswordInput';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import useForm from '@/core/util/composable/useForm';

const userModel = {
  currentPassword: '',
  password: '',
  passwordConfirm: '',
};

export default {
  components: {
    'password-input': PasswordInput,
  },
  props: {
    userName: {
      type: String,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/core/update-password',
    );
    const {formRef, reset} = useForm();
    return {
      http,
      formRef,
      reset,
    };
  },

  data() {
    return {
      isLoading: false,
      user: {...userModel},
      rules: {
        currentPassword: [required, shouldNotExceedCharLength(64)],
      },
    };
  },

  methods: {
    onCancel() {
      window.history.back();
    },
    onSave() {
      this.isLoading = true;
      this.http.http
        .put('api/v2/pim/update-password', {
          currentPassword: this.user.currentPassword,
          newPassword: this.user.password,
        })
        .then(response => {
          if (response.status === 200) {
            this.$toast.saveSuccess();
          } else {
            this.isLoading = false;
            this.$toast.error({
              title: this.$t('general.error'),
              message: this.$t('pim.current_password_is_incorrect'),
            });
            return Promise.reject();
          }
        })
        .then(() => {
          this.isLoading = false;
          this.reset();
        });
    },
  },
};
</script>
<style src="./update-password.scss" lang="scss" scoped></style>

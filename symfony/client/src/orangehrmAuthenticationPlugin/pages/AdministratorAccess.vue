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
  <div class="orangehrm-admin-access-container">
    <div class="orangehrm-card-container">
      <oxd-form
        ref="verifyForm"
        method="post"
        :action="submitUrl"
        @submitValid="onSubmit"
      >
        <oxd-text tag="h6" class="orangehrm-admin-access-title">
          {{ $t('auth.admin_access') }}
        </oxd-text>

        <br />

        <div :class="noteContainerClass">
          <oxd-text tag="toast-message">
            {{ $t('auth.admin_access_note') }}
          </oxd-text>
        </div>

        <oxd-alert
          :show="error !== null"
          type="error"
          :message="error?.message"
        />

        <input name="_token" :value="token" type="hidden" />

        <oxd-form-row>
          <oxd-input-field
            :model-value="username"
            name="username"
            :label="$t('general.username')"
            label-icon="person"
            disabled
          />
        </oxd-form-row>
        <oxd-form-row>
          <oxd-input-field
            v-model="password"
            name="password"
            :label="$t('general.password')"
            label-icon="key"
            type="password"
            :rules="rules.password"
            autofocus
          />
        </oxd-form-row>
        <div class="orangehrm-admin-access-button-container">
          <oxd-button
            class="orangehrm-admin-access-button"
            display-type="ghost"
            size="large"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <oxd-button
            class="orangehrm-admin-access-button"
            display-type="secondary"
            size="large"
            :label="$t('general.confirm')"
            type="submit"
          />
        </div>
      </oxd-form>
    </div>
    <slot name="footer"></slot>
  </div>
</template>

<script>
import {urlFor} from '@ohrm/core/util/helper/url';
import {navigate} from '@/core/util/helper/navigation';
import {required} from '@/core/util/validation/rules';
import Alert from '@ohrm/oxd/core/components/Alert/Alert';

export default {
  name: 'AdministratorAccess',

  components: {
    'oxd-alert': Alert,
  },

  props: {
    username: {
      type: String,
      required: true,
    },
    error: {
      type: Object,
      default: () => null,
    },
    token: {
      type: String,
      required: true,
    },
    backUrl: {
      type: String,
      default: null,
    },
  },

  data() {
    return {
      password: '',
      rules: {
        password: [required],
      },
      noteClasses: {
        'orangehrm-admin-access-note': true,
        '--padding': this.error === null,
      },
      noteContainerClass: {
        'orangehrm-admin-access-note-container': this.error === null,
      },
    };
  },

  computed: {
    submitUrl() {
      return urlFor('/auth/adminVerify');
    },
  },

  methods: {
    onSubmit() {
      this.$refs.verifyForm.$el.submit();
    },
    onCancel() {
      navigate(this.backUrl);
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-card-container {
  box-shadow: 3px 3px 10px $oxd-interface-gray-color;
  width: 50vh;
}

.orangehrm-admin-access {
  &-container {
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: center;
    align-items: center;
  }
  &-title {
    font-weight: 700;
  }
  &-note-container {
    padding-bottom: 1.2rem;
  }
  &-button {
    flex: 1;
    margin-left: 0.6rem;
    margin-right: 0.6rem;
    &-container {
      display: flex;
      flex-direction: row;
      justify-content: center;
    }
  }
}
</style>

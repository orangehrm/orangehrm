<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
 -->

<template>
  <div class="orangehrm-oauth-container">
    <div class="orangehrm-card-container">
      <login-branding
        :img-src="loginBannerSrc"
        class="orangehrm-oauth-branding"
      />
      <oxd-divider />
      <template v-if="errorType === null">
        <oxd-text>
          {{ $t('auth.client_name_would_like_to', {clientName: clientName}) }}:
        </oxd-text>
        <ul class="orangehrm-oauth-list">
          <li>
            <oxd-text>{{ $t('auth.access_and_manage_your_data') }}</oxd-text>
          </li>
          <li>
            <oxd-text>
              {{ $t('auth.perform_actions_on_your_behalf') }}
            </oxd-text>
          </li>
        </ul>
        <oxd-text>{{ $t('auth.do_you_want_to_allow_access') }}</oxd-text>
        <br />
        <oxd-form
          ref="authorizeForm"
          method="GET"
          :action="submitUrl"
          @submit-valid="onSubmit"
        >
          <input name="authorized" :value="authorized" type="hidden" />
          <div v-for="(value, name) in params" :key="name">
            <input :name="name" :value="value" type="hidden" />
          </div>
          <div class="orangehrm-oauth-button-container">
            <oxd-button
              display-type="ghost"
              size="large"
              class="orangehrm-oauth-button"
              :label="$t('auth.deny')"
              @click="onCancel"
            />
            <oxd-button
              display-type="secondary"
              class="orangehrm-oauth-button"
              size="large"
              :label="$t('auth.allow_access')"
              type="submit"
            />
          </div>
        </oxd-form>
      </template>
      <template v-else-if="params['client_id'] === 'orangehrm_mobile_app'">
        <oxd-alert
          :show="true"
          type="error"
          :message="$t('auth.mobile_client_disabled_error')"
        />
      </template>
      <template v-else>
        <oxd-alert
          :show="true"
          type="error"
          :message="$t('auth.this_request_is_invalid')"
        />
        <oxd-text class="orangehrm-oauth-error">
          {{ $t('general.error') }}: {{ errorType }}
        </oxd-text>
      </template>
    </div>
    <slot name="footer"></slot>
  </div>
</template>

<script>
import {urlFor} from '@/core/util/helper/url';
import LoginBranding from '@/orangehrmAuthenticationPlugin/components/LoginBranding.vue';
import {OxdAlert} from '@ohrm/oxd';

export default {
  name: 'OAuthAuthorize',
  components: {
    'login-branding': LoginBranding,
    'oxd-alert': OxdAlert,
  },
  props: {
    params: {
      type: Object,
      required: true,
    },
    clientName: {
      type: String,
      default: null,
    },
    errorType: {
      type: String,
      default: null,
    },
    loginBannerSrc: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      authorized: true,
    };
  },
  computed: {
    submitUrl() {
      return urlFor('/oauth2/authorize/consent');
    },
  },
  methods: {
    onCancel() {
      this.authorized = false;
      this.onSubmit();
    },
    onSubmit() {
      this.$nextTick(() => {
        this.$refs.authorizeForm.$el.submit();
      });
    },
  },
};
</script>

<style src="./oauth-authorize.scss" scoped lang="scss"></style>

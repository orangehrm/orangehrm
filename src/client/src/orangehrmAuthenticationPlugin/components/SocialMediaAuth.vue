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
  <div>
    <oxd-text tag="p" class="orangehrm-social-auth-header">
      {{ $t('general.or_login_with') }}
    </oxd-text>
  </div>
  <div class="orangehrm-social-auth">
    <auth-button
      v-for="authenticator in socialAuthenticators"
      :key="authenticator.id"
      :url="authenticator.url"
      :color="authenticator.color"
      :label="authenticator.label"
      @click.prevent="onClickAction(authenticator.id)"
    ></auth-button>
  </div>
</template>

<script>
import {computed} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import AuthButton from '@/orangehrmAuthenticationPlugin/components/AuthButton.vue';

export default {
  name: 'SocialMediaAuth',

  components: {
    'auth-button': AuthButton,
  },

  props: {
    authenticators: {
      type: Array,
      default: () => [],
    },
  },

  // TODO
  setup(props) {
    const http = new APIService(window.appGlobal.baseUrl, '');

    const socialAuthenticators = computed(() => {
      return props.authenticators.map((authenticator) => ({
        ...authenticator,
      }));
    });

    return {
      socialAuthenticators,
      http,
    };
  },

  // TODO:: URL
  methods: {
    onClickAction(id) {
      this.http.request({
        method: 'POST',
        url: '/openidauth/openIdCredentials',
        data: {
          providerId: id,
        },
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-social-auth {
  gap: 5px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  max-width: 80%;
  &-header {
    font-size: 0.8rem;
    text-align: center;
    margin-bottom: 1rem;
    margin-top: 1rem;
  }
}
</style>

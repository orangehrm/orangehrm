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
  <div class="orangehrm-social-auth">
    <auth-button
      v-for="authenticator in authenticators"
      :key="authenticator.id"
      :url="authenticator.url"
      :color="authenticator.color"
      :label="authenticator.label"
    ></auth-button>
  </div>
</template>

<script>
import {computed} from 'vue';
import {CHART_COLORS} from '@ohrm/oxd';
import AuthButton from '@/orangehrmAuthenticationPlugin/components/AuthButton.vue';

export default {
  name: 'SocialMediaAuth',

  components: {
    'auth-button': AuthButton,
  },

  setup() {
    // TODO: Pass authenticators via BE prop
    const auths = [
      {
        id: 1,
        label: 'Google',
        url: '',
      },
      {
        id: 2,
        label: 'Facebook',
        url: '',
      },
      {
        id: 3,
        label: 'LinkedIn',
        url: '',
      },
      {
        id: 4,
        label: 'Okta',
        url: '',
      },
      {
        id: 5,
        label: 'Keycloak',
        url: '',
      },
      {
        id: 6,
        label: 'Auth0',
        url: '',
      },
      {
        id: 7,
        label: 'Github',
        url: '',
      },
      {
        id: 8,
        label: 'Youtube',
        url: '',
      },
      {
        id: 9,
        label: 'Twitter',
        url: '',
      },
      {
        id: 10,
        label: 'Reddit',
        url: '',
      },
      {
        id: 11,
        label: 'Instagram',
        url: '',
      },
      {
        id: 12,
        label: 'Spotify',
        url: '',
      },
    ];

    function* getColor() {
      let index = 0;
      const colors = [
        CHART_COLORS.COLOR_HEAT_WAVE,
        CHART_COLORS.COLOR_CHROME_YELLOW,
        CHART_COLORS.COLOR_YELLOW_GREEN,
        CHART_COLORS.COLOR_MOUNTAIN_MEADOW,
        CHART_COLORS.COLOR_PACIFIC_BLUE,
        CHART_COLORS.COLOR_BLEU_DE_FRANCE,
        CHART_COLORS.COLOR_MAJORELLE_BLUE,
        CHART_COLORS.COLOR_MEDIUM_ORCHID,
        CHART_COLORS.COLOR_FANDANGO_PINK,
      ];
      while (true) {
        yield colors[index];
        index = index >= colors.length - 1 ? 0 : index + 1;
      }
    }

    const authenticators = computed(() => {
      const colorGenerator = getColor();
      return auths.map((authenticator) => ({
        ...authenticator,
        color: colorGenerator.next().value,
      }));
    });

    return {
      authenticators,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-social-auth {
  gap: 5px;
  margin: 0 auto;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  max-width: 80%;
}
</style>

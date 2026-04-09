<!--
/*
 * This file is part of OrangeHRM Inc
 *
 * Copyright (C) 2020 onwards OrangeHRM Inc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see  http://www.gnu.org/licenses
 */
-->

<template>
  <div v-if="showRemainingDays">
    <oxd-sheet :gutters="false" type="gray-lighten-2">
      <div class="orangehrm-free-trial">
        <div class="orangehrm-free-trial-logo">
          <img
            :src="freeTrialCompanyLogo"
            class="orangehrm-free-trial-logo-img"
            alt="company logo"
          />
        </div>
        <div class="orangehrm-free-trial-remain-days">
          <oxd-text
            type="card-title"
            class="orangehrm-free-trial-remain-days-title"
          >
            {{ remainingDays }}
          </oxd-text>
        </div>
        <oxd-text> left to subscribe for free hosting </oxd-text>
        <div class="orangehrm-free-trial-subscribe">
          <oxd-text
            type="card-title"
            class="orangehrm-free-trial-subscribe-link"
          >
            <a @click="navigateSubscribePage"> click here </a>
          </oxd-text>
        </div>
      </div>
    </oxd-sheet>
  </div>
</template>

<script>
import {OxdSheet} from '@ohrm/oxd';
import {navigate} from '@/core/util/helper/navigation';

export default {
  name: 'RemainingDays',

  components: {
    'oxd-sheet': OxdSheet,
  },
  props: {
    remainingDays: {
      type: String,
      default: '0',
    },
    showRemainingDays: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      freeTrialCompanyLogo: `${window.appGlobal.publicPath}/images/ohrm_logo.png`,
    };
  },
  methods: {
    navigateSubscribePage() {
      navigate('/trial/subscribeFreeHosting');
    },
  },
};
</script>
<style src="./remain-days.scss" lang="scss" scoped></style>

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
  <oxd-text v-if="errorType !== null" tag="h6">
    {{ errorType }}
  </oxd-text>
  <template v-else>
    <oxd-text tag="h6">
      Authorize <b>{{ clientName }}</b>
    </oxd-text>
    <oxd-form
      ref="authorizeForm"
      method="GET"
      :action="submitUrl"
      @submit-valid="onSubmit"
    >
      <input name="authorized" :value="authorized" type="hidden" />
      <div v-for="(value, name, index) in params" :key="name">
        <input :name="name" :value="value" type="hidden" />
        <p>{{ index }}. {{ name }}: {{ value }}</p>
      </div>
      <oxd-button
        display-type="ghost"
        :label="$t('general.cancel')"
        @click="onCancel"
      />
      <submit-button :label="$t('general.submit')" />
    </oxd-form>
  </template>
  <slot name="footer"></slot>
</template>

<script>
import SubmitButton from '@/core/components/buttons/SubmitButton';
import {urlFor} from '@/core/util/helper/url';

export default {
  name: 'OAuthAuthorize',
  components: {
    'submit-button': SubmitButton,
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

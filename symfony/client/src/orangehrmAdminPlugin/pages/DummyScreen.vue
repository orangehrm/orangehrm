<!--
  - OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  - all the essential functionalities required for any enterprise.
  - Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
  -
  - OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  - the GNU General Public License as published by the Free Software Foundation; either
  - version 2 of the License, or (at your option) any later version.
  -
  - OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  - without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  - See the GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License along with this program;
  - if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  - Boston, MA  02110-1301, USA
  -->

<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('admin.job_titles') }}
        </oxd-text>
      </div>
      <table-header
          :selected="checkedItems.length"
          :total="total"
          :loading="isLoading"
      ></table-header>

      <div v-if="this.checkedItems.length > 0">
        <oxd-text tag="span">
          {{ itemSelectedText }}
        </oxd-text>
        <oxd-button
            label="Delete Selected"
            icon-name="trash-fill"
            display-type="label-danger"
            class="orangehrm-horizontal-margin"
        />
      </div>
      <oxd-text v-else tag="span">{{ itemCountText }}</oxd-text>
      <div>
        <oxd-button
            :label="$t('general.add')"
            icon-name="plus"
            display-type="secondary"
            @click="onClickAdd"
        />
        <oxd-button
            label="Select"
            icon-name="plus"
            display-type="secondary"
            @click="onClickSelect"
        />
      </div>
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import IntlMessageFormat from 'intl-messageformat'
import {ref} from 'vue';

export default {
  setup() {
    const http = new APIService(
        window.appGlobal.baseUrl,
        '/core/i18n/messages',
    );
    const messages = ref([]);
    http.getAll()
        .then(response => {
          messages.value = response.data;
        })
    return {
      http,
      messages
    };
  },
  data() {
    return {
      checkedItems: [],
      total: 0,
      isLoading: false,
    };
  },

  computed: {
    itemSelectedText() {
      if (this.messages['no_of_selected_records'] == undefined) {
        return '';
      }
      const msg = new IntlMessageFormat(this.messages['no_of_selected_records'], 'en-US');
      return msg.format({count: this.checkedItems.length});
    },
    itemCountText() {
      if (this.messages['no_of_selected_records'] == undefined) {
        return '';
      }
      const msg = new IntlMessageFormat(this.messages['no_of_records'], 'en-US');
      return msg.format({count: this.total});
    }
  },

  methods: {
    onClickAdd() {
      this.total++;
    },
    onClickSelect() {
      this.checkedItems.push(1);
    },
  }
};
</script>

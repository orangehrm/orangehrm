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
  <div class="orangehrm-paper-container">
    <div class="orangehrm-header-container">
      <div class="orangehrm-ldap-sync">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('admin.sync_connection') }}
        </oxd-text>
        <oxd-text
          v-show="lastSyncDateTime"
          class="orangehrm-ldap-sync-time"
          type="card-body"
        >
          ({{ $t('admin.last_synced_on') }} {{ lastSyncDateTime }})
        </oxd-text>
      </div>
      <oxd-loading-spinner
        v-if="isLoading"
        class="orangehrm-ldap-sync-loader"
      />
      <oxd-button
        v-else
        display-type="secondary"
        class="orangehrm-ldap-sync-button"
        :label="$t('admin.force_sync')"
        @click="onClickSync"
      />
    </div>
  </div>
</template>

<script>
import {formatDate} from '@/core/util/helper/datefns';
import {APIService} from '@/core/util/services/api.service';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import useDateFormat from '@/core/util/composable/useDateFormat';

export default {
  name: 'LdapSyncConnection',
  components: {
    'oxd-loading-spinner': Spinner,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      // 'api/v2/admin/ldap/sync',
      'api/v2/admin/ldap/user-sync',
    );
    const {jsDateFormat} = useDateFormat();

    return {
      http,
      jsDateFormat,
    };
  },
  data() {
    return {
      isLoading: false,
      lastSync: null,
    };
  },
  computed: {
    lastSyncDateTime() {
      if (!this.lastSync) return null;
      const dateTime = new Date(this.lastSync * 1000);
      return `${formatDate(dateTime, 'hh:mm a')} ${formatDate(
        dateTime,
        this.jsDateFormat,
      )}`;
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.lastSync = data.timestamp;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onClickSync() {
      this.isLoading = true;
      this.http
        .create()
        .then(response => {
          const {data} = response.data;
          this.lastSync = data.timestamp;
          this.$toast.success({
            title: this.$t('general.success'),
            message: this.$t('admin.synchronization_successful'),
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-ldap-sync {
  display: flex;
  flex-direction: column;
  @include oxd-respond-to('md') {
    flex-direction: row;
    align-items: center;
    &-time {
      margin-left: 1rem;
    }
  }
  &-button {
    white-space: normal !important;
  }
  &-loader {
    margin: 0 2rem;
  }
}
</style>

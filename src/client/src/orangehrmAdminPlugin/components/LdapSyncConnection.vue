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
  <div class="orangehrm-paper-container">
    <div class="orangehrm-header-container">
      <div class="orangehrm-ldap-sync">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('admin.sync_connection') }}
        </oxd-text>
        <oxd-text
          v-show="lastSync"
          type="card-body"
          class="orangehrm-ldap-sync-time"
        >
          ({{ lastSync }})
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
        :label="$t('admin.sync_now')"
        @click="onClickSync"
      />
    </div>
  </div>
</template>

<script>
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import {APIService} from '@/core/util/services/api.service';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {OxdSpinner} from '@ohrm/oxd';

export default {
  name: 'LdapSyncConnection',
  components: {
    'oxd-loading-spinner': OxdSpinner,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/ldap/user-sync',
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
      lastSyncDate: null,
      lastSyncTime: null,
      lastSyncStatus: null,
    };
  },
  computed: {
    lastSync() {
      if (this.lastSyncStatus === 2) return null;
      if (this.lastSyncDate && this.lastSyncTime) {
        const parsedDateTime = parseDate(
          `${this.lastSyncDate} ${this.lastSyncTime} +00:00`,
          'yyyy-MM-dd HH:mm xxx',
        );
        return this.$t(
          this.lastSyncStatus === 1
            ? 'admin.last_synced_on_datetime'
            : 'admin.last_sync_failed_on_datetime',
          {
            datetime: formatDate(
              parsedDateTime,
              `hh:mm a ${this.jsDateFormat}`,
            ),
          },
        );
      } else {
        return null;
      }
    },
  },
  beforeMount() {
    this.getLastSyncStatus();
  },
  methods: {
    getLastSyncStatus() {
      this.isLoading = true;
      this.http
        .getAll()
        .then((response) => {
          const {data} = response.data;
          this.lastSyncStatus = data.syncStatus;
          this.lastSyncDate =
            data.syncFinishedAt?.date || data.syncStartedAt?.date;
          this.lastSyncTime =
            data.syncFinishedAt?.time || data.syncStartedAt?.time;
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    onClickSync() {
      this.isLoading = true;
      this.http
        .create()
        .then((response) => {
          const {data} = response.data;
          this.lastSyncStatus = data.syncStatus;
          this.lastSyncDate =
            data.syncFinishedAt?.date || data.syncStartedAt?.date;
          this.lastSyncTime =
            data.syncFinishedAt?.time || data.syncStartedAt?.time;
          this.$toast.success({
            title: this.$t('general.success'),
            message: this.$t('admin.synchronization_successful'),
          });
        })
        .catch(() => {
          this.getLastSyncStatus();
          this.$toast.error({
            title: this.$t('general.error'),
            message: this.$t('admin.synchronization_failed'),
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>

<style src="./ldap-sync-connection.scss" lang="scss" scoped></style>

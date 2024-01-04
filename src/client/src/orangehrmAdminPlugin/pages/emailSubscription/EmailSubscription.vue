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
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('admin.email_subscriptions') }}
        </oxd-text>
      </div>
      <table-header
        :selected="0"
        :total="total"
        :loading="isLoading"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          :loading="isLoading"
          :headers="headers"
          :items="items?.data"
          :selectable="false"
          :clickable="false"
          row-decorator="oxd-table-decorator-card"
        />
      </div>
      <div class="orangehrm-bottom-container"></div>
    </div>
  </div>
</template>

<script>
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import usei18n from '@/core/util/composable/usei18n';
import {OxdSpinner, OxdSwitchInput} from '@ohrm/oxd';

export default {
  setup() {
    const {$t} = usei18n();
    const subscribersNormalizer = (data) => {
      return data.map((item) => {
        const subscribers = Array.isArray(item.subscribers)
          ? item.subscribers.slice(0, 10)
          : [];
        let _type = item.name;
        switch (_type) {
          case 'Leave Applications':
            _type = $t('admin.leave_applications');
            break;
          case 'Leave Approvals':
            _type = $t('admin.leave_approvals');
            break;
          case 'Leave Assignments':
            _type = $t('admin.leave_assignments');
            break;
          case 'Leave Cancellations':
            _type = $t('admin.leave_cancellation');
            break;
          case 'Leave Rejections':
            _type = $t('admin.leave_rejections');
            break;
        }
        return {
          id: item.id,
          type: _type,
          subscribers: subscribers
            .map((sub) => {
              return `${sub.name} <${sub.email}>`;
            })
            .join(', '),
          enabled: item.isEnabled,
          _loading: false,
        };
      });
    };
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/email-subscriptions',
    );
    const {total, response, isLoading} = usePaginate(http, {
      normalizer: subscribersNormalizer,
    });
    return {
      http,
      isLoading,
      total,
      items: response,
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'type',
          slot: 'title',
          title: this.$t('admin.notification_type'),
          style: {flex: '20%'},
        },
        {
          name: 'subscribers',
          title: this.$t('admin.subscribers'),
          style: {flex: '65%'},
        },
        {
          name: 'actions',
          title: this.$t('general.actions'),
          slot: 'action',
          style: {flex: '15%'},
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.cellRenderer,
        },
      ],
    };
  },

  methods: {
    cellRenderer(...args) {
      const [index, , , row] = args;

      const addSubscriber = {
        component: 'oxd-icon-button',
        props: {
          name: 'person-plus-fill',
          onClick: () => {
            navigate('/admin/saveSubscriber/{id}', {id: row.id});
          },
        },
      };

      const switchSubscription = {
        component: OxdSwitchInput,
        props: {
          modelValue: row.enabled,
          'onUpdate:modelValue': ($event) => {
            this.items.data[index]._loading = true;
            this.http
              .update(row.id, {
                enabled: $event,
              })
              .then((response) => {
                const {data} = response.data;
                this.items.data[index].enabled = data.isEnabled;
                this.$toast.updateSuccess();
              })
              .finally(() => {
                this.items.data[index]._loading = false;
              });
          },
          style: {
            'margin-left': '1rem',
            'text-align': 'left',
          },
        },
      };

      const loader = {
        component: OxdSpinner,
        props: {
          withContainer: false,
        },
      };

      return {
        props: {
          header: {
            cellConfig: {
              ...(row._loading
                ? {loader}
                : {addSubscriber, switchSubscription}),
            },
          },
        },
      };
    },
  },
};
</script>

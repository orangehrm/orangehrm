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
  <div class="orangehrm-background-container">
    <slot :filters="filters" :filterItems="filterItems"></slot>
    <br />
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-button
          label="Add"
          iconName="plus"
          displayType="secondary"
          @click="onClickAdd"
        />
        <oxd-text tag="span">
          {{ totalEntitlements }}
        </oxd-text>
      </div>
      <table-header
        :selected="checkedItems.length"
        :total="total"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="items?.data"
          :selectable="true"
          :clickable="false"
          :loading="isLoading"
          v-model:selected="checkedItems"
          rowDecorator="oxd-table-decorator-card"
        />
      </div>
      <div class="orangehrm-bottom-container">
        <oxd-pagination
          v-if="showPaginator"
          :length="pages"
          v-model:current="currentPage"
        />
      </div>
    </div>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import DeleteConfirmationDialog from '@orangehrm/components/dialogs/DeleteConfirmationDialog';

const entitlementNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      leaveType:
        item.leaveType.name + `${item.leaveType.deleted ? ' (Deleted)' : ''}`,
      entitlementType: item.entitlementType.name,
      fromDate: item.fromDate,
      toDate: item.toDate,
      days: item.entitlement,
      isSelectable: item.deletable,
    };
  });
};

export default {
  name: 'leave-entitlement-table',

  props: {
    prefetch: {
      type: Boolean,
      default: true,
    },
    employee: {
      type: Object,
      required: false,
    },
    leaveType: {
      type: Object,
      required: false,
    },
    leavePeriod: {
      type: Object,
      required: false,
    },
  },

  components: {
    'delete-confirmation': DeleteConfirmationDialog,
  },

  data() {
    return {
      headers: [
        {
          name: 'leaveType',
          slot: 'title',
          title: 'Leave Type',
          style: {flex: 1},
        },
        {
          name: 'entitlementType',
          title: 'Entitlement Type',
          style: {flex: 1},
        },
        {
          name: 'fromDate',
          title: 'Valid From',
          style: {flex: 1},
        },
        {
          name: 'toDate',
          title: 'Valid To',
          style: {flex: 1},
        },
        {name: 'days', title: 'Days', style: {flex: 1}},
        {
          name: 'actions',
          slot: 'action',
          title: 'Actions',
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            delete: {
              onClick: this.onClickDelete,
              component: 'oxd-icon-button',
              props: {
                name: 'trash',
              },
            },
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: 'pencil-fill',
              },
            },
          },
        },
      ],
      checkedItems: [],
    };
  },

  setup(props) {
    const filters = ref({
      leaveType: props.leaveType ? props.leaveType : null,
      leavePeriod: props.leavePeriod ? props.leavePeriod : null,
      employee: props.employee
        ? {
            id: props.employee.empNumber,
            label: `${props.employee.firstName} ${props.employee.middleName} ${props.employee.lastName}`,
            isPastEmployee: props.employee.terminationId,
          }
        : null,
    });

    const serializedFilters = computed(() => {
      return {
        empNumber: filters.value.employee?.id,
        leaveTypeId: filters.value.leaveType?.id,
        fromDate: filters.value.leavePeriod?.startDate,
        toDate: filters.value.leavePeriod?.endDate,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/leave/leave-entitlements',
    );
    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
    } = usePaginate(http, {
      query: serializedFilters,
      normalizer: entitlementNormalizer,
      prefetch: props.employee || props.prefetch,
    });

    const totalEntitlements = computed(() => {
      const sum = response.value.meta?.sum ? response.value.meta.sum : 0;
      return `Total ${parseFloat(sum).toFixed(2)} Day(s)`;
    });

    return {
      http,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      pageSize,
      execQuery,
      items: response,
      filters,
      totalEntitlements,
    };
  },

  methods: {
    onClickAdd() {
      navigate('/leave/addLeaveEntitlement');
    },
    onClickEdit(item) {
      navigate('/leave/editLeaveEntitlement/{id}', {id: item.id});
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map(index => {
        return this.items?.data[index].id;
      });
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    onClickDelete(item) {
      if (!item.isSelectable) {
        return this.$toast.cannotDelete();
      }
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteItems([item.id]);
        }
      });
    },
    deleteItems(items) {
      if (items instanceof Array) {
        this.isLoading = true;
        this.http
          .deleteAll({
            ids: items,
          })
          .then(() => {
            return this.$toast.deleteSuccess();
          })
          .then(() => {
            this.isLoading = false;
            this.resetDataTable();
          });
      }
    },
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    async filterItems() {
      await this.execQuery();
    },
  },
};
</script>

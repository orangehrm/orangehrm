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
  <oxd-input-field
    type="select"
    :label="$t('leave.leave_type')"
    :options="options"
    :show-empty-selector="showEmptySelector"
  >
    <template #afterSelected="{data}">
      <template v-if="data.isDeleted">(Deleted)</template>
    </template>
    <template #option="{data}">
      <span>{{ data.label }}</span>
      <div v-if="data.isDeleted" class="deleted-tag">
        (Deleted)
      </div>
    </template>
  </oxd-input-field>
</template>

<script>
import {ref, watchEffect} from 'vue';
import {APIService} from '@ohrm/core/util/services/api.service';
export default {
  name: 'LeaveTypeDropdown',
  props: {
    eligibleOnly: {
      type: Boolean,
      default: true,
    },
    employeeId: {
      type: Number,
      required: false,
      default: null,
    },
    showEmptySelector: {
      type: Boolean,
      default: true,
    },
    includeAllocated: {
      type: Boolean,
      default: false,
    },
  },
  setup(props, context) {
    const options = ref([]);
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/leave/leave-types${props.eligibleOnly ? '/eligible' : ''}`,
    );

    watchEffect(async () => {
      if (!props.eligibleOnly && props.includeAllocated) {
        // eslint-disable-next-line no-console
        console.error(
          '`includeAllocated` prop can true only if `eligibleOnly` prop true',
        );
      }
      http
        .getAll({
          empNumber: props.employeeId,
          includeAllocated:
            props.eligibleOnly && props.includeAllocated ? true : undefined,
        })
        .then(({data}) => {
          options.value = data.data.map(item => {
            return {
              id: item.id,
              label: item.name,
              isDeleted: item.deleted,
            };
          });
          if (!props.showEmptySelector && options.value.length > 0) {
            // this $event is only fired to default select first option
            // where --select-- options is not shown
            // eslint-disable-next-line vue/require-explicit-emits
            context.emit('update:modelValue', options.value[0]);
          }
        });
    });

    return {
      options,
    };
  },
};
</script>

<style scoped>
.deleted-tag {
  margin-left: auto;
}
</style>

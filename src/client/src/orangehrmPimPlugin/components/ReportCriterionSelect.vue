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
  <oxd-grid-item>
    <oxd-input-field
      v-bind="$attrs"
      type="select"
      :rules="rules"
      :options="opts"
      :model-value="valueX"
      @update:modelValue="$emit('update:valueX', $event)"
    />
  </oxd-grid-item>
</template>

<script>
import {ref, onBeforeMount} from 'vue';
import {required} from '@ohrm/core/util/validation/rules';
import {APIService} from '@ohrm/core/util/services/api.service';

export default {
  name: 'ReportCriterionSelect',
  inheritAttrs: false,
  props: {
    api: {
      type: String,
      required: false,
      default: null,
    },
    options: {
      type: Array,
      default: () => [],
    },
    valueX: {
      type: Object,
      required: false,
      default: () => null,
    },
  },
  emits: ['update:valueX', 'update:operator'],
  setup(props, context) {
    const opts = ref(props.options);
    const rules = [required];

    if (props.api) {
      const http = new APIService(window.appGlobal.baseUrl, props.api);
      onBeforeMount(() => {
        http.getAll().then(({data}) => {
          opts.value = data.data.map(item => {
            return {
              id: item.id,
              label: item.name ? item.name : item.title,
              _indent: item.level ? item.level + 1 : 1,
            };
          });
        });
      });
    }

    if (
      props.api === 'api/v2/admin/locations' ||
      props.api === 'api/v2/admin/subunits'
    ) {
      context.emit('update:operator', {id: 'in', label: 'Equal'});
    } else {
      context.emit('update:operator', {id: 'eq', label: 'Equal'});
    }

    return {
      opts,
      rules,
    };
  },
};
</script>

<style lang="scss" scoped>
::v-deep(.oxd-input-group__label-wrapper) {
  display: none;
}
</style>

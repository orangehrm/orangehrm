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
  <oxd-grid-item class="orangehrm-report-criteria --offset-column-1">
    <oxd-icon-button name="trash-fill" @click="onClickDelete" />
    <oxd-text class="orangehrm-report-criteria-name">
      {{ criterion.label }}
    </oxd-text>
  </oxd-grid-item>

  <oxd-grid-item>
    <oxd-input-field
      :type="field.type"
      :label="field.name"
      :options="field.options"
    />
  </oxd-grid-item>

  <oxd-grid-item>
    {{ criterion }}
  </oxd-grid-item>
</template>

<script>
import {ref} from 'vue';
export default {
  name: 'report-criterion',

  emits: ['delete'],

  props: {
    criterion: {
      type: Object,
      required: true,
    },
  },

  setup(props, context) {
    const field = ref(null);
    // map the field type according to criterion
    switch (props.criterion.key) {
      case 'employee_name':
        field.value = {
          name: props.criterion.label,
          type: 'autocomplete',
          api: '',
          options: [],
        };
        break;

      case 'pay_grade':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      case 'education':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      case 'employment_status':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      case 'service_period':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      case 'joined_date':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      case 'job_title':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      case 'language':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      case 'skill':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      case 'age_group':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      case 'sub_unit':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      case 'gender':
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
        break;

      default:
        field.value = {
          name: props.criterion.label,
          type: 'select',
          api: '',
          options: [],
        };
    }

    const onClickDelete = $event => {
      context.emit('delete', $event);
    };

    return {
      field,
      onClickDelete,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-report {
  &-criteria {
    display: flex;
    align-items: center;
  }
  &-criteria-name {
    margin-left: 1rem;
    font-weight: 700;
    font-size: $oxd-input-control-font-size;
    padding: $oxd-input-control-vertical-padding 0rem;
  }
}
</style>

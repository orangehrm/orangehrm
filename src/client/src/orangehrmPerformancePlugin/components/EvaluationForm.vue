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
  <div class="orangehrm-evaluation orangehrm-card-container">
    <oxd-text class="orangehrm-main-title">
      {{ title }}
    </oxd-text>
    <div class="orangehrm-evaluation-header">
      <div class="orangehrm-evaluation-header-title">
        <img
          class="orangehrm-evaluation-header-profile-image"
          alt="profile picture"
          :src="profileImgSrc"
        />
        <div class="orangehrm-evaluation-header-name">
          <oxd-text type="card-title">
            {{ employeeName }}
          </oxd-text>
          <oxd-text type="card-body">
            {{ jobTitle }}
          </oxd-text>
        </div>
      </div>
      <div class="orangehrm-evaluation-header-action">
        <oxd-text type="card-title">
          Evaluation Activated
        </oxd-text>
        <oxd-icon-button
          v-if="collapsible"
          :with-container="false"
          :name="isCollapsed ? 'chevron-up' : 'chevron-down'"
          @click="toggleForm"
        />
      </div>
    </div>
    <oxd-divider v-show="!isCollapsed" />
    <template v-if="!isCollapsed">
      <oxd-grid :cols="3" class="orangehrm-evaluation-grid">
        <oxd-grid-item class="orangehrm-evaluation-grid-header">
          <oxd-text type="subtitle-2">KPIs</oxd-text>
        </oxd-grid-item>
        <oxd-grid-item class="orangehrm-evaluation-grid-header">
          <oxd-text type="subtitle-2">Rating</oxd-text>
        </oxd-grid-item>
        <oxd-grid-item class="orangehrm-evaluation-grid-header">
          <oxd-text type="subtitle-2">{{ $t('general.comments') }}</oxd-text>
        </oxd-grid-item>

        <template v-for="(kpi, index) in kpis" :key="kpi.id">
          <oxd-grid-item class="orangehrm-evaluation-grid-kpi">
            <oxd-text
              class="orangehrm-evaluation-grid-kpi-header"
              type="subtitle-2"
            >
              KPI
            </oxd-text>
            <oxd-text
              :title="kpi.title"
              tag="p"
              class="orangehrm-evaluation-grid-kpi-label"
            >
              {{ kpi.title }}
            </oxd-text>
            <oxd-text class="orangehrm-evaluation-grid-kpi-minmax" tag="p">
              Min: {{ kpi.minRating }}
            </oxd-text>
            <oxd-text class="orangehrm-evaluation-grid-kpi-minmax" tag="p">
              Max: {{ kpi.maxRating }}
            </oxd-text>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-text
              class="orangehrm-evaluation-grid-kpi-header"
              type="subtitle-2"
            >
              Rating
            </oxd-text>
            <oxd-input-field
              type="input"
              :rules="rules[index]"
              :model-value="modelValue[index].rating"
              @update:modelValue="onUpdateRating($event, index)"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-text
              class="orangehrm-evaluation-grid-kpi-header"
              type="subtitle-2"
            >
              {{ $t('general.comment') }}
            </oxd-text>
            <oxd-input-field
              class="orangehrm-evaluation-grid-comment"
              type="textarea"
              rows="2"
              :model-value="modelValue[index].comment"
              @update:modelValue="onUpdateComment($event, index)"
            />
          </oxd-grid-item>
          <div class="orangehrm-evaluation-grid-spacer"></div>
        </template>
      </oxd-grid>
      <slot></slot>
    </template>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import usei18n from '@/core/util/composable/usei18n';

export default {
  props: {
    kpis: {
      type: Array,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    editable: {
      type: Boolean,
      required: true,
    },
    collapsible: {
      type: Boolean,
      required: true,
    },
    employee: {
      type: Object,
      required: true,
    },
    jobTitle: {
      type: String,
      required: true,
    },
    rules: {
      type: Array,
      required: true,
    },
    modelValue: {
      type: Array,
      required: true,
    },
  },

  emits: ['update:modelValue'],

  setup(props, context) {
    const {$t} = usei18n();
    const isCollapsed = ref(props.kpis.length === 0);

    const profileImgSrc = computed(() => {
      return `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${props.employee.empNumber}`;
    });

    const employeeName = computed(() => {
      return `${props.employee.firstName} ${props.employee.lastName} ${
        props.employee.terminationId ? $t('general.past_employee') : ''
      }`;
    });

    const toggleForm = () => {
      isCollapsed.value = !isCollapsed.value;
    };

    const onUpdateRating = (value, index) => {
      context.emit(
        'update:modelValue',
        props.modelValue.map((item, _index) => {
          if (_index === index) {
            return {...item, rating: value};
          }
          return item;
        }),
      );
    };

    const onUpdateComment = (value, index) => {
      context.emit(
        'update:modelValue',
        props.modelValue.map((item, _index) => {
          if (_index === index) {
            return {...item, comment: value};
          }
          return item;
        }),
      );
    };

    return {
      toggleForm,
      isCollapsed,
      employeeName,
      profileImgSrc,
      onUpdateRating,
      onUpdateComment,
    };
  },
};
</script>

<style src="./evaluation-form.scss" lang="scss" scoped></style>

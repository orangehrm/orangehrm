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
  <div class="orangehrm-evaluation orangehrm-card-container">
    <oxd-text class="orangehrm-main-title">
      {{ title }}
    </oxd-text>

    <div class="orangehrm-evaluation-header">
      <oxd-grid :cols="3" class="orangehrm-evaluation-header-grid">
        <oxd-grid-item class="orangehrm-evaluation-title">
          <img
            class="orangehrm-evaluation-title-profile-image"
            alt="profile picture"
            :src="profileImgSrc"
          />
          <div class="orangehrm-evaluation-title-name">
            <oxd-text type="card-title">
              {{ employeeName }}
            </oxd-text>
            <oxd-text type="card-body">
              {{ jobTitle }}
            </oxd-text>
          </div>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-text type="card-body">
            {{ $t('general.status') }}
          </oxd-text>
          <oxd-text type="card-title">
            {{ evaluationLabel }}
          </oxd-text>
        </oxd-grid-item>
      </oxd-grid>
      <oxd-icon-button
        v-if="collapsible"
        :with-container="false"
        :name="isCollapsed ? 'chevron-down' : 'chevron-up'"
        @click="toggleForm"
      />
    </div>

    <template v-if="!isCollapsed">
      <oxd-divider />
      <oxd-grid :cols="4" class="orangehrm-evaluation-grid">
        <oxd-grid-item class="orangehrm-evaluation-grid-header">
          <oxd-text type="subtitle-2">{{ $t('general.kpis') }}</oxd-text>
        </oxd-grid-item>
        <oxd-grid-item class="orangehrm-evaluation-grid-header">
          <oxd-text type="subtitle-2">{{ $t('performance.rating') }}</oxd-text>
        </oxd-grid-item>
        <oxd-grid-item class="orangehrm-evaluation-grid-header">
          <oxd-text type="subtitle-2">{{ $t('general.comments') }}</oxd-text>
        </oxd-grid-item>
        <oxd-grid-item
          class="orangehrm-evaluation-grid-spacer-md"
        ></oxd-grid-item>

        <template v-for="(kpi, index) in kpis" :key="kpi.id">
          <oxd-grid-item class="orangehrm-evaluation-grid-kpi">
            <oxd-text
              class="orangehrm-evaluation-grid-kpi-header"
              type="subtitle-2"
            >
              {{ $t('performance.kpi') }}
            </oxd-text>
            <oxd-text
              :title="kpi.title"
              tag="p"
              class="orangehrm-evaluation-grid-kpi-label"
            >
              {{ kpi.title }}
            </oxd-text>
            <oxd-text class="orangehrm-evaluation-grid-kpi-minmax" tag="p">
              {{ $t('performance.min') }}: {{ kpi.minRating }}
            </oxd-text>
            <oxd-text class="orangehrm-evaluation-grid-kpi-minmax" tag="p">
              {{ $t('performance.max') }}: {{ kpi.maxRating }}
            </oxd-text>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-text
              class="orangehrm-evaluation-grid-kpi-header"
              type="subtitle-2"
            >
              {{ $t('performance.rating') }}
            </oxd-text>
            <oxd-input-field
              type="input"
              :disabled="!editable"
              :rules="rules[index]"
              :model-value="modelValue.kpis[index].rating"
              @update:model-value="onUpdateRating($event, index)"
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
              rows="2"
              type="textarea"
              :disabled="!editable"
              :rules="commentValidators"
              :model-value="modelValue.kpis[index].comment"
              @update:model-value="onUpdateComment($event, index)"
            />
          </oxd-grid-item>
          <oxd-grid-item
            class="orangehrm-evaluation-grid-spacer-md"
          ></oxd-grid-item>
        </template>
      </oxd-grid>

      <oxd-divider />
      <oxd-grid :cols="3" class="orangehrm-evaluation-grid">
        <oxd-grid-item class="orangehrm-evaluation-grid-general">
          <oxd-text tag="p" class="orangehrm-evaluation-grid-general-label">
            {{ $t('performance.general_comment') }}
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item class="--span-column-2">
          <oxd-input-field
            class="orangehrm-evaluation-grid-comment"
            rows="2"
            type="textarea"
            :disabled="!editable"
            :rules="commentValidators"
            :model-value="modelValue.generalComment"
            @update:model-value="onUpdateGeneralComment($event)"
          />
        </oxd-grid-item>
      </oxd-grid>
      <slot></slot>
    </template>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
import {shouldNotExceedCharLength} from '@/core/util/validation/rules';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import {OxdDivider} from '@ohrm/oxd';

const defaultPic = `${window.appGlobal.publicPath}/images/default-photo.png`;

export default {
  components: {
    'oxd-divider': OxdDivider,
  },
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
      type: Object,
      required: true,
      validator: (value) =>
        Object.hasOwn(value, 'kpis') && Object.hasOwn(value, 'generalComment'),
    },
    collapsed: {
      type: Boolean,
      default: false,
    },
    status: {
      type: Number,
      required: true,
    },
  },

  emits: ['update:modelValue'],

  setup(props, context) {
    const {$t} = usei18n();
    const {$tEmpName} = useEmployeeNameTranslate();
    const isCollapsed = ref(props.collapsed);
    const commentValidators = [shouldNotExceedCharLength(2000)];

    const profileImgSrc = computed(() => {
      return props.employee.empNumber
        ? `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${props.employee.empNumber}`
        : defaultPic;
    });

    const employeeName = computed(() => {
      return $tEmpName(props.employee);
    });

    const toggleForm = () => {
      isCollapsed.value = !isCollapsed.value;
    };

    const onUpdateRating = (value, index) => {
      context.emit('update:modelValue', {
        kpis: props.modelValue.kpis.map((item, _index) => {
          if (_index === index) {
            return {...item, rating: value};
          }
          return item;
        }),
        generalComment: props.modelValue.generalComment,
      });
    };

    const onUpdateComment = (value, index) => {
      context.emit('update:modelValue', {
        kpis: props.modelValue.kpis.map((item, _index) => {
          if (_index === index) {
            return {...item, comment: value};
          }
          return item;
        }),
        generalComment: props.modelValue.generalComment,
      });
    };

    const onUpdateGeneralComment = (value) => {
      context.emit('update:modelValue', {
        kpis: props.modelValue.kpis,
        generalComment: value,
      });
    };

    const statusOpts = [
      {id: 1, label: $t('performance.evaluation_activated')},
      {id: 2, label: $t('performance.evaluation_in_progress')},
      {id: 3, label: $t('performance.evaluation_completed')},
    ];

    const evaluationLabel = computed(
      () => statusOpts.find((el) => el.id === props.status).label,
    );

    return {
      toggleForm,
      isCollapsed,
      employeeName,
      profileImgSrc,
      onUpdateRating,
      onUpdateComment,
      onUpdateGeneralComment,
      commentValidators,
      evaluationLabel,
    };
  },
};
</script>

<style src="./evaluation-form.scss" lang="scss" scoped></style>

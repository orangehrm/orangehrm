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
    <oxd-text class="orangehrm-main-title">Evaluation by Employee</oxd-text>
    <div class="orangehrm-evaluation-header">
      <div class="orangehrm-evaluation-header-title">
        <img
          class="orangehrm-evaluation-header-profile-image"
          alt="profile picture"
          :src="profileImgSrc"
        />
        <div class="orangehrm-evaluation-header-name">
          <oxd-text type="card-title">
            James McGill
          </oxd-text>
          <oxd-text type="card-body">
            Software Engineer
          </oxd-text>
        </div>
      </div>
      <div class="orangehrm-evaluation-header-action">
        <oxd-text type="card-title">
          Evaluation Activated
        </oxd-text>
        <oxd-icon-button
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
          <oxd-text type="subtitle-2">Comments</oxd-text>
        </oxd-grid-item>

        <template v-for="kpi in kpis" :key="kpi.id">
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
            <oxd-input-field type="input" />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-text
              class="orangehrm-evaluation-grid-kpi-header"
              type="subtitle-2"
            >
              Comment
            </oxd-text>
            <oxd-input-field
              class="orangehrm-evaluation-grid-comment"
              type="textarea"
              rows="2"
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
import {computed, reactive, toRefs} from 'vue';

export default {
  setup() {
    const state = reactive({
      isCollapsed: true,
      kpis: [
        {
          id: 3,
          title: 'Client retention rate',
          minRating: 1,
          maxRating: 5,
          isDefault: false,
        },
        {
          id: 4,
          title: 'Customer satisfaction',
          minRating: 1,
          maxRating: 5,
          isDefault: false,
        },
        {
          id: 5,
          title: 'Profit margin',
          minRating: 1,
          maxRating: 5,
          isDefault: false,
        },
        {
          id: 1,
          title:
            'Praesent lorem mauris, rhoncus vel hendrerit ac, egestas in nulla. Cras suscipit mi ut dictum lacinia. Curabitur pellentesque neque ut aliquet ullamcorper',
          minRating: 1,
          maxRating: 5,
          isDefault: false,
        },
        {
          id: 2,
          title: 'Revenue per client',
          minRating: 1,
          maxRating: 5,
          isDefault: false,
        },
      ],
    });

    const profileImgSrc = computed(() => {
      return `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/1`;
    });

    const toggleForm = () => {
      state.isCollapsed = !state.isCollapsed;
    };

    return {
      toggleForm,
      profileImgSrc,
      ...toRefs(state),
    };
  },
};
</script>

<style src="./evaluation-form.scss" lang="scss" scoped></style>

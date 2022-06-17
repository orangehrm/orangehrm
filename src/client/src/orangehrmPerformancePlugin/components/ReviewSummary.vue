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
  <div class="orangehrm-card-container">
    <oxd-text tag="h5" class="orangehrm-performance-review-title">
      {{ $t('performance.review_summary') }}
    </oxd-text>
    <div class="orangehrm-performance-review-owner">
      <img alt="profile picture" class="employee-image" :src="imgSrc" />
      <div class="orangehrm-performance-review-owner-employee-section">
        <div class="orangehrm-performance-review-owner-employee">
          <oxd-text
            tag="h5"
            class="orangehrm-performance-review-owner-employee-name"
          >
            {{ employeeName }}
          </oxd-text>
          <oxd-text
            tag="h6"
            class="orangehrm-performance-review-owner-employee-job"
          >
            {{ jobTitle }}
          </oxd-text>
        </div>
      </div>
    </div>
    <oxd-grid :cols="3">
      <oxd-grid-item class="orangehrm-performance-review-column">
        <oxd-text type="subtitle-2">
          {{ $t('performance.review_status') }}
        </oxd-text>
        <oxd-text class="orangehrm-performance-review-bold">
          {{ reviewStatus }}
        </oxd-text>
      </oxd-grid-item>
      <oxd-grid-item class="orangehrm-performance-review-column">
        <oxd-text type="subtitle-2">
          {{ $t('performance.review_period') }}
        </oxd-text>
        <oxd-text class="orangehrm-performance-review-bold">
          {{ reviewPeriod }}
        </oxd-text>
      </oxd-grid-item>
      <oxd-grid-item class="orangehrm-performance-review-column">
        <oxd-text type="subtitle-2">
          {{ $t('performance.review_due_date') }}
        </oxd-text>
        <oxd-text class="orangehrm-performance-review-bold">
          {{ reviewDueDate }}
        </oxd-text>
      </oxd-grid-item>
    </oxd-grid>
  </div>
</template>

<script>
import {computed} from 'vue';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useLocale from '@/core/util/composable/useLocale';
import usei18n from '@/core/util/composable/usei18n';

export default {
  name: 'ReviewSummary',
  props: {
    employee: {
      type: Object,
      required: true,
    },
    jobTitle: {
      type: String,
      required: true,
    },
    status: {
      type: Number,
      required: true,
    },
    reviewPeriodStart: {
      type: String,
      required: true,
    },
    reviewPeriodEnd: {
      type: String,
      required: true,
    },
    dueDate: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    const {$t} = usei18n();
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const statusOpts = [
      {id: 1, label: $t('performance.inactive')},
      {id: 2, label: $t('performance.activated')},
      {id: 3, label: $t('performance.in_progress')},
      {id: 4, label: $t('performance.completed')},
    ];

    const reviewDateFormat = date =>
      formatDate(parseDate(date), jsDateFormat, {locale});

    const imgSrc = `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${props.employee.empNumber}`;
    const reviewStatus = statusOpts.find(el => el.id === props.status).label;
    const reviewPeriod = `${reviewDateFormat(
      props.reviewPeriodStart,
    )} - ${reviewDateFormat(props.reviewPeriodEnd)}`;
    const reviewDueDate = reviewDateFormat(props.dueDate);

    const employeeName = computed(() => {
      return `${props.employee.firstName} ${props.employee.lastName} ${
        props.employee.terminationId ? $t('general.past_employee') : ''
      }`;
    });

    return {
      imgSrc,
      reviewStatus,
      reviewPeriod,
      reviewDueDate,
      employeeName,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-performance-review {
  &-title {
    font-size: 14px;
    font-weight: 800;
  }

  &-bold {
    font-weight: 700;
  }

  &-owner {
    display: flex;
    flex-direction: row;
    align-items: center;
    margin-top: 1.2rem;
    margin-bottom: 1.2rem;

    & img {
      width: 75px;
      height: 75px;
      border-radius: 100%;
      display: flex;
      overflow: hidden;
      justify-content: center;
      box-sizing: border-box;
    }

    &-employee-section {
      display: flex;
    }

    &-employee {
      display: flex;
      flex-direction: column;
      padding-left: 1.2rem;

      &-name {
        font-weight: 700;
        font-size: 21px;
      }

      &-job {
        font-weight: 700;
        color: $oxd-interface-gray-color;
      }
    }
  }

  &-column {
    margin-bottom: 0.5rem;
  }
}
</style>

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
    <div class="orangehrm-performance-review-summary">
      <oxd-text tag="h5" class="orangehrm-performance-review-title">
        {{ $t('performance.review_summary') }}
      </oxd-text>
      <oxd-form :loading="loading">
        <oxd-grid :cols="3" class="orangehrm-performance-review-grid">
          <oxd-grid-item :class="reviewOwnerClasses">
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
          </oxd-grid-item>
          <oxd-grid-item v-show="status === 4" :class="finalRatingClasses">
            <div class="orangehrm-performance-review-rating">
              <oxd-text type="subtitle-2">
                {{ $t('performance.final_rating') }}
              </oxd-text>
              <oxd-text
                tag="h4"
                class="orangehrm-performance-review-rating-number"
              >
                {{ finalRating }}
              </oxd-text>
            </div>
          </oxd-grid-item>
          <oxd-grid-item class="orangehrm-performance-review-column">
            <oxd-text type="subtitle-2">
              {{ $t('performance.review_status') }}
            </oxd-text>
            <oxd-text class="orangehrm-performance-review-bold">
              {{ reviewStatus }}
            </oxd-text>
          </oxd-grid-item>
          <oxd-grid-item
            class="orangehrm-performance-review-column --review-period"
          >
            <oxd-text type="subtitle-2">
              {{ $t('performance.review_period') }}
            </oxd-text>
            <oxd-text class="orangehrm-performance-review-bold">
              {{ reviewPeriod }}
            </oxd-text>
          </oxd-grid-item>
          <oxd-grid-item
            class="orangehrm-performance-review-column --review-due-date"
          >
            <oxd-text type="subtitle-2">
              {{ $t('performance.review_due_date') }}
            </oxd-text>
            <oxd-text class="orangehrm-performance-review-bold">
              {{ reviewDueDate }}
            </oxd-text>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form>
    </div>
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
  inject: ['screenState'],
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
    loading: {
      type: Boolean,
      required: true,
    },
    finalRating: {
      type: Number,
      default: 0,
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

    const imgSrc = computed(
      () =>
        `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${props.employee.empNumber}`,
    );
    const reviewStatus = statusOpts.find(el => el.id === props.status).label;
    const reviewPeriod = `${reviewDateFormat(
      props.reviewPeriodStart,
    )} - ${reviewDateFormat(props.reviewPeriodEnd)}`;
    const reviewDueDate = reviewDateFormat(props.dueDate);

    const employeeName = computed(() => {
      return `${props.employee.firstName} ${props.employee.middleName ? props.employee.middleName : ''} ${props.employee.lastName} ${
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
  computed: {
    reviewOwnerClasses() {
      return {
        '--span-column-2': this.status === 4,
        '--span-column-3': this.status < 4,
      };
    },
    finalRatingClasses() {
      return {
        'orangehrm-performance-review-final-rating': true,
        '--span-column-3': !(
          this.screenState.screenType === 'lg' ||
          this.screenState.screenType === 'xl'
        ),
      };
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-performance-review {
  &-title {
    font-size: 14px;
    font-weight: 800;
  }

  &-bold {
    font-weight: 700;
  }

  &-grid {
    padding-left: 0.25rem;
    @include oxd-respond-to('md') {
      padding-left: 0;
    }
  }

  &-top {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    @include oxd-respond-to('md') {
      flex-direction: row;
    }
  }

  &-rating {
    display: flex;
    flex-direction: column;
    @include oxd-respond-to('lg') {
      text-align: end;
    }

    &-number {
      color: $oxd-primary-one-color;
    }
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

  &-final-rating {
    justify-self: start;
    margin-top: 0;
    margin-bottom: 0;
    @include oxd-respond-to('lg') {
      justify-self: end;
      margin-top: 1.2rem;
      margin-bottom: 1.2rem;
    }
  }

  &-column {
    margin-bottom: 0.5rem;

    &.--review-period {
      justify-self: start;
      @include oxd-respond-to('lg') {
        justify-self: center;
      }
    }

    &.--review-due-date {
      @include oxd-respond-to('lg') {
        justify-self: end;
      }
    }
  }
}
</style>

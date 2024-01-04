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
  <div>
    <oxd-text class="orangehrm-performance-review-title">
      {{ $t('performance.review_finalization') }}
    </oxd-text>
    <br />
    <oxd-grid :cols="4" class="orangehrm-performance-review-grid">
      <oxd-grid-item>
        <oxd-text type="subtitle-2">
          {{ $t('performance.date_of_completion') }}
        </oxd-text>
        <date-input
          v-if="editable"
          :model-value="completedDate"
          :rules="rules.completedDate"
          @update:model-value="$emit('update:completedDate', $event)"
        />
        <div v-else class="orangehrm-performance-review-read">
          <oxd-text>{{ formattedCompletedDate }}</oxd-text>
        </div>
      </oxd-grid-item>
      <oxd-grid-item class="orangehrm-performance-review-grid-rating">
        <oxd-text type="subtitle-2">
          {{ $t('performance.final_rating') }}
        </oxd-text>
        <oxd-input-field
          v-if="editable"
          :model-value="finalRating"
          :rules="rules.finalRating"
          @update:model-value="$emit('update:finalRating', $event)"
        />
        <div v-else class="orangehrm-performance-review-read">
          <oxd-text>{{ finalRating }}</oxd-text>
        </div>
      </oxd-grid-item>
      <oxd-grid-item>
        <oxd-text type="subtitle-2">
          {{ $t('performance.final_comments') }}
        </oxd-text>
        <oxd-input-field
          v-if="editable"
          rows="1"
          type="textarea"
          :model-value="finalComment"
          :rules="rules.finalComment"
          @update:model-value="$emit('update:finalComment', $event)"
        />
        <div v-else class="orangehrm-performance-review-read">
          <oxd-text>{{ finalComment }}</oxd-text>
        </div>
      </oxd-grid-item>
    </oxd-grid>
  </div>
</template>

<script>
import {computed} from 'vue';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useLocale from '@/core/util/composable/useLocale';
import {
  required,
  validDateFormat,
  greaterThanOrEqual,
  lessThanOrEqual,
} from '@/core/util/validation/rules';

export default {
  name: 'FinalEvaluation',
  props: {
    completedDate: {
      type: String,
      default: null,
      required: false,
    },
    finalRating: {
      type: Number,
      default: null,
      required: false,
    },
    finalComment: {
      type: String,
      default: null,
      required: false,
    },
    status: {
      type: Number,
      required: true,
    },
    isRequired: {
      type: Boolean,
      required: true,
    },
  },
  emits: ['update:finalRating', 'update:finalComment', 'update:completedDate'],
  setup(props) {
    const {locale} = useLocale();
    const {jsDateFormat, userDateFormat} = useDateFormat();

    const editable = computed(() => props.status !== 4);
    const formattedCompletedDate = computed(() =>
      formatDate(parseDate(props.completedDate), jsDateFormat, {locale}),
    );

    return {
      editable,
      userDateFormat,
      formattedCompletedDate,
    };
  },

  data() {
    return {
      rules: {
        completedDate: [
          validDateFormat(this.userDateFormat),
          ...(this.isRequired ? [required] : []),
        ],
        finalRating: [
          greaterThanOrEqual(
            0,
            this.$t(
              'performance.rating_should_be_greater_than_or_equal_to_minValue',
              {
                minValue: 0,
              },
            ),
          ),
          lessThanOrEqual(
            100,
            this.$t(
              'performance.rating_should_be_less_than_or_equal_to_maxValue',
              {
                maxValue: 100,
              },
            ),
          ),
          ...(this.isRequired ? [required] : []),
        ],
        finalComment: [...(this.isRequired ? [required] : [])],
      },
    };
  },
};
</script>

<style src="./final-evaluation.scss" lang="scss" scoped></style>

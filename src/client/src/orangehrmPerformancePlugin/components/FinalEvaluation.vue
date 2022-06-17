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
  <div>
    <oxd-text class="orangehrm-performance-review-title">
      {{ $t('performance.review_finalization') }}
    </oxd-text>
    <br />
    <oxd-grid :cols="3">
      <oxd-grid-item>
        <oxd-text type="subtitle-2">
          {{ $t('performance.date_of_completion') }}
        </oxd-text>
        <date-input
          v-if="editable"
          :model-value="completedDate"
          :rules="rules.completedDate"
          @update:modelValue="$emit('update:completedDate', $event)"
        />
        <div v-else class="orangehrm-performance-review-final-read">
          <oxd-text>{{ formattedCompletedDate }}</oxd-text>
        </div>
      </oxd-grid-item>
      <oxd-grid-item>
        <oxd-text type="subtitle-2">
          {{ $t('performance.final_rating') }}
        </oxd-text>
        <oxd-input-field
          v-if="editable"
          :model-value="finalRating"
          :rules="rules.finalRating"
          @update:modelValue="$emit('update:finalRating', $event)"
        />
        <div v-else class="orangehrm-performance-review-final-read">
          <oxd-text>{{ finalRating }}</oxd-text>
        </div>
      </oxd-grid-item>
      <oxd-grid-item>
        <oxd-text type="subtitle-2">
          {{ $t('performance.final_comments') }}
        </oxd-text>
        <oxd-input-field
          v-if="editable"
          :model-value="finalComment"
          :rules="rules.finalComment"
          @update:modelValue="$emit('update:finalComment', $event)"
        />
        <div v-else class="orangehrm-performance-review-final-read">
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
      type: String,
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
    const {jsDateFormat} = useDateFormat();

    const editable = computed(() => props.status !== 4);
    const formattedCompletedDate = computed(() =>
      formatDate(parseDate(props.completedDate), jsDateFormat, {locale}),
    );

    return {
      editable,
      formattedCompletedDate,
    };
  },

  data() {
    return {
      rules: {
        completedDate: [
          validDateFormat(),
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
        // TODO add min max rules
        finalComment: [...(this.isRequired ? [required] : [])],
      },
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

  &-final {
    display: flex;
    flex-direction: row;
    margin-top: 1.2rem;
    margin-bottom: 1.2rem;

    &-date {
      width: 30%;
      margin-right: 2.4rem;
    }

    &-rating {
      width: 10%;
      margin-right: 2.4rem;
      //overflow: hidden;
      //white-space: nowrap;
      //text-overflow: ellipsis;
    }

    &-comment {
      width: 65%;
    }

    &-read {
      margin-top: 1.2rem;
    }
  }
}
</style>

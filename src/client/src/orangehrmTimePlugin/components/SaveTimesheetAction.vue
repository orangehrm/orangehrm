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
    <oxd-text tag="h6" class="orangehrm-main-title">
      {{ $t('time.timesheet_action') }}
    </oxd-text>
    <oxd-divider />
    <oxd-form ref="formRef" :loading="isLoading">
      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="comment"
              type="textarea"
              :placeholder="$t('general.type_here_message')"
              :rules="rules.comment"
              :label="$t('general.comment')"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />
      <oxd-form-actions>
        <oxd-button
          v-if="canRejectTimesheet"
          :label="$t('general.reject')"
          display-type="danger"
          @click="onClickReject"
        />
        <oxd-button
          v-if="canApproveTimesheet"
          :label="$t('general.approve')"
          display-type="secondary"
          class="orangehrm-left-space"
          @click="onClickApprove('test')"
        />
      </oxd-form-actions>
    </oxd-form>
  </div>
</template>

<script>
import {ref} from 'vue';
import useForm from '@ohrm/core/util/composable/useForm';
import {shouldNotExceedCharLength} from '@/core/util/validation/rules';

export default {
  name: 'SaveTimesheetAction',

  props: {
    isLoading: {
      type: Boolean,
      required: true,
    },
    rejectTimesheet: {
      type: Function,
      required: true,
    },
    approveTimesheet: {
      type: Function,
      required: true,
    },
    canRejectTimesheet: {
      type: Boolean,
      required: true,
    },
    canApproveTimesheet: {
      type: Boolean,
      required: true,
    },
  },

  setup(props) {
    const {formRef, invalid, validate} = useForm();

    const comment = ref('');

    const rules = {
      comment: [shouldNotExceedCharLength(250)],
    };

    const onClickApprove = () => {
      validate().then(
        () => invalid.value === false && props.approveTimesheet(comment.value),
      );
    };

    const onClickReject = () => {
      validate().then(
        () => invalid.value === false && props.rejectTimesheet(comment.value),
      );
    };

    return {
      rules,
      comment,
      formRef,
      onClickReject,
      onClickApprove,
    };
  },
};
</script>

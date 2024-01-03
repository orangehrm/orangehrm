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
  <oxd-dialog
    :style="{width: '90%', maxWidth: '650px'}"
    @update:show="onCancel"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('performance.add_tracker_log') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-input-field
          v-model="trackerLog.log"
          :label="$t('performance.log')"
          :placeholder="$t('general.type_here')"
          :rules="rules.log"
          required
        />
      </oxd-form-row>
      <oxd-form-row>
        <div class="orangehrm-add-tracker-log-ratings-container">
          <tracker-log-rating-button
            :label="$t('performance.positive')"
            :selected="rating"
            type="positive"
            @click="onClickPositive"
          />
          <tracker-log-rating-button
            :label="$t('performance.negative')"
            :selected="!rating"
            type="negative"
            @click="onClickNegative"
          />
        </div>
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          v-model="trackerLog.comment"
          type="textarea"
          :label="$t('general.comment')"
          :placeholder="$t('general.type_here')"
          :rules="rules.comment"
          required
        />
      </oxd-form-row>
      <oxd-divider />
      <oxd-form-actions class="orangehrm-form-action">
        <required-text />
        <oxd-button
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <oxd-button
          display-type="secondary"
          :label="$t('general.save')"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import {OxdDialog} from '@ohrm/oxd';
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import TrackerLogRatingButton from '@/orangehrmPerformancePlugin/components/TrackerLogRatingButton';

const trackerLogModel = {
  log: '',
  comment: '',
};

export default {
  name: 'AddTrackerLogModal',
  components: {
    'oxd-dialog': OxdDialog,
    'tracker-log-rating-button': TrackerLogRatingButton,
  },
  props: {
    trackerId: {
      type: Number,
      required: true,
    },
  },
  emits: ['close'],
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/performance/trackers/${props.trackerId}/logs`,
    );

    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      rating: true,
      trackerLog: {...trackerLogModel},
      rules: {
        log: [required, shouldNotExceedCharLength(150)],
        comment: [required, shouldNotExceedCharLength(3000)],
      },
    };
  },
  methods: {
    onClickPositive() {
      this.rating = true;
    },
    onClickNegative() {
      this.rating = false;
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          log: this.trackerLog.log,
          comment: this.trackerLog.comment,
          achievement: this.rating ? 1 : 2,
        })
        .then(() => {
          this.$toast.saveSuccess();
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close');
    },
  },
};
</script>

<style src="./tracker-log-modal.scss" lang="scss" scoped></style>

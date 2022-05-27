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
    <oxd-form :loading="isLoading" @submitValid="onSave">
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
          <oxd-button
            display-type="text"
            :label="$t('performance.positive')"
            icon-name="hand-thumbs-up-fill"
            :class="{
              'orangehrm-add-tracker-rating-button': true,
              '--positive': true,
              '--deselected': !rating,
            }"
            @click="onClickPositive"
          />
          <oxd-button
            display-type="text"
            :label="$t('performance.negative')"
            icon-name="hand-thumbs-down-fill"
            :class="{
              'orangehrm-add-tracker-rating-button': true,
              '--negative': true,
              '--deselected': !!rating,
            }"
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
import Dialog from '@ohrm/oxd/core/components/Dialog/Dialog';
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';

const trackerLogModel = {
  log: '',
  comment: '',
};

export default {
  name: 'AddTrackerLogModal',
  components: {
    'oxd-dialog': Dialog,
  },
  props: {
    trackerId: {
      type: Number,
      required: true,
    },
  },
  emits: ['close'],
  setup(props) {
    // TODO change to window.appGlobal.baseUrl
    const http = new APIService(
      'https://942be86c-56c6-42e3-ac85-874a20c7ce9b.mock.pstmn.io',
      `api/v2/performance/trackers/${props.trackerId}/logs`,
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
          this.$toast.saveSuccess;
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close');
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-add-tracker-log-ratings-container {
  padding-bottom: 0.6rem;
}

.orangehrm-add-tracker-rating-button {
  margin-right: 0.6rem;
  padding-right: 0.6rem;
  padding-left: 0.6rem;
}

.--positive {
  ::v-deep(.oxd-icon) {
    color: $oxd-secondary-four-color;
  }
}

.--negative {
  ::v-deep(.oxd-icon) {
    color: $oxd-feedback-danger-color;
  }
}

.--deselected {
  background-color: $oxd-white-color;
}
</style>

<template>
  <oxd-dialog
    :style="{width: '90%', maxWidth: '450px'}"
    @update:show="onCancel"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('performance.edit_tracker_log') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-input-field
          v-model="trackerLog.log"
          :label="$t('performance.log')"
          required
          :placeholder="$t('general.type_here')"
          :rules="rules.log"
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
              '--deselected': positiveSelected === false,
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
              '--deselected': negativeSelected === false,
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
  name: 'EditTrackerLogModal',
  components: {
    'oxd-dialog': Dialog,
  },
  props: {
    trackerId: {
      type: Number,
      required: true,
    },
    trackerLogId: {
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
      positiveSelected: false,
      negativeSelected: false,
      trackerLog: {...trackerLogModel},
      rules: {
        log: [required, shouldNotExceedCharLength(150)],
        comment: [required, shouldNotExceedCharLength(3000)],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.trackerLogId)
      .then(response => {
        const {data} = response.data;
        this.trackerLog.log = data.log;
        this.trackerLog.comment = data.comment;
        this.positiveSelected = data.achievement === 1;
        this.negativeSelected = data.achievement === 2;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onClickPositive() {
      this.positiveSelected = true;
      this.negativeSelected = false;
    },
    onClickNegative() {
      this.negativeSelected = true;
      this.positiveSelected = false;
    },
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.trackerLogId, {
          log: this.trackerLog.log,
          comment: this.trackerLog.comment,
          achievement: this.positiveSelected ? 1 : 2,
        })
        .then(() => {
          this.$toast.updateSuccess();
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

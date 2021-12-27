<template>
  <oxd-form :loading="isLoading" @submitValid="onSave">
    <oxd-grid :cols="2" v-if="punchIn.punchedInTime">
      <oxd-grid-item>
        <br />
        <oxd-text type="subtitle-2">
          <b>Punched In Time</b>
          <br />
          {{ punchIn.punchedInTime }}
        </oxd-text>
        <br />
      </oxd-grid-item>
    </oxd-grid>

    <oxd-form-row>
      <oxd-grid :cols="4" class="orangehrm-full-width-grid">
        <!-- Date Selector -->
        <oxd-grid-item>
          <oxd-input-field
            v-model="punchIn.date"
            :label="$t('general.date')"
            type="date"
            :rules="rules.date"
            :disabled="!editable"
          />
        </oxd-grid-item>

        <!-- Time  Selector -->
        <oxd-grid-item>
          <oxd-input-field
            v-model="punchIn.time"
            :label="$t('time.time')"
            type="time"
            :rules="rules.time"
            :disabled="!editable"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>

    <!-- Note input -->
    <oxd-form-row>
      <oxd-grid :cols="2" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <oxd-input-field
            v-model="punchIn.note"
            :label="$t('general.date')"
            type="textarea"
            placeholder="Type here."
            :rules="rules.note"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>
    <oxd-divider class="orangehrm-horizontal-margin" />
    <oxd-form-actions>
      <submit-button :disabled="punched" :label="!recordId ? 'In' : 'Out'" />
    </oxd-form-actions>
  </oxd-form>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {APIService} from '@ohrm/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';
import {freshDate, formatDate} from '@/core/util/helper/datefns';

export default {
  props: {
    recordId: {
      type: String,
      default: null,
    },
    editable: {
      type: Boolean,
      default: true,
    },
  },

  setup() {
    //date punch-in data submiting Api
    const http = new APIService(
      'https://884b404a-f4d0-4908-9eb5-ef0c8afec15c.mock.pstmn.io',
      '/api/v2/attendance/records',
    );

    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,

      punchIn: {
        date: null,
        time: null,
        note: null,
      },

      punched: false,

      rules: {
        date: [required],
        note: [shouldNotExceedCharLength(255)],
        time: [required],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;

    this.http
      .getAll({date: formatDate(freshDate(), 'yyyy-MM-dd')})
      .then(res => {
        const {data} = res.data;
        this.punchIn.date = data[0].punchIn.date;
        this.punchIn.time = data[0].punchIn.time;

        this.isLoading = false;
      });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      const payload = {
        date: this.punchIn.date,
        time: this.punchIn.time,
        note: this.punchIn.note,
      };

      if (!this.recordId) {
        this.http
          .create(payload)
          .then(() => {
            this.punched = true;
            return this.$toast.saveSuccess();
          })
          .then(() => navigate('/attendance/punchOut'))
          .finally(() => {
            this.isLoading = false;
          });
      } else {
        this.http
          .update(this.recordId, payload)
          .then(() => {
            this.punched = true;
            return this.$toast.updateSuccess();
          })
          .then(() => navigate('/attendance/punchIn'))
          .finally(() => {
            this.isLoading = false;
          });
      }
    },
  },
};
</script>

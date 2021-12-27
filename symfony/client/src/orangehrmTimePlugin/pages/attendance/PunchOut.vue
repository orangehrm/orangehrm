<template>
  <div>
    <div class="orangehrm-background-container">
      <div class="orangehrm-card-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          Punch Out
        </oxd-text>

        <oxd-divider />

        <!-- wrapper -->
        <div class="orangehrm-paper-container">
          <oxd-form :loading="isLoading" @submitValid="onSave">
            <oxd-grid :cols="2">
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
                    label="Date"
                    type="date"
                    :rules="rules.date"
                  />
                </oxd-grid-item>

                <!-- Time  Selector -->
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="punchIn.time"
                    label="Time"
                    type="time"
                    :rules="rules.time"
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
                    label="Note"
                    type="textarea"
                    placeholder="Type here."
                    :rules="rules.note"
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-divider class="orangehrm-horizontal-margin" />
            <oxd-form-actions>
              <submit-button :disabled="punched" value="Out" />
            </oxd-form-actions>
          </oxd-form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {APIService} from '@ohrm/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';

export default {
  setup() {
    //on save data submiting
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
        punchedInTime: null,
      },

      punched: false,

      rules: {
        date: [
          required,
          promiseDebounce(() => this.timeValidator('date'), 1000),
        ],
        time: [required, promiseDebounce(this.timeValidator, 1000)],
        note: [shouldNotExceedCharLength(255)],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;

    this.http
      .request({url: '/api/v2/attendance/records/latest', method: 'GET'})
      .then(res => {
        const {data} = res.data;
        this.punchIn.punchedInTime =
          data.punchIn.date + ' ' + data.punchIn.time;
        this.punchIn.date = data.punchOut.date;
        this.punchIn.time = data.punchOut.time;

        this.isLoading = false;
      });
  },
  methods: {
    //time and date input validating api
    timeValidator(field) {
      return this.http
        .request({
          url: '/api/v2/attendance/validatedate',
          method: 'GET',
          params: {punchedInTime: this.punchIn.punchedInTime},
        })
        .then(res => {
          return res.data.data === true
            ? true
            : field === 'date'
            ? ''
            : 'Overlapping Records Found';
        });
    },

    onSave() {
      this.isLoading = true;
      const punchInData = {
        date: this.punchIn.date,
        time: this.punchIn.time,
        note: this.punchIn.note,
        timezoneOffset: new Date().getTimezoneOffset(),
      };

      this.http
        .create(punchInData)
        .then(() => {
          this.punched = true;
          return this.$toast.saveSuccess();
        })
        .then(() => navigate('/attendance/punchIn'))
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>

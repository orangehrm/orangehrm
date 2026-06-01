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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          Slack Notification Configuration
        </oxd-text>
        <oxd-switch-input
          v-model="configuration.enable"
          label-position="left"
          :option-label="$t('general.enable')"
        />
      </div>
      <oxd-divider />

      <oxd-form ref="formRef" :loading="isLoading" @submit-valid="onClickSave">
        <oxd-text tag="p" class="orangehrm-subtitle">
          General settings
        </oxd-text>
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.timezone"
                type="select"
                :options="timezoneOptions"
                :show-empty-selector="false"
                :rules="rules.timezone"
                label="Timezone"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                IANA timezone name (e.g. Europe/London). Used to determine
                calendar day for notifications.
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.sendTime"
                type="time"
                :step="60"
                :rules="rules.sendTime"
                label="Send time"
                placeholder="HH:mm"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                Local time when daily notifications should be sent (HH:mm).
              </oxd-text>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider class="orangehrm-form-divider" />

        <oxd-text tag="p" class="orangehrm-subtitle">
          Notification registration
        </oxd-text>
        <oxd-text
          class="orangehrm-input-hint orangehrm-slack-section-hint"
          tag="p"
        >
          Add multiple rows to send the same event type to different Slack
          channels, or to filter by subunit.
        </oxd-text>

        <div
          v-for="(registration, index) in registrations"
          :key="registration._key"
          class="orangehrm-slack-registration"
        >
          <div
            v-if="registrations.length > 1"
            class="orangehrm-slack-registration-header"
          >
            <oxd-icon-button name="trash" @click="onClickRemove(index)" />
          </div>

          <oxd-form-row>
            <oxd-grid :cols="3" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-field
                  v-model="registration.eventType"
                  type="select"
                  :options="eventTypeOptions"
                  :show-empty-selector="false"
                  :rules="rules.eventType"
                  label="Event type"
                  required
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <oxd-input-field
                  v-model="registration.webhookUrl"
                  :rules="rules.webhookUrl"
                  :placeholder="
                    registration.hasWebhookUrl ? maskedWebhookPlaceholder : null
                  "
                  label="Slack Incoming Webhook URL"
                  required
                />
                <oxd-text class="orangehrm-input-hint" tag="p">
                  Create an Incoming Webhook in your Slack workspace and paste
                  the URL here. Must be HTTPS.
                </oxd-text>
              </oxd-grid-item>
              <oxd-grid-item>
                <oxd-input-field
                  v-model="registration.channelLabel"
                  :rules="rules.channelLabel"
                  label="Slack channel name (optional)"
                />
                <oxd-text class="orangehrm-input-hint" tag="p">
                  Label only (e.g. #hr-team). The webhook URL determines the
                  actual channel.
                </oxd-text>
              </oxd-grid-item>

              <oxd-grid-item class="--offset-row-2">
                <oxd-input-field
                  v-model="registration.subunit"
                  type="select"
                  :options="subunitOptions"
                  label="Sub Unit"
                />
                <oxd-text class="orangehrm-input-hint" tag="p">
                  Filter by subunit: leave empty to include all employees, or
                  pick a subunit to limit notifications.
                </oxd-text>
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <div class="orangehrm-slack-registration-actions">
            <oxd-button
              type="button"
              display-type="ghost"
              label="Send test"
              :disabled="!registration.webhookUrl"
              @click="onClickSendTest(index)"
            />
          </div>

          <oxd-divider class="orangehrm-form-divider" />
        </div>

        <div class="orangehrm-slack-add-row">
          <oxd-button
            type="button"
            display-type="secondary"
            icon-name="plus"
            label="Add registration"
            @click="onClickAddRegistration"
          />
        </div>
        <oxd-text class="orangehrm-input-hint" tag="p">
          Ensure the server cron runs
          <strong>php bin/console orangehrm:run-schedule</strong> regularly
          (e.g. every 5-15 minutes) so scheduled notifications can run.
        </oxd-text>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <submit-button :label="$t('general.save')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
  validTimeFormat,
} from '@/core/util/validation/rules';
import useForm from '@/core/util/composable/useForm';
import {reloadPage} from '@/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import {OxdIconButton, OxdSwitchInput} from '@ohrm/oxd';

const SLACK_WEBHOOK_URL_REGEX =
  /^https:\/\/hooks\.slack\.com\/services\/[A-Z0-9]+\/[A-Z0-9]+\/[A-Za-z0-9]+$/;

const validSlackWebhookUrl = function (value) {
  if (!value) return true;
  return (
    SLACK_WEBHOOK_URL_REGEX.test(value) ||
    'Should be a valid Slack Incoming Webhook URL (https://hooks.slack.com/services/…)'
  );
};

let registrationKeySeed = 0;
const newRegistration = () => ({
  _key: ++registrationKeySeed,
  id: null,
  eventType: null,
  webhookUrl: '',
  hasWebhookUrl: false,
  channelLabel: '',
  subunit: null,
  active: true,
});

export default {
  components: {
    'oxd-icon-button': OxdIconButton,
    'oxd-switch-input': OxdSwitchInput,
  },

  setup() {
    const configHttp = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/slack-notification/config',
    );
    const registrationsHttp = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/slack-notification/registrations',
    );
    const subunitsHttp = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/subunits',
    );
    const timezonesHttp = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/attendance/timezones',
    );
    const {formRef} = useForm();

    return {
      configHttp,
      registrationsHttp,
      subunitsHttp,
      timezonesHttp,
      formRef,
    };
  },

  data() {
    return {
      isLoading: false,
      maskedWebhookPlaceholder:
        'https://hooks.slack.com/services/…/…/… (saved)',
      configuration: {
        enable: false,
        timezone: null,
        sendTime: '09:00',
      },
      registrations: [newRegistration()],
      timezoneOptions: [],
      subunitOptions: [],
      eventTypeOptions: [
        {id: 'BIRTHDAY', label: 'Birthday'},
        {id: 'LEAVE_TODAY', label: 'Employees on Leave Today'},
      ],
      rules: {
        timezone: [required],
        sendTime: [required, validTimeFormat],
        eventType: [required],
        webhookUrl: [
          (v) => {
            const reg = this.currentRegistrationForRule(v);
            return reg && reg.hasWebhookUrl ? true : required(v);
          },
          shouldNotExceedCharLength(512),
          validSlackWebhookUrl,
        ],
        channelLabel: [shouldNotExceedCharLength(100)],
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    Promise.all([
      this.timezonesHttp.getAll().then(({data}) => {
        this.timezoneOptions = data.data.map((tz) => ({
          id: tz.name,
          label: `(GMT${tz.label}) ${tz.name}`,
        }));
      }),
      this.subunitsHttp.getAll().then(({data}) => {
        this.subunitOptions = data.data.map((item) => ({
          id: item.id,
          label: item.name,
          _indent: item.level ? item.level + 1 : 1,
        }));
      }),
      this.configHttp.getAll().then(({data}) => {
        const settings = data.data || {};
        this.configuration.enable = !!settings.enable;
        this.configuration.timezone =
          this.timezoneOptions.find((tz) => tz.id === settings.timezone) ||
          null;
        if (settings.dailySendTime) {
          this.configuration.sendTime = settings.dailySendTime;
        }
      }),
      this.registrationsHttp.getAll().then(({data}) => {
        const rows = data.data || [];
        if (rows.length === 0) return;
        this.registrations = rows.map((row) => ({
          _key: ++registrationKeySeed,
          id: row.id,
          eventType:
            this.eventTypeOptions.find((o) => o.id === row.eventType) || null,
          webhookUrl: '',
          hasWebhookUrl: !!row.webhookUrl,
          channelLabel: row.channelLabel || '',
          subunit: row.subunit
            ? {id: row.subunit.id, label: row.subunit.name}
            : null,
          active: row.active !== false,
        }));
      }),
    ]).finally(() => {
      this.isLoading = false;
    });
  },

  methods: {
    currentRegistrationForRule(value) {
      // Rule callbacks don't carry row context; match by reference identity
      // against the row whose webhookUrl currently equals this value.
      return this.registrations.find((r) => r.webhookUrl === value);
    },
    onClickAddRegistration() {
      this.registrations.push(newRegistration());
    },
    onClickRemove(index) {
      this.registrations.splice(index, 1);
    },
    onClickSendTest(index) {
      const reg = this.registrations[index];
      if (!reg.webhookUrl && !reg.hasWebhookUrl) return;
      this.isLoading = true;
      const body = {
        eventType: reg.eventType?.id,
        webhookUrl: reg.webhookUrl,
      };
      if (reg.subunit?.id) {
        body.subunitId = reg.subunit.id;
      }
      this.registrationsHttp
        .request({
          method: 'POST',
          url: reg.id
            ? `/api/v2/admin/slack-notification/registrations/${reg.id}/test`
            : '/api/v2/admin/slack-notification/registrations/test',
          data: body,
        })
        .then(() =>
          this.$toast.success({
            title: this.$t('general.success'),
            message: 'Test message sent to Slack.',
          }),
        )
        .catch(() =>
          this.$toast.error({
            title: this.$t('general.error'),
            message: 'Failed to send test message. Check the webhook URL.',
          }),
        )
        .finally(() => {
          this.isLoading = false;
        });
    },
    getRequestBody() {
      return {
        enable: this.configuration.enable,
        timezone: this.configuration.timezone?.id || null,
        dailySendTime: this.configuration.sendTime,
        registrations: this.registrations.map((reg) => ({
          id: reg.id,
          eventType: reg.eventType?.id,
          webhookUrl: reg.webhookUrl || null,
          channelLabel: reg.channelLabel || null,
          subunitId: reg.subunit?.id || null,
          active: reg.active !== false,
        })),
      };
    },
    onClickSave() {
      this.isLoading = true;
      this.configHttp
        .request({
          method: 'PUT',
          data: this.getRequestBody(),
        })
        .then(() => this.$toast.updateSuccess())
        .finally(() => reloadPage());
    },
  },
};
</script>

<style src="./slack-notification-configuration.scss" lang="scss" scoped></style>

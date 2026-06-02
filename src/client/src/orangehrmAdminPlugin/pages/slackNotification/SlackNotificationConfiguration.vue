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
    <!-- Single config card: page title + Enable toggle + registration form. -->
    <div class="orangehrm-card-container orangehrm-slack-config-card">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          Slack Notification Configuration
        </oxd-text>
        <oxd-switch-input
          v-model="globalEnabled"
          label-position="left"
          option-label="Enable"
          @update:model-value="onToggleEnable"
        />
      </div>

      <oxd-divider class="orangehrm-slack-section-divider" />

      <oxd-text tag="p" class="orangehrm-subtitle">
        {{
          formMode === 'edit'
            ? 'Edit Notification Registration'
            : 'Notification Registration'
        }}
      </oxd-text>
      <oxd-text class="orangehrm-input-hint" tag="p">
        Configure a Slack channel to receive automated notifications. Each
        registration has its own timezone and send time.
      </oxd-text>
      <oxd-divider />

      <oxd-form
        :key="formKey"
        ref="formRef"
        :loading="isLoading"
        @submit-valid="onClickSave"
      >
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="form.eventType"
                type="select"
                :options="eventTypeOptions"
                :show-empty-selector="false"
                :rules="rules.eventType"
                label="Notification type"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="form.webhookUrl"
                :rules="rules.webhookUrl"
                :placeholder="
                  form.hasStoredWebhookUrl ? maskedWebhookPlaceholder : null
                "
                label="Slack Incoming Webhook URL"
                :required="!form.hasStoredWebhookUrl"
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                Create an Incoming Webhook in your Slack workspace and paste the
                URL here. Must be HTTPS.
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="form.channelLabel"
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
                v-model="form.subunits"
                type="multiselect"
                :options="subunitOptions"
                label="Sub Units"
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                Filter by subunit(s): leave empty to include all employees, or
                pick one or more subunits to limit notifications.
              </oxd-text>
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-field
                v-model="form.timezone"
                type="select"
                :options="timezoneOptions"
                :show-empty-selector="false"
                :rules="rules.timezone"
                label="Timezone"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                IANA timezone for this notification.
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="form.sendTime"
                type="time"
                :step="60"
                :rules="rules.sendTime"
                label="Send time"
                placeholder="HH:mm"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                Local send time (HH:mm).
              </oxd-text>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider class="orangehrm-form-divider" />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            type="button"
            display-type="ghost"
            label="Send test"
            :disabled="!canSendTest"
            @click="onClickSendTest"
          />
          <oxd-button
            v-if="formMode === 'edit'"
            type="button"
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onClickCancel"
          />
          <submit-button
            :label="formMode === 'edit' ? 'Update' : '+ Add Registration'"
          />
        </oxd-form-actions>
      </oxd-form>
    </div>

    <!-- Registration table is its own card, visually separated from the config card. -->
    <div class="orangehrm-card-container orangehrm-slack-table-card">
      <oxd-text tag="p" class="orangehrm-subtitle">
        Notification registrations
      </oxd-text>

      <table-header
        :total="registrations.length"
        :selected="0"
        :show-divider="false"
      />

      <div class="orangehrm-container">
        <oxd-card-table
          :loading="isLoading"
          :headers="tableHeaders"
          :items="tableItems"
          :selectable="false"
          :clickable="false"
          row-decorator="oxd-table-decorator-card"
        />
        <p
          v-if="!isLoading && registrations.length === 0"
          class="orangehrm-input-hint"
        >
          No registrations yet. Fill the form above and click
          <strong>+ Add Registration</strong> to create one.
        </p>
      </div>

      <oxd-text class="orangehrm-input-hint" tag="p">
        Ensure the server cron runs
        <strong>php bin/console orangehrm:run-schedule</strong> regularly (e.g.
        every 5–15 minutes) so scheduled notifications can run.
      </oxd-text>
    </div>

    <!-- Delete confirmation -->
    <delete-confirmation ref="deleteDialog"></delete-confirmation>

    <!-- Duplicate-registration warning (FR-11) — fires when event + webhook (or stored hash)
         match another registration. Generic OHRM ConfirmationDialog pattern. -->
    <confirmation-dialog
      ref="duplicateDialog"
      title="Possible duplicate registration"
      subtitle="A registration with the same event type and Slack channel already exists. Saving this will cause the same message to be sent more than once per day. Continue anyway?"
      confirm-label="Save anyway"
      cancel-label="Go back &amp; fix"
      confirm-button-type="label-warn"
      icon="warning"
    ></confirmation-dialog>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
  validTimeFormat,
} from '@/core/util/validation/rules';
import useForm from '@/core/util/composable/useForm';
import {APIService} from '@ohrm/core/util/services/api.service';
import {OxdSwitchInput, OxdSpinner} from '@ohrm/oxd';
import TableHeader from '@ohrm/components/table/TableHeader';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import ConfirmationDialog from '@/core/components/dialogs/ConfirmationDialog';

const SLACK_WEBHOOK_URL_REGEX =
  /^https:\/\/hooks\.slack\.com\/services\/[A-Z0-9]+\/[A-Z0-9]+\/[A-Za-z0-9]+$/;

const validSlackWebhookUrl = function (value) {
  if (!value) return true;
  return (
    SLACK_WEBHOOK_URL_REGEX.test(value) ||
    'Should be a valid Slack Incoming Webhook URL (https://hooks.slack.com/services/…)'
  );
};

/**
 * Client-side mirror of SlackRegistrationService::maskWebhookUrl(). Lets us compare a
 * just-typed full URL against the masked URL stored on existing registrations.
 */
const maskWebhookUrl = function (url) {
  if (!url) return null;
  const m = url.match(
    /^(https:\/\/hooks\.slack\.com\/services\/[A-Z0-9]+\/[A-Z0-9]+)\/.+$/,
  );
  if (m) return m[1] + '/…';
  const parts = url.split('/');
  if (parts.length > 2) {
    parts.pop();
    return parts.join('/') + '/…';
  }
  return '…';
};

const emptyForm = () => ({
  id: null,
  eventType: null,
  webhookUrl: '',
  hasStoredWebhookUrl: false,
  channelLabel: '',
  subunits: [],
  timezone: null,
  sendTime: '09:00',
  active: true,
});

export default {
  components: {
    'oxd-switch-input': OxdSwitchInput,
    'table-header': TableHeader,
    'delete-confirmation': DeleteConfirmationDialog,
    'confirmation-dialog': ConfirmationDialog,
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
        'https://hooks.slack.com/services/…/…/… (saved — leave blank to keep)',
      globalEnabled: false,

      // Form state — form is always visible. Mode flips between 'add' (blank) and
      // 'edit' (populated from a table row).
      formMode: 'add',
      form: emptyForm(),
      // Bumped after every reset so Vue destroys + remounts the oxd-form with
      // pristine validation state. Without this, a successful save blanks the
      // v-model values but the form's internal `touched` state persists, so
      // required-rule errors light up on the now-empty fields.
      formKey: 0,

      // Table state
      registrations: [],

      // Reference data
      timezoneOptions: [],
      subunitOptions: [],
      eventTypeOptions: [
        {id: 'BIRTHDAY', label: 'Birthday'},
        {id: 'LEAVE_TODAY', label: 'Employees on Leave Today'},
      ],

      tableHeaders: [
        {
          name: 'eventType',
          title: 'Notification type',
          style: {flex: '18%'},
        },
        {name: 'channelLabel', title: 'Channel', style: {flex: '13%'}},
        {name: 'subunit', title: 'Sub Unit', style: {flex: '15%'}},
        {name: 'timezone', title: 'Timezone', style: {flex: '15%'}},
        {name: 'sendTime', title: 'Send time', style: {flex: '9%'}},
        {
          name: 'statusToggle',
          title: 'Status',
          slot: 'action',
          style: {flex: '12%'},
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.statusCellRenderer,
        },
        {
          name: 'actions',
          title: this.$t('general.actions'),
          slot: 'action',
          style: {flex: '15%'},
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.actionCellRenderer,
        },
      ],

      rules: {
        eventType: [required],
        timezone: [required],
        sendTime: [required, validTimeFormat],
        webhookUrl: [
          (v) => (this.form.hasStoredWebhookUrl ? true : required(v)),
          shouldNotExceedCharLength(512),
          validSlackWebhookUrl,
        ],
        channelLabel: [shouldNotExceedCharLength(100)],
      },
    };
  },

  computed: {
    canSendTest() {
      // Either we typed a URL, or we have a stored URL on an existing row
      return !!this.form.webhookUrl || !!this.form.id;
    },
    tableItems() {
      return this.registrations.map((row, index) => {
        const subunitNames = (row.subunits || []).map((s) => s.name);
        return {
          id: row.id,
          index,
          eventType: this.labelFor(this.eventTypeOptions, row.eventType),
          channelLabel: row.channelLabel || '—',
          subunit:
            subunitNames.length === 0
              ? 'All employees'
              : subunitNames.join(', '),
          timezone: row.timezone || 'UTC',
          sendTime: row.dailySendTime || '09:00',
          active: row.active !== false,
          _loading: row._loading === true,
          _raw: row,
        };
      });
    },
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
        this.globalEnabled = !!settings.enable;
      }),
      this.reloadRegistrations(),
    ]).finally(() => {
      this.isLoading = false;
    });
  },

  methods: {
    labelFor(options, id) {
      const found = options.find((o) => o.id === id);
      return found ? found.label : id;
    },

    reloadRegistrations() {
      return this.registrationsHttp.getAll().then(({data}) => {
        this.registrations = (data.data || []).slice();
      });
    },

    /* ───────── add / edit form ───────── */
    resetForm() {
      this.form = emptyForm();
      this.formMode = 'add';
      // Force <oxd-form> to remount with a clean slate — without this the form's
      // internal touched/dirty state survives the data wipe, and required-rule
      // errors immediately fire on the now-empty fields.
      this.formKey += 1;
    },

    onClickEdit(item) {
      const row = this.registrations.find((r) => r.id === item.id);
      if (!row) return;
      this.form = {
        id: row.id,
        eventType:
          this.eventTypeOptions.find((o) => o.id === row.eventType) || null,
        webhookUrl: '',
        hasStoredWebhookUrl: !!row.webhookUrl,
        channelLabel: row.channelLabel || '',
        subunits: (row.subunits || []).map((s) => ({
          id: s.id,
          label: s.name,
        })),
        timezone:
          this.timezoneOptions.find((tz) => tz.id === row.timezone) || null,
        sendTime: row.dailySendTime || '09:00',
        active: row.active !== false,
      };
      this.formMode = 'edit';
      // Scroll to the form so admin sees the populated fields without hunting.
      window.scrollTo({top: 0, behavior: 'smooth'});
    },

    onClickCancel() {
      this.resetForm();
    },

    /**
     * FR-11: warn (don't block) if the form would create or update into a registration
     * that duplicates another one. "Duplicate" = same eventType AND same webhook channel.
     * Subunit overlap is considered too — same event + same channel + overlapping subunit
     * filters means the same employee can be notified twice on the same day.
     */
    findDuplicate() {
      if (!this.form.eventType?.id) return null;

      const myEventType = this.form.eventType.id;
      const mySubunitIds = (this.form.subunits || []).map((s) => s.id);
      // Mask the typed URL so we can compare against masked URLs already in the table.
      // On edit-without-retype, fall back to the original row's stored masked URL.
      let myMaskedUrl = null;
      if (this.form.webhookUrl) {
        myMaskedUrl = maskWebhookUrl(this.form.webhookUrl);
      } else if (this.formMode === 'edit' && this.form.id) {
        const myself = this.registrations.find((r) => r.id === this.form.id);
        myMaskedUrl = myself ? myself.webhookUrl : null;
      }
      if (!myMaskedUrl) return null;

      return (
        this.registrations.find((r) => {
          if (this.formMode === 'edit' && r.id === this.form.id) return false;
          if (r.eventType !== myEventType) return false;
          if (r.webhookUrl !== myMaskedUrl) return false;
          // If either side has no subunit filter (all-employees), or the two filters overlap,
          // they hit the same audience → duplicate.
          const otherIds = (r.subunits || []).map((s) => s.id);
          if (mySubunitIds.length === 0 || otherIds.length === 0) return true;
          return mySubunitIds.some((id) => otherIds.includes(id));
        }) || null
      );
    },

    onClickSave() {
      const duplicate = this.findDuplicate();
      if (duplicate) {
        this.$refs.duplicateDialog.showDialog().then((confirmation) => {
          if (confirmation === 'ok') {
            this.submitSave();
          }
          // 'cancel' → admin returns to the form to adjust event / webhook / subunit
        });
        return;
      }
      this.submitSave();
    },

    submitSave() {
      const body = {
        eventType: this.form.eventType?.id,
        webhookUrl: this.form.webhookUrl || null,
        channelLabel: this.form.channelLabel || null,
        subunitIds: (this.form.subunits || []).map((s) => s.id),
        timezone: this.form.timezone?.id || null,
        dailySendTime: this.form.sendTime,
        active: this.form.active !== false,
      };
      this.isLoading = true;
      const request =
        this.formMode === 'edit' && this.form.id
          ? this.registrationsHttp.update(this.form.id, body)
          : this.registrationsHttp.create(body);
      request
        .then(() => {
          this.$toast.saveSuccess();
          this.resetForm();
          return this.reloadRegistrations();
        })
        .catch(() =>
          this.$toast.error({
            title: this.$t('general.error'),
            message: 'Failed to save. Check the webhook URL and try again.',
          }),
        )
        .finally(() => {
          this.isLoading = false;
        });
    },

    /* ───────── send test ───────── */
    sendTest(idOrNull, webhookUrl) {
      this.isLoading = true;
      const body = {
        eventType: this.form.eventType?.id || 'BIRTHDAY',
      };
      if (webhookUrl) body.webhookUrl = webhookUrl;
      return this.registrationsHttp
        .request({
          method: 'POST',
          url: idOrNull
            ? `/api/v2/admin/slack-notification/registrations/${idOrNull}/test`
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

    onClickSendTest() {
      if (this.form.webhookUrl) {
        this.sendTest(null, this.form.webhookUrl);
      } else if (this.form.id) {
        this.sendTest(this.form.id, null);
      }
    },

    onClickRowSendTest(item) {
      this.sendTest(item.id, null);
    },

    /* ───────── delete ───────── */
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation !== 'ok') return;
        this.isLoading = true;
        this.registrationsHttp
          .deleteAll({ids: [item.id]})
          .then(() => {
            this.$toast.deleteSuccess();
            return this.reloadRegistrations();
          })
          .finally(() => {
            this.isLoading = false;
          });
      });
    },

    /* ───────── status column renderer — per-row active toggle ───────── */
    statusCellRenderer(...args) {
      const [, , , row] = args;

      if (row._loading) {
        return {
          props: {
            header: {
              cellConfig: {
                spinner: {
                  component: OxdSpinner,
                  props: {withContainer: false},
                },
              },
            },
          },
        };
      }

      return {
        props: {
          header: {
            cellConfig: {
              activeSwitch: {
                component: OxdSwitchInput,
                props: {
                  modelValue: row.active,
                  'onUpdate:modelValue': ($event) =>
                    this.onToggleActive(row, $event),
                },
              },
            },
          },
        },
      };
    },

    onToggleActive(row, newValue) {
      const existing = this.registrations.find((r) => r.id === row.id);
      if (!existing) return;
      // Optimistic: show loader, send PATCH-style PUT with only the active flag.
      this.$set
        ? this.$set(existing, '_loading', true)
        : (existing._loading = true);
      this.registrationsHttp
        .update(row.id, {active: newValue})
        .then(({data}) => {
          existing.active = data.data.active;
          this.$toast.updateSuccess();
        })
        .catch(() => {
          this.$toast.error({
            title: this.$t('general.error'),
            message: 'Could not update status. Refresh and try again.',
          });
        })
        .finally(() => {
          existing._loading = false;
        });
    },

    /* ───────── action column renderer (oxd-card-table cellConfig pattern) ───────── */
    actionCellRenderer(...args) {
      const [, , , row] = args;
      const sendTest = {
        component: 'oxd-icon-button',
        props: {name: 'send-fill', title: 'Send test'},
        onClick: () => this.onClickRowSendTest(row),
      };
      const edit = {
        component: 'oxd-icon-button',
        props: {name: 'pencil-fill'},
        onClick: () => this.onClickEdit(row),
      };
      const del = {
        component: 'oxd-icon-button',
        props: {name: 'trash'},
        onClick: () => this.onClickDelete(row),
      };
      return {
        props: {
          header: {
            cellConfig: {sendTest, edit, delete: del},
          },
        },
      };
    },

    /* ───────── global toggle ───────── */
    onToggleEnable(value) {
      this.configHttp
        .request({
          method: 'PUT',
          data: {enable: value},
        })
        .then(() => this.$toast.updateSuccess())
        .catch(() => {
          // revert on failure
          this.globalEnabled = !value;
          this.$toast.error({
            title: this.$t('general.error'),
            message: 'Could not update global toggle.',
          });
        });
    },
  },
};
</script>

<style src="./slack-notification-configuration.scss" lang="scss" scoped></style>

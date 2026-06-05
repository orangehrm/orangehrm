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
    <!-- Single merged card: page title + Enable toggle + form + registrations table. -->
    <div class="orangehrm-card-container orangehrm-slack-config-card">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          Workspace Notification Configurations
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
        Configure a workspace channel to receive automated notifications. Each
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
                label="Notification Type"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="form.provider"
                type="select"
                :options="providerOptions"
                :show-empty-selector="false"
                :rules="rules.provider"
                label="Platform"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="form.webhookUrl"
                :rules="rules.webhookUrl"
                :placeholder="
                  effectiveHasStoredUrl
                    ? maskedWebhookPlaceholder
                    : webhookUrlPlaceholder
                "
                :label="webhookUrlLabel"
                :required="!effectiveHasStoredUrl"
              />
              <oxd-text
                v-if="platformChanged"
                class="orangehrm-input-hint orangehrm-platform-changed-hint"
                tag="p"
              >
                Chat Platform changed. Paste a new
                {{ form.provider?.label }} Webhook URL for this channel before
                saving.
              </oxd-text>
              <oxd-text v-else class="orangehrm-input-hint" tag="p">
                {{ webhookUrlHint }}
              </oxd-text>
            </oxd-grid-item>

            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="form.channelLabel"
                :rules="rules.channelLabel"
                label="Channel name (optional)"
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                Label only (e.g. #hr-team). The webhook URL determines the
                actual destination.
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="form.subunit"
                type="select"
                :options="subunitOptions"
                label="Sub Unit"
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                Filter by subunit: leave empty to include all employees, or pick
                one to filter notifications. To notify the same channel for
                multiple subunits, register a separate row per subunit.
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
                The IANA timezone for this notification is auto-detected from
                your browser. Please change it if the channel is in a different
                region.
              </oxd-text>
            </oxd-grid-item>

            <oxd-grid-item class="--offset-row-3">
              <oxd-input-field
                v-model="form.sendTime"
                type="time"
                :step="1"
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
            label="Send Test"
            :disabled="!canSendTest"
            @click="onClickSendTest"
          />
          <oxd-button
            v-if="formMode === 'edit'"
            class="orangehrm-left-space"
            type="button"
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onClickCancel"
          />
          <submit-button
            class="orangehrm-left-space"
            :label="formMode === 'edit' ? 'Update' : '+ Add Registration'"
          />
        </oxd-form-actions>
      </oxd-form>
    </div>

    <!-- Registration table — its own card, visually separated from the form
         card above. The inter-card gap + box-shadow come from the SCSS. -->
    <div class="orangehrm-card-container orangehrm-slack-table-card">
      <oxd-text tag="p" class="orangehrm-subtitle">
        Notification registrations
      </oxd-text>

      <table-header
        :total="registrations.length"
        :selected="checkedItems.length"
        :loading="isLoading"
        :show-divider="false"
        @delete="onClickDeleteSelected"
      />

      <div class="orangehrm-container">
        <oxd-card-table
          v-model:selected="checkedItems"
          :loading="isLoading"
          :headers="tableHeaders"
          :items="tableItems"
          :selectable="true"
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
    </div>

    <!-- Delete confirmation -->
    <delete-confirmation ref="deleteDialog"></delete-confirmation>

    <!-- Duplicate-registration warning (FR-11) — fires when event + webhook (or stored hash)
         match another registration. Generic OHRM ConfirmationDialog pattern. -->
    <confirmation-dialog
      ref="duplicateDialog"
      title="Possible duplicate registration"
      subtitle="A registration with the same event type and workspace channel already exists. Saving this will cause the same message to be sent more than once per day. Continue anyway?"
      confirm-label="Save Anyway"
      cancel-label="Go Back &amp; Fix"
      confirm-button-type="label-warn"
      icon="warning"
    ></confirmation-dialog>

    <!-- Send-Test confirmation — guards the row-icon path only. The form-level
         "Send test" button is contextual to an admin actively editing a row,
         so no prompt is needed there. The row icon, by contrast, can be
         tapped by accident from the table; the prompt prevents that. -->
    <confirmation-dialog
      ref="sendTestDialog"
      title="Send a test message?"
      subtitle="This will deliver a sample notification to the channel for this registration. It works regardless of whether the row is active."
      confirm-label="Send Test"
      cancel-label="Cancel"
      confirm-button-type="label-success"
      icon="send-fill"
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

const GOOGLE_CHAT_WEBHOOK_URL_REGEX =
  /^https:\/\/chat\.googleapis\.com\/v1\/spaces\/[A-Za-z0-9_-]+\/messages\?\S+$/;

const TEAMS_WEBHOOK_URL_REGEX =
  /^https:\/\/(?:[a-z0-9-]+\.)+logic\.azure\.com(:\d+)?\/workflows\/[a-z0-9-]+\/triggers\/[a-zA-Z0-9_]+\/paths\/invoke\?\S+$/;

// Provider-aware URL validator. Same provider-id strings as the backend
// (SlackRegistration::PROVIDER_*). Adding a 4th platform = new regex + new
// branch — keep the validator tight, the platform list lives in `providerOptions`.
const validWebhookUrl = (providerId) =>
  function (value) {
    if (!value) return true;
    if (providerId === 'google_chat') {
      if (!GOOGLE_CHAT_WEBHOOK_URL_REGEX.test(value)) {
        return 'Should be a valid Google Chat webhook URL (https://chat.googleapis.com/v1/spaces/…?key=…&token=…)';
      }
      try {
        const u = new URL(value);
        if (!u.searchParams.get('key') || !u.searchParams.get('token')) {
          return 'Google Chat webhook URL must include both `key` and `token` query parameters.';
        }
      } catch (e) {
        return 'Invalid URL.';
      }
      return true;
    }
    if (providerId === 'teams') {
      if (!TEAMS_WEBHOOK_URL_REGEX.test(value)) {
        return 'Should be a valid Microsoft Teams Power Automate workflow URL (https://prod-XX.{region}.logic.azure.com/workflows/…/triggers/manual/paths/invoke?…&sig=…)';
      }
      try {
        const u = new URL(value);
        if (!u.searchParams.get('sig')) {
          return 'Microsoft Teams workflow URL must include the `sig` query parameter.';
        }
      } catch (e) {
        return 'Invalid URL.';
      }
      return true;
    }
    return (
      SLACK_WEBHOOK_URL_REGEX.test(value) ||
      'Should be a valid Slack Incoming Webhook URL (https://hooks.slack.com/services/…)'
    );
  };

/**
 * Client-side mirror of SlackRegistrationService::maskWebhookUrl(). Handles
 * Slack, Google Chat and Teams URL shapes — used by duplicate detection to
 * compare a just-typed full URL against the masked URL stored on existing
 * registrations. Output must match the corresponding provider's `maskUrl()`
 * byte-for-byte (see WebhookProviderInterface::maskUrl).
 */
const maskWebhookUrl = function (url) {
  if (!url) return null;
  const slack = url.match(
    /^(https:\/\/hooks\.slack\.com\/services\/[A-Z0-9]+\/[A-Z0-9]+)\/.+$/,
  );
  if (slack) return slack[1] + '/…';
  const gchat = url.match(
    /^(https:\/\/chat\.googleapis\.com\/v1\/spaces\/[A-Za-z0-9_-]+\/messages)\?.+$/,
  );
  if (gchat) return gchat[1] + '?…';
  const teams = url.match(
    /^(https:\/\/(?:[a-z0-9-]+\.)+logic\.azure\.com(?::\d+)?\/workflows\/[a-z0-9-]+\/triggers\/[a-zA-Z0-9_]+\/paths\/invoke)\?.+$/,
  );
  if (teams) return teams[1] + '?…';
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
  provider: null,
  webhookUrl: '',
  hasStoredWebhookUrl: false,
  // Provider ID at the moment an existing row was loaded for editing. If
  // the user swaps Platform mid-edit, this stays at the original so we can
  // detect the change and force a fresh webhook URL — the stored one is
  // tied to the old platform's URL shape and can't carry over.
  originalProvider: null,
  channelLabel: '',
  // Single-select per the June 3 decision: one row = one subunit (or none =
  // all employees). For multi-subunit coverage, admins register multiple rows.
  // The backend still persists subunits via the join table, so this field
  // serialises as `subunitIds: [...]` (0 or 1 element) on the wire.
  subunit: null,
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
      globalEnabled: false,

      // Provider catalog — IDs match backend SlackRegistration::PROVIDER_*.
      // Adding a 4th platform (Discord, …) means appending here AND
      // registering it on the backend WebhookProviderRegistry — both ends
      // converge on the same provider-id string.
      providerOptions: [
        {id: 'slack', label: 'Slack'},
        {id: 'google_chat', label: 'Google Chat'},
        {id: 'teams', label: 'Microsoft Teams'},
      ],

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
      // Row indices the admin has ticked. `v-model:selected` on oxd-card-table
      // hands back positions in `tableItems`, not row ids — we map back to ids
      // in onClickDeleteSelected. Matches the pattern in WorkShift.vue.
      checkedItems: [],

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
          title: 'Notification Type',
          style: {flex: '14%'},
        },
        {name: 'platform', title: 'Platform', style: {flex: '11%'}},
        {name: 'channelLabel', title: 'Channel', style: {flex: '12%'}},
        {name: 'subunit', title: 'Sub Unit', style: {flex: '13%'}},
        {name: 'timezone', title: 'Timezone', style: {flex: '14%'}},
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
        provider: [required],
        timezone: [required],
        sendTime: [required, validTimeFormat],
        webhookUrl: [
          (v) => (this.effectiveHasStoredUrl ? true : required(v)),
          shouldNotExceedCharLength(512),
          // Closure captures `this` so the regex switches when the Platform
          // dropdown changes — re-evaluated on every form validation pass.
          (v) => validWebhookUrl(this.form.provider?.id || 'slack')(v),
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
    selectedProviderId() {
      return this.form.provider?.id || 'slack';
    },
    platformChanged() {
      // True only on an edit where the admin has picked a different platform
      // from the one originally stored on the row. Drives the "you must
      // paste a new URL" requirement so we don't ship the old platform's
      // URL to the new platform's API.
      return (
        this.formMode === 'edit' &&
        this.form.originalProvider !== null &&
        this.form.provider?.id !== this.form.originalProvider
      );
    },
    effectiveHasStoredUrl() {
      // The stored URL belongs to the original platform. As soon as the
      // platform is swapped, treat the form as if no URL is stored so the
      // field becomes required and the placeholder shows the NEW platform's
      // example URL shape instead of the (now stale) masked one.
      return this.form.hasStoredWebhookUrl && !this.platformChanged;
    },
    webhookUrlLabel() {
      switch (this.selectedProviderId) {
        case 'google_chat':
          return 'Google Chat Webhook URL';
        case 'teams':
          return 'Microsoft Teams Workflow URL';
        default:
          return 'Slack Incoming Webhook URL';
      }
    },
    webhookUrlHint() {
      switch (this.selectedProviderId) {
        case 'google_chat':
          return 'Create an Incoming Webhook in your Google Chat workspace and paste the URL here. Must be HTTPS.';
        case 'teams':
          return 'Create a Power Automate "Post to channel" workflow with an HTTP trigger and paste the workflow URL here. Must be HTTPS.';
        default:
          return 'Create an Incoming Webhook in your Slack workspace and paste the URL here. Must be HTTPS.';
      }
    },
    webhookUrlPlaceholder() {
      switch (this.selectedProviderId) {
        case 'google_chat':
          return 'https://chat.googleapis.com/v1/spaces/…/messages?key=…&token=…';
        case 'teams':
          return 'https://prod-XX.{region}.logic.azure.com/workflows/…/triggers/manual/paths/invoke?…&sig=…';
        default:
          return 'https://hooks.slack.com/services/…/…/…';
      }
    },
    maskedWebhookPlaceholder() {
      switch (this.selectedProviderId) {
        case 'google_chat':
          return 'https://chat.googleapis.com/v1/spaces/…/messages?… (saved — leave blank to keep)';
        case 'teams':
          return 'https://…/workflows/…/triggers/manual/paths/invoke?… (saved — leave blank to keep)';
        default:
          return 'https://hooks.slack.com/services/…/…/… (saved — leave blank to keep)';
      }
    },
    tableItems() {
      return this.registrations.map((row, index) => {
        const subunitNames = (row.subunits || []).map((s) => s.name);
        return {
          id: row.id,
          index,
          eventType: this.labelFor(this.eventTypeOptions, row.eventType),
          platform: this.labelFor(this.providerOptions, row.provider),
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
    // Default the form to the first provider (Slack) so the URL hint /
    // placeholder render before the admin has interacted with the dropdown.
    this.form.provider = this.providerOptions[0] || null;
    this.isLoading = true;
    Promise.all([
      this.timezonesHttp.getAll().then(({data}) => {
        this.timezoneOptions = data.data.map((tz) => ({
          id: tz.name,
          label: `(GMT${tz.label}) ${tz.name}`,
        }));
        // Pre-select the admin's local timezone for the first add. Skip if
        // we're already pointing at a row (e.g. the page was loaded with an
        // edit in progress) so we don't clobber the stored value.
        if (this.formMode === 'add' && !this.form.timezone) {
          this.form.timezone = this.detectDefaultTimezone();
        }
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

    /**
     * Returns the timezone option matching the browser's IANA identifier
     * (e.g. `Asia/Colombo`), or null if the browser's name isn't in the
     * server-returned list. Used to pre-select the admin's local zone in
     * the dropdown so a new registration doesn't start blank.
     */
    detectDefaultTimezone() {
      try {
        const browserTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        return this.timezoneOptions.find((tz) => tz.id === browserTz) || null;
      } catch (e) {
        return null;
      }
    },

    /* ───────── add / edit form ───────── */
    resetForm() {
      this.form = emptyForm();
      // Restore the default provider so the URL field shows the right hint
      // / placeholder immediately after a successful save.
      this.form.provider = this.providerOptions[0] || null;
      // Re-apply the auto-detected timezone so the next "add" starts with a
      // sensible default instead of a blank required field.
      this.form.timezone = this.detectDefaultTimezone();
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
        provider:
          this.providerOptions.find((p) => p.id === row.provider) ||
          this.providerOptions[0] ||
          null,
        webhookUrl: '',
        hasStoredWebhookUrl: !!row.webhookUrl,
        // Remember the platform the stored URL was created for; if the
        // admin switches Platform mid-edit, we'll require a new URL.
        originalProvider: row.provider,
        channelLabel: row.channelLabel || '',
        // Backend may still carry multiple subunits on legacy rows; surface
        // the first one in the now-single-select dropdown. (Empty = all-emp.)
        subunit:
          row.subunits && row.subunits.length > 0
            ? {id: row.subunits[0].id, label: row.subunits[0].name}
            : null,
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
      const mySubunitId = this.form.subunit?.id ?? null;
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
          // If either side has no subunit filter (all-employees), or both target
          // the same subunit, they hit the same audience → duplicate.
          const otherIds = (r.subunits || []).map((s) => s.id);
          if (mySubunitId === null || otherIds.length === 0) return true;
          return otherIds.includes(mySubunitId);
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
        provider: this.selectedProviderId,
        webhookUrl: this.form.webhookUrl || null,
        channelLabel: this.form.channelLabel || null,
        subunitIds: this.form.subunit ? [this.form.subunit.id] : [],
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
    sendTest(idOrNull, webhookUrl, eventTypeOverride = null) {
      this.isLoading = true;
      // Row-icon tests pass the row's own eventType in `eventTypeOverride` so
      // the body matches the row, even if the form is empty / pointed at a
      // different registration. The backend also overrides from the row, but
      // we send the right value too so the validation layer doesn't surprise.
      const body = {
        eventType: eventTypeOverride || this.form.eventType?.id || 'BIRTHDAY',
      };
      if (webhookUrl) {
        body.webhookUrl = webhookUrl;
        // Tell the backend which provider's regex + transport to use for the
        // unsaved URL. Saved-row tests read the provider from the row.
        body.provider = this.selectedProviderId;
      }
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
            message: 'Test message sent.',
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
      // Use the ROW's eventType, not whatever the form happens to show — the
      // form may be empty (admin clicked the icon without selecting the row)
      // or pointed at a different registration. Gate the actual send behind
      // a confirmation prompt so an accidental icon tap doesn't post to a
      // live channel.
      const row = this.registrations.find((r) => r.id === item.id);
      this.$refs.sendTestDialog.showDialog().then((confirmation) => {
        if (confirmation !== 'ok') return;
        this.sendTest(item.id, null, row?.eventType);
      });
    },

    /* ───────── delete ───────── */
    onClickDelete(item) {
      this.confirmAndDelete([item.id]);
    },

    onClickDeleteSelected() {
      if (this.checkedItems.length === 0) return;
      // table-header passes us indices into `tableItems`; resolve to row ids
      // through the computed view so a future re-sort doesn't break the map.
      const ids = this.checkedItems
        .map((idx) => this.tableItems[idx]?.id)
        .filter((id) => id != null);
      if (ids.length === 0) return;
      this.confirmAndDelete(ids);
    },

    confirmAndDelete(ids) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation !== 'ok') return;
        this.isLoading = true;
        this.registrationsHttp
          .deleteAll({ids})
          .then(() => {
            this.$toast.deleteSuccess();
            // Wipe selection so the row indices don't dangle into a
            // post-reload list that may have fewer rows.
            this.checkedItems = [];
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
        props: {name: 'send-fill', title: 'Send Test'},
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

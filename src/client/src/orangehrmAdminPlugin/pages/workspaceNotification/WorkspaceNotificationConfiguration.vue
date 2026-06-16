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
    <div
      class="orangehrm-card-container orangehrm-workspace-notification-config-card"
    >
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('admin.workspace_notification_configuration') }}
        </oxd-text>
        <oxd-switch-input
          v-model="globalEnabled"
          label-position="left"
          :option-label="$t('general.enable')"
          @update:model-value="onToggleEnable"
        />
      </div>

      <oxd-divider class="orangehrm-workspace-notification-section-divider" />

      <oxd-text tag="p" class="orangehrm-subtitle">
        {{
          formMode === 'edit'
            ? $t('admin.edit_notification_registration')
            : $t('admin.notification_registration')
        }}
      </oxd-text>
      <oxd-text class="orangehrm-input-hint" tag="p">
        {{ $t('admin.workspace_notification_form_hint') }}
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
                :label="$t('admin.notification_type')"
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
                :label="$t('admin.platform')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="form.webhookUrl"
                :rules="rules.webhookUrl"
                :placeholder="webhookFieldPlaceholder"
                :label="webhookUrlLabel"
                :required="!effectiveHasStoredUrl"
              />
              <oxd-text
                v-if="platformChanged"
                class="orangehrm-input-hint orangehrm-workspace-platform-changed-hint"
                tag="p"
              >
                {{
                  $t('admin.workspace_notification_platform_changed_hint', {
                    platform: form.provider?.label,
                  })
                }}
              </oxd-text>
              <oxd-text v-else class="orangehrm-input-hint" tag="p">
                {{ webhookUrlHint }}
              </oxd-text>
            </oxd-grid-item>

            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="form.channelLabel"
                :rules="rules.channelLabel"
                :label="
                  $t('admin.workspace_notification_channel_name_optional')
                "
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('admin.workspace_notification_channel_hint') }}
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="form.subunit"
                type="select"
                :options="subunitOptions"
                :label="$t('admin.sub_unit')"
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('admin.workspace_notification_subunit_hint') }}
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="form.timezone"
                type="select"
                :options="timezoneOptions"
                :show-empty-selector="false"
                :rules="rules.timezone"
                :label="$t('general.timezone')"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('admin.workspace_notification_timezone_hint') }}
              </oxd-text>
            </oxd-grid-item>

            <oxd-grid-item class="--offset-row-3">
              <oxd-input-field
                v-model="form.sendTime"
                type="time"
                :step="1"
                :rules="rules.sendTime"
                :label="$t('admin.send_time')"
                placeholder="HH:mm"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('admin.workspace_notification_send_time_hint') }}
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
            :label="$t('admin.send_test')"
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
            :label="
              formMode === 'edit'
                ? $t('admin.update')
                : $t('admin.add_registration')
            "
          />
        </oxd-form-actions>
      </oxd-form>
    </div>

    <div
      class="orangehrm-card-container orangehrm-workspace-notification-table-card"
    >
      <oxd-text tag="p" class="orangehrm-subtitle">
        {{ $t('admin.notification_registrations') }}
      </oxd-text>

      <table-header
        :total="total"
        :selected="checkedItems.length"
        :loading="isLoading || isListLoading"
        :show-divider="false"
        @delete="onClickDeleteSelected"
      />

      <div class="orangehrm-container">
        <oxd-card-table
          v-model:selected="checkedItems"
          v-model:order="sortDefinition"
          :loading="isLoading || isListLoading"
          :headers="tableHeaders"
          :items="tableItems"
          :selectable="true"
          :clickable="false"
          row-decorator="oxd-table-decorator-card"
        />
      </div>

      <div class="orangehrm-bottom-container">
        <oxd-pagination
          v-if="showPaginator"
          v-model:current="currentPage"
          :length="pages"
        />
      </div>
    </div>

    <delete-confirmation ref="deleteDialog"></delete-confirmation>

    <confirmation-dialog
      ref="duplicateDialog"
      :title="$t('admin.workspace_notification_duplicate_dialog_title')"
      :subtitle="$t('admin.workspace_notification_duplicate_dialog_subtitle')"
      :confirm-label="$t('general.save')"
      :cancel-label="$t('admin.workspace_notification_go_back_and_fix')"
      icon="warning"
    ></confirmation-dialog>

    <confirmation-dialog
      ref="sendTestDialog"
      :title="$t('admin.workspace_notification_send_test_dialog_title')"
      :subtitle="$t('admin.workspace_notification_send_test_dialog_subtitle')"
      :confirm-label="$t('admin.send_test')"
      :cancel-label="$t('general.cancel')"
      confirm-button-type="label-success"
      icon="send-fill"
    ></confirmation-dialog>
  </div>
</template>

<script>
import {computed} from 'vue';
import {
  required,
  shouldNotExceedCharLength,
  validTimeFormat,
} from '@/core/util/validation/rules';
import useForm from '@/core/util/composable/useForm';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useSort from '@ohrm/core/util/composable/useSort';
import {APIService} from '@ohrm/core/util/services/api.service';
import {OxdSwitchInput, OxdSpinner, OxdPagination} from '@ohrm/oxd';
import TableHeader from '@ohrm/components/table/TableHeader';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import ConfirmationDialog from '@/core/components/dialogs/ConfirmationDialog';

const SLACK_WEBHOOK_URL_REGEX =
  /^https:\/\/hooks\.slack\.com\/services\/[A-Z0-9]+\/[A-Z0-9]+\/[A-Za-z0-9]+$/;

const GOOGLE_CHAT_WEBHOOK_URL_REGEX =
  /^https:\/\/chat\.googleapis\.com\/v1\/spaces\/[A-Za-z0-9_-]+\/messages\?\S+$/;

const TEAMS_WEBHOOK_URL_REGEX =
  /^https:\/\/(?:[a-z0-9-]+\.)+logic\.azure\.com(:\d+)?\/workflows\/[a-z0-9-]+\/triggers\/[a-zA-Z0-9_]+\/paths\/invoke\?\S+$/;

const validWebhookUrl = (providerId) =>
  function (value) {
    if (!value) return true;
    if (providerId === 'google_chat') {
      if (!GOOGLE_CHAT_WEBHOOK_URL_REGEX.test(value)) {
        return 'Should be a valid Google Chat webhook URL';
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
        return 'Should be a valid Microsoft Teams Power Automate workflow URL';
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
      'Should be a valid Slack Incoming Webhook URL'
    );
  };

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
  originalProvider: null,
  channelLabel: '',
  subunit: null,
  timezone: null,
  sendTime: '',
  active: true,
});

export default {
  components: {
    'oxd-switch-input': OxdSwitchInput,
    'oxd-pagination': OxdPagination,
    'table-header': TableHeader,
    'delete-confirmation': DeleteConfirmationDialog,
    'confirmation-dialog': ConfirmationDialog,
  },

  setup() {
    const configHttp = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/workspace-notification/config',
    );
    const registrationsHttp = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/workspace-notification/registrations',
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
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: {
        'r.channelLabel': 'DEFAULT',
        'r.dailySendTime': 'DEFAULT',
      },
    });

    const serializedFilters = computed(() => ({
      sortField: sortField.value,
      sortOrder: sortOrder.value,
    }));

    const {
      showPaginator,
      currentPage,
      total,
      pages,
      response,
      isLoading: isListLoading,
      execQuery,
    } = usePaginate(registrationsHttp, {query: serializedFilters});

    onSort(execQuery);

    return {
      configHttp,
      registrationsHttp,
      subunitsHttp,
      timezonesHttp,
      formRef,
      sortDefinition,
      sortField,
      sortOrder,
      showPaginator,
      currentPage,
      total,
      pages,
      execQuery,
      items: response,
      isListLoading,
    };
  },

  data() {
    return {
      isLoading: false,
      globalEnabled: false,

      checkedItems: [],

      providerOptions: [
        {id: 'slack', label: 'Slack'},
        {id: 'google_chat', label: 'Google Chat'},
        // Microsoft Teams is out of scope for this release. Backend support
        // (TeamsWebhookProvider, dialect, formatter, API allow-list) is left
        // intact so the option can be re-enabled by un-commenting this line.
        // {id: 'teams', label: 'Microsoft Teams'},
      ],

      formMode: 'add',
      form: emptyForm(),
      formKey: 0,

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
        {
          name: 'channelLabel',
          title: 'Channel',
          sortField: 'r.channelLabel',
          style: {flex: '12%'},
        },
        {name: 'subunit', title: 'Sub Unit', style: {flex: '13%'}},
        {name: 'timezone', title: 'Timezone', style: {flex: '14%'}},
        {
          name: 'sendTime',
          title: 'Send time',
          sortField: 'r.dailySendTime',
          style: {flex: '9%'},
        },
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
          (v) => validWebhookUrl(this.form.provider?.id || 'slack')(v),
        ],
        channelLabel: [shouldNotExceedCharLength(100)],
      },
    };
  },

  computed: {
    canSendTest() {
      return !!this.form.webhookUrl || !!this.form.id;
    },
    selectedProviderId() {
      return this.form.provider?.id || 'slack';
    },
    platformChanged() {
      return (
        this.formMode === 'edit' &&
        this.form.originalProvider !== null &&
        this.form.provider?.id !== this.form.originalProvider
      );
    },
    effectiveHasStoredUrl() {
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
    /**
     * Picks the right placeholder per state:
     *  - edit-mode with stored URL → masked stored-URL placeholder
     *  - platform selected (no stored URL yet) → provider's example URL
     *  - no platform picked yet → blank, so the field reads clean
     */
    webhookFieldPlaceholder() {
      if (this.effectiveHasStoredUrl) return this.maskedWebhookPlaceholder;
      if (!this.form.provider) return '';
      return this.webhookUrlPlaceholder;
    },
    tableItems() {
      return (this.items?.data || []).map((row, index) => {
        const subunitNames = (row.subunits || []).map((s) => s.name);
        return {
          id: row.id,
          index,
          eventType: this.labelFor(this.eventTypeOptions, row.eventType),
          platform: this.labelFor(this.providerOptions, row.provider),
          channelLabel: row.channelLabel || '',
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

  watch: {
    // Clear+restore the URL so oxd-input-field re-runs its rule against the
    // newly-selected provider's regex. formRef.validate() would also touch
    // every other required field, surfacing premature "Required" errors.
    selectedProviderId() {
      if (!this.form.webhookUrl) return;
      const url = this.form.webhookUrl;
      this.form.webhookUrl = '';
      this.$nextTick(() => {
        this.form.webhookUrl = url;
      });
    },
  },

  beforeMount() {
    this.isLoading = true;
    Promise.all([
      this.timezonesHttp.getAll().then(({data}) => {
        this.timezoneOptions = data.data.map((tz) => ({
          id: tz.name,
          label: this.formatTimezoneLabel(tz),
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
    ]).finally(() => {
      this.isLoading = false;
    });
  },

  methods: {
    labelFor(options, id) {
      const found = options.find((o) => o.id === id);
      return found ? found.label : id;
    },

    formatTimezoneLabel(tz) {
      const offset = `(GMT${tz.label})`;
      const city = (tz.name.split('/').pop() || tz.name).replace(/_/g, ' ');
      let longName = null;
      try {
        const parts = new Intl.DateTimeFormat('en', {
          timeZone: tz.name,
          timeZoneName: 'long',
        }).formatToParts(new Date(0));
        longName = parts.find((p) => p.type === 'timeZoneName')?.value || null;
      } catch (e) {
        longName = null;
      }
      return longName
        ? `${offset} ${longName} - ${city}`
        : `${offset} ${tz.name}`;
    },

    resetForm() {
      this.form = emptyForm();
      this.formMode = 'add';
      this.formKey += 1;
    },

    onClickEdit(item) {
      const row = (this.items?.data || []).find((r) => r.id === item.id);
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
        originalProvider: row.provider,
        channelLabel: row.channelLabel || '',
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
      // Remount oxd-form to drop any stale `touched` state from a prior
      // add-mode empty-submit (otherwise Webhook URL shows "Required" even
      // though edit mode keeps the stored URL).
      this.formKey += 1;
      window.scrollTo({top: 0, behavior: 'smooth'});
    },

    onClickCancel() {
      this.resetForm();
    },

    // Duplicate detection is scoped to the current page only (best-effort UX hint;
    // not a hard uniqueness constraint — there is no server-side unique key).
    findDuplicate() {
      if (!this.form.eventType?.id) return null;

      const myEventType = this.form.eventType.id;
      const mySubunitId = this.form.subunit?.id ?? null;
      let myMaskedUrl = null;
      if (this.form.webhookUrl) {
        myMaskedUrl = maskWebhookUrl(this.form.webhookUrl);
      } else if (this.formMode === 'edit' && this.form.id) {
        const myself = (this.items?.data || []).find(
          (r) => r.id === this.form.id,
        );
        myMaskedUrl = myself ? myself.webhookUrl : null;
      }
      if (!myMaskedUrl) return null;

      return (
        (this.items?.data || []).find((r) => {
          if (this.formMode === 'edit' && r.id === this.form.id) return false;
          if (r.eventType !== myEventType) return false;
          if (r.webhookUrl !== myMaskedUrl) return false;
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
          return this.execQuery();
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

    sendTest(idOrNull, webhookUrl, eventTypeOverride = null) {
      this.isLoading = true;
      const body = {
        eventType: eventTypeOverride || this.form.eventType?.id || 'BIRTHDAY',
      };
      if (webhookUrl) {
        body.webhookUrl = webhookUrl;
        body.provider = this.selectedProviderId;
      }
      return this.registrationsHttp
        .request({
          method: 'POST',
          url: idOrNull
            ? `/api/v2/admin/workspace-notification/registrations/${idOrNull}/test`
            : '/api/v2/admin/workspace-notification/registrations/test',
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
      // Prefer the just-typed URL (add-mode + new URL during edit). Falls
      // back to the saved-registration's stored URL via id when the field
      // hasn't been retyped.
      if (this.form.webhookUrl) {
        this.sendTest(null, this.form.webhookUrl);
      } else if (this.form.id) {
        this.sendTest(this.form.id, null);
      }
    },

    onClickRowSendTest(item) {
      const row = (this.items?.data || []).find((r) => r.id === item.id);
      this.$refs.sendTestDialog.showDialog().then((confirmation) => {
        if (confirmation !== 'ok') return;
        this.sendTest(item.id, null, row?.eventType);
      });
    },

    onClickDelete(item) {
      this.confirmAndDelete([item.id]);
    },

    onClickDeleteSelected() {
      if (this.checkedItems.length === 0) return;
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
            this.checkedItems = [];
            this.currentPage = 1;
            return this.execQuery();
          })
          .finally(() => {
            this.isLoading = false;
          });
      });
    },

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
      const existing = (this.items?.data || []).find((r) => r.id === row.id);
      if (!existing) return;
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

    onToggleEnable(value) {
      this.configHttp
        .request({
          method: 'PUT',
          data: {enable: value},
        })
        .then(() => this.$toast.updateSuccess())
        .catch(() => {
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

<style
  src="./workspace-notification-configuration.scss"
  lang="scss"
  scoped
></style>

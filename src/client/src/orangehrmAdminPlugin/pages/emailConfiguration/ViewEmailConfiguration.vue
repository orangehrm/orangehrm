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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text class="orangehrm-main-title">
        {{ $t('admin.email_configuration') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="emailConfiguration.sentAs"
                :label="$t('admin.mail_sent_as')"
                :rules="rules.sentAs"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-group :label="$t('admin.sending_method')">
                <div class="send-method-div">
                  <oxd-input-field
                    v-model="emailConfiguration.mailType"
                    type="radio"
                    :option-label="$t('admin.secure_smtp')"
                    value="smtps"
                  />
                  <oxd-input-field
                    v-model="emailConfiguration.mailType"
                    type="radio"
                    :option-label="$t('admin.smtp')"
                    value="smtp"
                  />
                  <oxd-input-field
                    v-model="emailConfiguration.mailType"
                    type="radio"
                    :option-label="$t('admin.sendmail')"
                    value="sendmail"
                  />
                </div>
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType === 'sendmail'">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-group :label="$t('admin.path_to_sendmail')">
                <oxd-text tag="p" class="sendmail-path-value">
                  {{ emailConfiguration.pathToSendmail }}
                </oxd-text>
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType !== 'sendmail'">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="emailConfiguration.smtpHost"
                :label="$t('admin.smtp_host')"
                :rules="rules.smtpHost"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="emailConfiguration.smtpPort"
                :label="$t('admin.smtp_port')"
                :rules="rules.smtpPort"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType !== 'sendmail'">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-group
                :label="$t('admin.use_smtp_authentication')"
                :classes="{wrapper: '--status-grouped-field'}"
              >
                <oxd-input-field
                  v-model="emailConfiguration.smtpAuthType"
                  type="radio"
                  :option-label="$t('general.yes')"
                  value="login"
                />
                <oxd-input-field
                  v-model="emailConfiguration.smtpAuthType"
                  type="radio"
                  :option-label="$t('general.no')"
                  value="none"
                />
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row
          v-if="
            emailConfiguration.mailType !== 'sendmail' &&
              emailConfiguration.smtpAuthType === 'login'
          "
        >
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="emailConfiguration.smtpUsername"
                :label="$t('admin.smtp_user')"
                :rules="rules.smtpUsername"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="emailConfiguration.smtpPassword"
                :label="$t('admin.smtp_password')"
                :rules="rules.smtpPassword"
                type="password"
                :placeholder="passwordPlaceHolder"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType !== 'sendmail'">
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="organization-name-container">
              <div class="orangehrm-optional-field-row">
                <oxd-text tag="p" class="orangehrm-optional-field-label">
                  {{ $t('admin.tls') }}
                </oxd-text>
                <oxd-switch-input v-model="useTLSSecureConnection" />
              </div>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType !== 'sendmail'">
          <oxd-grid :cols="1" class="orangehrm-full-width-grid">
            <oxd-grid-item class="organization-name-container">
              <oxd-text tag="p" class="tls-hint">
                {{
                  $t(
                    'admin.optional_the_mail_server_requires_the_use_of_tls_security',
                  )
                }}
              </oxd-text>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="organization-name-container">
              <div class="orangehrm-optional-field-row">
                <oxd-text tag="p" class="orangehrm-optional-field-label">
                  {{ $t('admin.send_test_email') }}
                </oxd-text>
                <oxd-switch-input v-model="sendTestMailEditable" />
              </div>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="sendTestMailEditable">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="emailConfiguration.testEmailAddress"
                :label="$t('admin.test_email_address')"
                :rules="rules.testEmailAddress"
                :disabled="!sendTestMailEditable"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.reset')"
            @click="onReset"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';
import {
  required,
  validEmailFormat,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';

export default {
  components: {
    'oxd-switch-input': SwitchInput,
  },
  props: {
    pathToSendmail: {
      type: String,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/email-configuration',
    );
    return {
      http,
    };
  },

  data() {
    return {
      passwordPlaceHolder: '',
      defaultValues: {
        smtpSecurityType: 'tls',
      },
      useTLSSecureConnection: true,
      sendTestMailEditable: false,
      isLoading: false,
      emailConfiguration: {
        mailType: '',
        sentAs: '',
        pathToSendmail: this.pathToSendmail,
        smtpHost: '',
        smtpPort: null,
        smtpUsername: '',
        smtpPassword: null,
        smtpAuthType: '',
        smtpSecurityType: '',
        testEmailAddress: '',
      },
      initialEmailConfiguration: {
        ...this.emailConfiguration,
        useTLSSecureConnection: false,
      },
      rules: {
        mailType: [required, shouldNotExceedCharLength(50)],
        sentAs: [required, shouldNotExceedCharLength(100), validEmailFormat],
        smtpHost: [required, shouldNotExceedCharLength(100)],
        smtpPort: [shouldNotExceedCharLength(10)],
        smtpUsername: [required, shouldNotExceedCharLength(100)],
        smtpPassword: [shouldNotExceedCharLength(100)],
        smtpAuthType: [shouldNotExceedCharLength(50)],
        smtpSecurityType: [shouldNotExceedCharLength(50)],
        testEmailAddress: [
          required,
          shouldNotExceedCharLength(250),
          validEmailFormat,
        ],
      },
    };
  },
  created() {
    this.isLoading = true;
    this.http
      .request({
        method: 'GET',
        url: 'api/v2/admin/email-configuration',
      })
      .then(response => {
        const {data} = response.data;
        this.emailConfiguration.mailType = data.mailType;
        this.emailConfiguration.sentAs = data.sentAs;
        this.emailConfiguration.smtpHost = data.smtpHost;
        this.emailConfiguration.smtpPort = data.smtpPort;
        this.emailConfiguration.smtpUsername = data.smtpUsername;
        this.passwordPlaceHolder = data.smtpUsername ? '******' : '';
        this.emailConfiguration.smtpAuthType = data.smtpAuthType;
        this.emailConfiguration.testEmailAddress = data.testEmailAddress;
        this.useTLSSecureConnection = data.smtpSecurityType === 'tls';
        this.initialEmailConfiguration = {
          ...this.emailConfiguration,
          useTLSSecureConnection: this.useTLSSecureConnection,
        };
        if (!data.smtpUsername) {
          this.rules.smtpPassword.push(required);
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            mailType: this.emailConfiguration.mailType,
            sentAs: this.emailConfiguration.sentAs,
            smtpHost: this.emailConfiguration.smtpHost,
            smtpPort: parseInt(this.emailConfiguration.smtpPort),
            smtpUsername:
              this.emailConfiguration.smtpAuthType === 'login'
                ? this.emailConfiguration.smtpUsername
                : '',
            smtpPassword:
              this.emailConfiguration.smtpPassword === ''
                ? null
                : this.emailConfiguration.smtpPassword,
            smtpAuthType: this.emailConfiguration.smtpAuthType,
            smtpSecurityType: this.useTLSSecureConnection ? 'tls' : 'none',
            testEmailAddress: this.emailConfiguration.testEmailAddress,
          },
        })
        .then(response => {
          const testEmailStatus = response.data.meta?.testEmailStatus;
          if (testEmailStatus === 1 && this.sendTestMailEditable) {
            this.$toast.success({
              title: this.$t('general.success'),
              message: this.$t('admin.test_email_sent'),
            });
          } else if (testEmailStatus === 0 && this.sendTestMailEditable) {
            this.$toast.warn({
              title: this.$t('general.failed'),
              message: this.$t('admin.test_email_not_sent'),
            });
          }
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.isLoading = false;
        });
    },
    onReset() {
      this.emailConfiguration = {...this.initialEmailConfiguration};
      this.useTLSSecureConnection = this.initialEmailConfiguration.useTLSSecureConnection;
    },
  },
};
</script>

<style src="./email-configuration.scss" lang="scss" scoped></style>

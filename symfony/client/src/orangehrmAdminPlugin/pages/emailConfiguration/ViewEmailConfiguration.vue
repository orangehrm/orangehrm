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
      <oxd-text class="orangehrm-main-title">Email Configuration</oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Mail Sent As"
                v-model="emailConfiguration.sentAs"
                :rules="rules.sentAs"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-group
                label="Sending Method"
                :classes="{wrapper: '--status-grouped-field'}"
              >
                <oxd-input-field
                  type="radio"
                  v-model="emailConfiguration.mailType"
                  optionLabel="Sendmail"
                  value="sendmail"
                />
                <oxd-input-field
                  type="radio"
                  v-model="emailConfiguration.mailType"
                  optionLabel="SMTP"
                  value="smtp"
                />
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType === 'sendmail'">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-group label="Path to Sendmail">
                <oxd-text tag="p" class="sendmail-path-value">
                  {{ emailConfiguration.pathToSendmail }}
                </oxd-text>
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType === 'smtp'">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="SMTP Host"
                v-model="emailConfiguration.smtpHost"
                :rules="rules.smtpHost"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="SMTP Port"
                v-model="emailConfiguration.smtpPort"
                :rules="rules.smtpPort"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType === 'smtp'">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-group
                label="Use SMTP Authentication"
                :classes="{wrapper: '--status-grouped-field'}"
              >
                <oxd-input-field
                  type="radio"
                  v-model="emailConfiguration.smtpAuthType"
                  optionLabel="Yes"
                  value="login"
                />
                <oxd-input-field
                  type="radio"
                  v-model="emailConfiguration.smtpAuthType"
                  optionLabel="No"
                  value="none"
                />
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType === 'smtp' && emailConfiguration.smtpAuthType === 'login'">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="SMTP User"
                v-model="emailConfiguration.smtpUsername"
                :rules="rules.smtpUsername"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="SMTP Password"
                v-model="emailConfiguration.smtpPassword"
                :rules="rules.smtpPassword"
                type="password"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType === 'smtp'">
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <div class="orangehrm-optional-field-row">
              <oxd-text tag="p" class="orangehrm-optional-field-label">
                User Secure Connection
              </oxd-text>
              <oxd-switch-input v-model="userSecureConnection" />
            </div>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="emailConfiguration.mailType === 'smtp' && userSecureConnection">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-group
                :classes="{wrapper: '--status-grouped-field'}"
              >
                <oxd-input-field
                  type="radio"
                  v-model="emailConfiguration.smtpSecurityType"
                  optionLabel="SSL"
                  value="ssl"
                />
                <oxd-input-field
                  type="radio"
                  v-model="emailConfiguration.smtpSecurityType"
                  optionLabel="TLS"
                  value="tls"
                />
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <div class="orangehrm-optional-field-row">
              <oxd-text tag="p" class="orangehrm-optional-field-label">
                Send Test Mail
              </oxd-text>
              <oxd-switch-input v-model="sendTestMailEditable" />
            </div>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="sendTestMailEditable">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Test Email Address"
                v-model="emailConfiguration.testEmailAddress"
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
          <submit-button/>
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@orangehrm/core/util/services/api.service';
import SwitchInput from '@orangehrm/oxd/src/core/components/Input/SwitchInput';
import {
  required,
  validEmailFormat,
  shouldNotExceedCharLength,
} from '@orangehrm/core/util/validation/rules';

export default {
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

  components: {
    'oxd-switch-input': SwitchInput,
  },

  data() {
    return {
      userSecureConnection: false,
      sendTestMailEditable: false,
      isLoading: false,
      emailConfiguration: {
        mailType: '',
        sentAs: '',
        pathToSendmail: this.pathToSendmail,
        smtpHost: '',
        smtpPort: null,
        smtpUsername: '',
        smtpPassword: '',
        smtpAuthType: '',
        smtpSecurityType: '',
        testEmailAddress: '',
      },
      rules: {
        mailType: [required, shouldNotExceedCharLength(50)],
        sentAs: [required, shouldNotExceedCharLength(250), validEmailFormat],
        smtpHost: [required, shouldNotExceedCharLength(250)],
        smtpPort: [shouldNotExceedCharLength(10)],
        smtpUsername: [required, shouldNotExceedCharLength(250)],
        smtpPassword: [required, shouldNotExceedCharLength(250)],
        smtpAuthType: [shouldNotExceedCharLength(50)],
        smtpSecurityType: [shouldNotExceedCharLength(50)],
        testEmailAddress: [required, shouldNotExceedCharLength(250), validEmailFormat],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http.request({
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
            smtpPassword: this.emailConfiguration.smtpPassword,
            smtpAuthType: this.emailConfiguration.smtpAuthType,
            smtpSecurityType: this.userSecureConnection
              ? this.emailConfiguration.smtpSecurityType
              : 'none',
            testEmailAddress: this.emailConfiguration.testEmailAddress,
          },
        })
        .then(response => {
          const testEmailStatus = response.data.meta?.testEmailStatus;
          if (testEmailStatus === 1 && this.sendTestMailEditable) {
            this.$toast.success({
              title: 'Success',
              message: 'Test Email Sent',
            });
          } else if (testEmailStatus === 0 && this.sendTestMailEditable) {
            this.$toast.warn({
              title: 'Success',
              message: 'Test Email Not Sent',
            });
          }
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.isLoading = false;
        });
    }
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
        this.emailConfiguration.smtpPassword = data.smtpPassword;
        this.emailConfiguration.smtpAuthType = data.smtpAuthType;
        this.emailConfiguration.smtpSecurityType = data.smtpSecurityType;
        this.emailConfiguration.testEmailAddress = data.testEmailAddress;
        this.userSecureConnection = data.smtpSecurityType !== 'none';
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style src="./email-configuration.scss" lang="scss" scoped></style>

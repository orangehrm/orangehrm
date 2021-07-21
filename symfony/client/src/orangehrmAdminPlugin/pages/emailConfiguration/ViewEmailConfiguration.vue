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
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          Email Configuration
        </oxd-text>
<!--        <oxd-switch-input-->
<!--          v-model="editable"-->
<!--          optionLabel="Edit"-->
<!--          labelPosition="left"-->
<!--        />-->
      </div>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Mail Sent As"
                v-model="emailConfiguration.sentAs"
                :rules="rules.sentAs"
                :disabled="!editable"
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
        <div v-if="emailConfiguration.mailType === 'sendmail'">
          <oxd-form-row>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-group label="Path to Sendmail">
                  <oxd-text tag="p" class="no-of-employees-value">
                    {{ emailConfiguration.pathToSendmail }}
                  </oxd-text>
                </oxd-input-group>
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
        </div>
        <div v-if="emailConfiguration.mailType === 'smtp'">
          <oxd-form-row>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-field
                  label="SMTP Host"
                  v-model="emailConfiguration.smtpHost"
                  :rules="rules.smtpHost"
                  :disabled="!editable"
                  required
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <oxd-input-field
                  label="SMTP Port"
                  v-model="emailConfiguration.smtpPort"
                  :rules="rules.smtpPort"
                  :disabled="!editable"
                  required
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
          <oxd-form-row>
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
          <div v-if="emailConfiguration.smtpAuthType === 'login'">
            <oxd-form-row>
              <oxd-grid :cols="2" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    label="SMTP User"
                    v-model="emailConfiguration.smtpUsername"
                    :rules="rules.smtpUsername"
                    :disabled="!editable"
                    required
                  />
                </oxd-grid-item>
                <oxd-grid-item>
                  <oxd-input-field
                    label="SMTP Password"
                    v-model="emailConfiguration.smtpPassword"
                    :rules="rules.smtpPassword"
                    :disabled="!editable"
                    type="password"
                    required
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
          </div>
          <oxd-form-row>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-switch-input
                  v-model="userSecureConnection"
                  optionLabel="User Secure Connection"
                  labelPosition="left"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
          <div v-if="userSecureConnection">
            <oxd-form-row>
              <oxd-grid :cols="2" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-group
                    :classes="{wrapper: '--status-grouped-field'}"
                  >
                    <oxd-input-field style="width: 10px"
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
          </div>
        </div>

        <oxd-divider />

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-switch-input
                v-model="sendTestMailEditable"
                optionLabel="Send Test Mail"
                labelPosition="left"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row v-if="sendTestMailEditable">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Test Email Address"
                v-model="emailConfiguration.testEmailAddress"
                :rules="rules.testEmailAddress"
                :disabled="!editable || !sendTestMailEditable"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <submit-button v-if="editable" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@orangehrm/core/util/services/api.service';
import SwitchInput from '@orangehrm/oxd/src/core/components/Input/SwitchInput';
import { required, validEmailFormat } from '@orangehrm/core/util/validation/rules'
import { shouldNotExceedCharLength } from '@/core/util/validation/rules'

export default {
  props: {
    pathToSendmail: {
      type: String,
      required: true,
    }
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/organization',
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
      sendTestMailEditable: false, //check
      editable: true,
      isLoading: false,
      emailConfiguration: {
        mailType: '',
        sentAs: '',
        pathToSendmail: this.pathToSendmail,
        smtpHost: '',
        smtpPort: '',
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
        smtpPort: [required, shouldNotExceedCharLength(10)],
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
      this.http.http
        .put('api/v2/admin/email-configuration', {
          mailType: this.emailConfiguration.mailType,
          sentAs: this.emailConfiguration.sentAs,
          smtpHost: this.emailConfiguration.smtpHost,
          smtpPort: this.emailConfiguration.smtpPort,
          smtpUsername:
            this.emailConfiguration.smtpAuthType === 'login'
              ? this.emailConfiguration.smtpUsername
              : '', //check
          smtpPassword: this.emailConfiguration.smtpPassword,
          smtpAuthType: this.emailConfiguration.smtpAuthType,
          smtpSecurityType: this.userSecureConnection
            ? this.emailConfiguration.smtpSecurityType
            : 'none',
          testEmailAddress: this.emailConfiguration.testEmailAddress,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.isLoading = false;
        });
    },
  },
  created() {
    this.isLoading = true;
    this.http.http
      .get('api/v2/admin/email-configuration')
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
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};

</script>

<style>
::v-deep(.--status-grouped-field) {
  display: flex;
}
</style>

<style src="../organizationGeneralInformation/general-info.scss" lang="scss" scoped></style>
<style src="../../../orangehrmPimPlugin/pages/employee/employee.scss" lang="scss" scoped></style>

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
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('leave.add_leave_type') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-input-field
            v-model="leaveType.name"
            :label="$t('general.name')"
            :rules="rules.name"
            required
          />
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-group :classes="{wrapper: '--status-grouped-field'}">
                <template #label>
                  <div class="label-is-entitlement-situational">
                    <oxd-label
                      :label="$t('leave.is_entitlement_situational')"
                    />
                    <oxd-icon-button
                      class="--help"
                      name="exclamation-circle"
                      :with-container="false"
                      @click="onModalOpen"
                    />
                  </div>
                </template>
                <oxd-input-field
                  v-model="leaveType.situational"
                  type="radio"
                  :option-label="$t('general.yes')"
                  :value="true"
                />
                <oxd-input-field
                  v-model="leaveType.situational"
                  type="radio"
                  :option-label="$t('general.no')"
                  :value="false"
                />
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
      <entitlement-situational-modal
        v-if="showModal"
        @close="onModalClose"
      ></entitlement-situational-modal>
    </div>
  </div>
</template>

<script>
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import Label from '@ohrm/oxd/core/components/Label/Label';
import EntitlementSituationalModal from '@/orangehrmLeavePlugin/components/EntitlementSituationalModal';

const leaveTypeModel = {
  id: '',
  name: '',
  situational: false,
};

export default {
  components: {
    'oxd-label': Label,
    'entitlement-situational-modal': EntitlementSituationalModal,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/leave/leave-types',
    );
    return {
      http,
    };
  },

  data() {
    return {
      showModal: false,
      isLoading: false,
      leaveType: {...leaveTypeModel},
      rules: {
        name: [required, shouldNotExceedCharLength(50)],
      },
      errors: [],
    };
  },
  created() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.rules.name.push(v => {
          const index = data.findIndex(item => item.name == v);
          return index === -1 || this.$t('general.already_exists');
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          name: this.leaveType.name,
          situational: this.leaveType.situational,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.leaveType = {...leaveTypeModel};
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/leave/leaveTypeList');
    },
    onModalOpen() {
      this.showModal = true;
    },
    onModalClose() {
      this.showModal = false;
    },
  },
};
</script>

<style src="./leave-type.scss" lang="scss" scoped></style>

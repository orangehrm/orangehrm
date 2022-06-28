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
      <oxd-text class="orangehrm-main-title" tag="h6">
        {{ $t('recruitment.view_action_history') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.candidate')"
                disabled
                :value="action.candidate"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.vacancy')"
                disabled
                :value="action.vacancy"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.hiring_manager')"
                disabled
                :value="action.hiringManager"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.current_status')"
                disabled
                :value="action.status"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.performed_action')"
                disabled
                :value="action.performedAction"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('general.performed_by')"
                disabled
                :value="action.performedBy"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.performed_date')"
                disabled
                :value="getPerformedDate"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <slot></slot>
        <oxd-form-row>
          <oxd-grid :rows="2" :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item
              class="orangehrm-save-candidate-page --span-column-2"
            >
              <oxd-input-field
                v-model="action.notes"
                :label="$t('general.notes')"
                type="textarea"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <oxd-button
            display-type="ghost"
            :label="$t('general.back')"
            @click="onBack"
          />
          <submit-button :label="$t('general.save')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';
import useDateFormat from '@/core/util/composable/useDateFormat';

const actionModel = {
  candidate: null,
  vacancy: null,
  hiringManager: null,
  status: null,
  performedAction: null,
  performedDate: null,
  performedBy: null,
  notes: null,
};

export default {
  name: 'ViewActionHistory',

  props: {
    candidateId: {
      type: Number,
      required: true,
    },
    historyId: {
      type: Number,
      required: true,
    },
  },

  setup(props) {
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();

    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/recruitment/candidates/${props.candidateId}/history`,
    );
    return {
      http,
      locale,
      jsDateFormat,
    };
  },

  data() {
    return {
      isLoading: false,
      action: {...actionModel},
    };
  },
  computed: {
    getPerformedDate() {
      return formatDate(
        parseDate(this.action.performedDate),
        this.jsDateFormat,
        {locale: this.locale},
      );
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.historyId)
      .then(response => {
        const {data} = response.data;
        (this.action.candidate = `${data.candidate.firstName} ${data.candidate.middleName} ${data.candidate.lastName}`),
          (this.action.vacancy = data.vacancy.name),
          (this.action.hiringManager = `${data.vacancy.hiringManager.firstName}
          ${data.vacancy.hiringManager.middleName} ${data.vacancy.hiringManager.lastName}`),
          (this.action.status = data.action.label);
        this.action.performedDate = data.performedDate;
        this.action.performedAction = data.action.label;
        (this.action.performedBy = `${data.performedBy.firstName}
          ${data.performedBy.middleName} ${data.performedBy.lastName}`),
          (this.action.notes = data.note);
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSave() {
      this.loading = true;
      this.http
        .update(this.historyId, {
          note: this.action.notes,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onBack();
        });
    },
    onBack() {
      navigate('/recruitment/viewCandidates');
    },
  },
};
</script>

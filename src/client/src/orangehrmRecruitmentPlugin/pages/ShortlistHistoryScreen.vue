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
  <div class="orangehrm-background-container orangehrm-save-candidate-page">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
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
                :value="history.candidate"
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
                :value="history.vacancy"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.hiring_manager')"
                disabled
                :value="history.manager"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.current_status')"
                disabled
                :value="history.status"
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
                :value="history.performedAction"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.performed_by')"
                disabled
                :value="history.performedBy"
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
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item
              class="orangehrm-save-candidate-page --span-column-2"
            >
              <oxd-input-field
                :label="$t('general.notes')"
                type="textarea"
                :v-model="history.note"
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
import useLocale from '@/core/util/composable/useLocale';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';

export default {
  name: 'ShortlistHistoryScreen',
  setup() {
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();

    const http = new APIService(
      'https://c81c3149-4936-41d9-ab3d-e25f1bff2934.mock.pstmn.io',
      'recruitment/candidateHistory',
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
      history: {
        candidate: '',
        vacancy: '',
        manager: '',
        performedAction: '',
        performedBy: '',
        performedDate: '',
        note: '',
        status: null,
      },
    };
  },
  computed: {
    getPerformedDate() {
      return formatDate(
        parseDate(this.history.performedDate),
        this.jsDateFormat,
        {locale: this.locale},
      );
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.http.getAll().then(({data: {data}}) => {
      const {
        candidate,
        performedAction,
        vacancy,
        status,
        manager,
        ...rest
      } = data;
      const {firstName, lastName, middleName} = candidate;
      const fullName = `${manager.firstName} ${manager.middleName} ${manager.lastName}`;
      this.history = {
        candidate: `${firstName} ${middleName} ${lastName}`,
        vacancy: vacancy.title,
        manager:
          (manager?.terminationId ? this.$t('general.past_employee') : '') +
          fullName,
        performedAction: performedAction.label,
        cid: candidate.id,
        status: status.label,
        ...rest,
      };
      this.isLoading = false;
    });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.history.id, {note: this.history.note})
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          navigate('/recruitment/viewCandidates');
          this.isLoading = false;
        });
    },
    onBack() {
      navigate(`/recruitment/addCandidate/${this.history.cid}`);
    },
  },
};
</script>

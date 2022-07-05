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
    <purge-candidate-records @search="onClickSearch" />
    <br />
    <selected-candidates
      v-show="showPurgeableCandidates"
      :vacancy-id="vacancyId"
      :loading="isLoading"
      @purge="onClickPurge"
    />
    <br v-if="showPurgeableCandidates" />
    <maintenance-note :instance-identifier="instanceIdentifier" />

    <purge-confirmation
      ref="purgeDialog"
      :title="$t('maintenance.purge_candidates')"
      :subtitle="$t('maintenance.purge_candidates_warning')"
      :cancel-label="$t('general.no_cancel')"
      :confirmation-label="$t('maintenance.yes_purge')"
      icon=""
    ></purge-confirmation>
  </div>
</template>

<script>
import {reloadPage} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import CandidateRecords from '@/orangehrmMaintenancePlugin/components/CandidateRecords';
import MaintenanceNote from '@/orangehrmMaintenancePlugin/components/MaintenanceNote';
import SelectedCandidates from '@/orangehrmMaintenancePlugin/components/SelectedCandidates';
import ConfirmationDialog from '@/core/components/dialogs/ConfirmationDialog';

export default {
  name: 'PurgeCandidate',
  components: {
    'purge-confirmation': ConfirmationDialog,
    'purge-candidate-records': CandidateRecords,
    'selected-candidates': SelectedCandidates,
    'maintenance-note': MaintenanceNote,
  },
  props: {
    instanceIdentifier: {
      type: String,
      default: '',
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/maintenance/candidates/purge',
    );

    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      vacancyId: null,
      showPurgeableCandidates: false,
    };
  },
  methods: {
    onClickSearch(vacancy) {
      this.showPurgeableCandidates = true;
      this.vacancyId = vacancy;
    },
    onClickPurge() {
      this.$refs.purgeDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.purgeCandidates();
        }
      });
    },
    purgeCandidates() {
      this.isLoading = true;
      this.http
        .deleteAll({
          vacancyId: this.vacancyId,
        })
        .then(() => {
          return this.$toast.success({
            title: this.$t('general.success'),
            message: this.$t('maintenance.purge_success'),
          });
        })
        .then(() => {
          reloadPage();
        });
    },
  },
};
</script>

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
    <selected-candidates :loading="isLoading" @purge="onClickPurge" />
    <br />
    <maintenance-note :instance-identifier="instanceIdentifier" />

    <purge-confirmation
      ref="purgeDialog"
      :title="$t('maintenance.purge_candidates')"
      :subtitle="$t('maintenance.purge_candidates_warning')"
      :cancel-label="$t('general.no_cancel')"
      :confirm-label="$t('maintenance.yes_purge')"
    ></purge-confirmation>
  </div>
</template>

<script>
import {reloadPage} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import ConfirmationDialog from '@/core/components/dialogs/ConfirmationDialog';
import MaintenanceNote from '@/orangehrmMaintenancePlugin/components/MaintenanceNote';
import SelectedCandidates from '@/orangehrmMaintenancePlugin/components/SelectedCandidates';

export default {
  name: 'PurgeCandidate',
  components: {
    'maintenance-note': MaintenanceNote,
    'purge-confirmation': ConfirmationDialog,
    'selected-candidates': SelectedCandidates,
  },
  props: {
    instanceIdentifier: {
      type: String,
      default: null,
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
    };
  },
  methods: {
    onClickPurge(vacancy) {
      const vacancyId = vacancy;
      this.$refs.purgeDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.purgeCandidates(vacancyId);
        }
      });
    },
    purgeCandidates(vacancyId) {
      this.isLoading = true;
      this.http
        .deleteAll({
          vacancyId,
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

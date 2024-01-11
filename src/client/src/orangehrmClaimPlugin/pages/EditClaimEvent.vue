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
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('claim.edit_event') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-form-row>
              <oxd-input-field
                v-model="claimEvent.name"
                :label="$t('claim.event_name')"
                :disabled="!canEdit"
                :rules="rules.name"
                required
              />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <oxd-form-row>
              <oxd-input-field
                v-model="claimEvent.description"
                type="textarea"
                :label="$t('general.description')"
                :rules="rules.description"
              />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-3">
            <div class="orangehrm-sm-field">
              <oxd-text tag="p" class="orangehrm-sm-field-label">
                {{ $t('general.active') }}
              </oxd-text>
              <oxd-switch-input v-model="claimEvent.status" />
            </div>
          </oxd-grid-item>
        </oxd-grid>
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
    </div>
  </div>
</template>

<script>
import {OxdSwitchInput} from '@ohrm/oxd';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import useServerValidation from '@/core/util/composable/useServerValidation';

const initialClaimEvent = {
  name: '',
  description: '',
  status: null,
};

export default {
  components: {
    'oxd-switch-input': OxdSwitchInput,
  },
  props: {
    id: {
      type: Number,
      required: true,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/claim/events',
    );
    const {createUniqueValidator} = useServerValidation(http);
    const claimEventNameUniqueValidation = createUniqueValidator(
      'ClaimEvent',
      'name',
      {entityId: props.id, matchByField: 'isDeleted', matchByValue: 'false'},
    );
    return {
      http,
      claimEventNameUniqueValidation,
    };
  },

  data() {
    return {
      isLoading: false,
      claimEvent: {...initialClaimEvent},
      canEdit: false,
      name: '',
      rules: {
        name: [
          required,
          this.claimEventNameUniqueValidation,
          shouldNotExceedCharLength(100),
        ],
        description: [shouldNotExceedCharLength(1000)],
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.id)
      .then((response) => {
        const {data} = response.data;
        this.claimEvent = {...data};
        this.name = data.name;
        this.canEdit = response.data.meta.canEdit;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onCancel() {
      navigate('/claim/viewEvents');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.id, {
          name: this.canEdit ? this.claimEvent.name : null,
          description: this.claimEvent.description,
          status: this.claimEvent.status,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
  },
};
</script>

<style src="./save-claim-event.scss" lang="scss" scoped></style>

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
        {{ $t('admin.edit_location') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" novalidate="true" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.name"
                :disabled="!hasUpdatePermissions"
                :rules="rules.name"
                :label="$t('general.name')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.city"
                :disabled="!hasUpdatePermissions"
                :rules="rules.city"
                :label="$t('general.city')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.province"
                :disabled="!hasUpdatePermissions"
                :rules="rules.province"
                :label="$t('general.state_province')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.zipCode"
                :disabled="!hasUpdatePermissions"
                :rules="rules.zipCode"
                :label="$t('general.zip_postal_code')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.countryCode"
                :clear="false"
                :disabled="!hasUpdatePermissions"
                :options="countries"
                :rules="rules.countryCode"
                :label="$t('general.country')"
                required
                type="select"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model.trim="location.phone"
                :disabled="!hasUpdatePermissions"
                :rules="rules.phone"
                :label="$t('general.phone')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.fax"
                :disabled="!hasUpdatePermissions"
                :rules="rules.fax"
                :label="$t('general.fax')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.address"
                :disabled="!hasUpdatePermissions"
                :rules="rules.address"
                :label="$t('admin.address')"
                type="textarea"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.note"
                :disabled="!hasUpdatePermissions"
                :rules="rules.note"
                :label="$t('general.note')"
                type="textarea"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button v-if="hasUpdatePermissions" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
  validPhoneNumberFormat,
} from '@ohrm/core/util/validation/rules';

const initialLocation = {
  name: '',
  countryCode: null,
  province: '',
  city: '',
  address: '',
  zipCode: '',
  phone: '',
  fax: '',
  note: '',
};

export default {
  props: {
    locationId: {
      type: String,
      required: true,
    },
    countries: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/locations',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      location: {...initialLocation},
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
        countryCode: [required],
        province: [shouldNotExceedCharLength(50)],
        city: [shouldNotExceedCharLength(50)],
        address: [shouldNotExceedCharLength(250)],
        zipCode: [shouldNotExceedCharLength(30)],
        phone: [shouldNotExceedCharLength(30), validPhoneNumberFormat],
        fax: [shouldNotExceedCharLength(30), validPhoneNumberFormat],
        note: [shouldNotExceedCharLength(250)],
      },
    };
  },

  computed: {
    hasUpdatePermissions() {
      return this.$can.update(`locations`);
    },
  },

  created() {
    this.isLoading = true;
    this.http
      .get(parseInt(this.locationId, 10))
      .then(response => {
        const {data} = response.data;
        this.location.name = data.name;
        this.location.countryCode = this.countries.find(
          item => item.id === data.country.countryCode,
        );
        this.location.province = data.province;
        this.location.city = data.city;
        this.location.address = data.address;
        this.location.zipCode = data.zipCode;
        this.location.phone = data.phone;
        this.location.fax = data.fax;
        this.location.note = data.note;

        // Fetch list data for unique test
        return this.http.getAll({limit: 0});
      })
      .then(response => {
        const {data} = response.data;
        this.rules.name.push(v => {
          const index = data.findIndex(item => item.name === v);
          if (index > -1) {
            const {id} = data[index];
            return parseInt(id, 10) !== parseInt(this.locationId, 10)
              ? this.$t('general.already_exists')
              : true;
          } else {
            return true;
          }
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onCancel() {
      navigate('/admin/viewLocations');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.locationId, {
          name: this.location.name,
          countryCode: this.location.countryCode.id,
          province: this.location.province,
          city: this.location.city,
          address: this.location.address,
          zipCode: this.location.zipCode,
          phone: this.location.phone,
          fax: this.location.fax,
          note: this.location.note,
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

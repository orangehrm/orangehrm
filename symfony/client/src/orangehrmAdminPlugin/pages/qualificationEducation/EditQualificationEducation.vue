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
      <oxd-text tag="h6" class="orangehrm-main-title">{{
        $t('general.edit_education')
      }}</oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-input-field
            v-model="qualification.name"
            :label="$t('general.level')"
            :rules="rules.name"
            required
          />
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            type="button"
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
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';

export default {
  props: {
    educationId: {
      type: Number,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/educations',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      qualification: {
        id: '',
        name: '',
      },
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
      },
    };
  },

  created() {
    this.isLoading = true;
    this.http
      .get(this.educationId)
      .then(response => {
        const {data} = response.data;
        this.qualification.id = data.id;
        this.qualification.name = data.name;
        // Fetch list data for unique test
        return this.http.getAll();
      })
      .then(response => {
        const {data} = response.data;
        this.rules.name.push(v => {
          const index = data.findIndex(item => item.name === v);
          if (index > -1) {
            const {id} = data[index];
            return id !== this.qualification.id
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
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.educationId, {
          name: this.qualification.name,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/admin/viewEducation');
    },
  },
};
</script>

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
        {{ $t('admin.add_pay_grade') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="grade.name"
                :label="$t('general.name')"
                :rules="rules.name"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
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
import {APIService} from '@/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import useServerValidation from '@/core/util/composable/useServerValidation';

export default {
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/pay-grades',
    );
    const {createUniqueValidator} = useServerValidation(http);
    const payGradeUniqueValidation = createUniqueValidator('PayGrade', 'name');

    return {
      http,
      payGradeUniqueValidation,
    };
  },
  data() {
    return {
      isLoading: false,
      grade: {
        id: '',
        name: '',
      },
      rules: {
        name: [
          required,
          this.payGradeUniqueValidation,
          shouldNotExceedCharLength(50),
        ],
      },
      errors: [],
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          name: this.grade.name,
        })
        .then((response) => {
          const {data} = response.data;
          this.$toast.saveSuccess();
          navigate('/admin/payGrade/{id}', {id: data.id});
        });
    },
    onCancel() {
      navigate('/admin/viewPayGrades');
    },
  },
};
</script>

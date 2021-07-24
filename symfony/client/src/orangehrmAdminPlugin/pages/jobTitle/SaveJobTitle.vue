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
        {{ $t('admin.add_job_title') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-grid :cols="1">
          <div>
            <oxd-form-row>
              <oxd-input-field
                :label="$t('general.job_title')"
                v-model="jobTitle.title"
                :rules="rules.title"
                required
              />
            </oxd-form-row>

            <oxd-form-row>
              <oxd-input-field
                type="textarea"
                :label="$t('general.job_description')"
                placeholder="Type description here"
                v-model="jobTitle.description"
                :rules="rules.description"
              />
            </oxd-form-row>

            <oxd-form-row>
              <oxd-input-field
                type="file"
                :label="$t('general.job_specification')"
                buttonLabel="Browse"
                v-model="jobTitle.specification"
                :rules="rules.specification"
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('general.file_upload_notice') }}
              </oxd-text>
            </oxd-form-row>

            <oxd-form-row>
              <oxd-input-field
                type="textarea"
                :label="$t('general.note')"
                placeholder="Add note"
                v-model="jobTitle.note"
                labelIcon="pencil-square"
                :rules="rules.note"
              />
            </oxd-form-row>
          </div>
        </oxd-grid>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            displayType="ghost"
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
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
  validFileTypes,
  maxFileSize,
} from '@orangehrm/core/util/validation/rules';

const initialJobTitle = {
  title: '',
  description: '',
  specification: null,
  note: '',
};

export default {
  props: {
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/job-titles',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      jobTitle: {...initialJobTitle},
      rules: {
        title: [required, shouldNotExceedCharLength(100)],
        description: [shouldNotExceedCharLength(400)],
        specification: [
          validFileTypes(this.allowedFileTypes),
          maxFileSize(this.maxFileSize),
        ],
        note: [shouldNotExceedCharLength(400)],
      },
    };
  },

  methods: {
    onCancel() {
      navigate('/admin/viewJobTitleList');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.jobTitle,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
  },

  created() {
    this.isLoading = true;
    this.http
      .getAll({limit: 0})
      .then(response => {
        const {data} = response.data;
        this.rules.title.push(v => {
          const index = data.findIndex(item => item.title == v);
          return index === -1 || 'Already exists';
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

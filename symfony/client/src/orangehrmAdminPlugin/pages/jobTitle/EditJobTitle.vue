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
        {{ $t('admin.edit_job_title') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form novalidate="true" :loading="isLoading" @submitValid="onSave">
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
              <file-upload-input
                :label="$t('general.job_specification')"
                buttonLabel="Browse"
                v-model:newFile="jobTitle.newSpecification"
                v-model:method="jobTitle.method"
                :file="jobTitle.oldSpecification"
                :rules="rules.specification"
                :url="`admin/viewJobSpecification/attachId`"
                :hint="$t('general.file_upload_notice')"
              />
            </oxd-form-row>

            <oxd-form-row>
              <oxd-input-field
                type="textarea"
                :label="$t('general.note')"
                placeholder="Add note"
                v-model="jobTitle.note"
                :rules="rules.note"
                labelIcon="pencil-square"
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
import FileUploadInput from '@/core/components/inputs/FileUploadInput';

const initialJobTitle = {
  title: '',
  description: '',
  oldSpecification: '',
  newSpecification: null,
  method: 'keepCurrent',
  note: '',
};

export default {
  props: {
    jobTitleId: {
      type: String,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
  },

  components: {
    'file-upload-input': FileUploadInput,
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
          v => {
            if (this.jobTitle.method == 'replaceCurrent') {
              return required(v);
            } else {
              return true;
            }
          },
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
        .update(this.jobTitleId, {
          title: this.jobTitle.title,
          description: this.jobTitle.description,
          note: this.jobTitle.note,
          currentJobSpecification: this.jobTitle.oldSpecification
            ? this.jobTitle.method
            : undefined,
          specification: this.jobTitle.newSpecification
            ? this.jobTitle.newSpecification
            : undefined,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
  },

  created() {
    this.isLoading = true;
    this.http
      .get(this.jobTitleId)
      .then(response => {
        const {data} = response.data;
        this.jobTitle.title = data.title;
        this.jobTitle.description = data.description;
        this.jobTitle.note = data.note;
        this.jobTitle.oldSpecification = data.jobSpecification?.id
          ? data.jobSpecification
          : null;
        this.jobTitle.newSpecification = null;
        this.jobTitle.method = 'keepCurrent';

        // Fetch list data for unique test
        return this.http.getAll({limit: 0});
      })
      .then(response => {
        const {data} = response.data;
        this.rules.title.push(v => {
          const index = data.findIndex(item => item.title == v);
          if (index > -1) {
            const {id} = data[index];
            return id != this.jobTitleId ? 'Already exists' : true;
          } else {
            return true;
          }
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

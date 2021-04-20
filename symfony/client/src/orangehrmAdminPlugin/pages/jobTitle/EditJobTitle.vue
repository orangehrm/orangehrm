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
      <oxd-text tag="h6">Edit Job Title</oxd-text>

      <oxd-divider />

      <oxd-form novalidate="true" :loading="isLoading" @submitValid="onSave">
        <oxd-grid :cols="2">
          <div>
            <oxd-form-row>
              <oxd-input-field
                label="Job Title"
                v-model="jobTitle.title"
                :rules="rules.title"
                required
              />
            </oxd-form-row>

            <oxd-form-row>
              <oxd-input-field
                type="textarea"
                label="Job Description"
                placeholder="Type description here"
                v-model="jobTitle.description"
                :rules="rules.description"
              />
            </oxd-form-row>

            <oxd-form-row>
              <oxd-input-field
                type="file"
                label="Job Specification"
                buttonLabel="Browse"
                v-model="jobTitle.specification"
                :rules="rules.specification"
              />
              <oxd-text class="orangehrm-input-hint" tag="p">Accepts up to 1MB</oxd-text>
            </oxd-form-row>

            <oxd-form-row>
              <oxd-input-field
                type="textarea"
                label="Note"
                placeholder="Add note"
                v-model="jobTitle.note"
                :rules="rules.note"
              />
            </oxd-form-row>
          </div>
        </oxd-grid>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button displayType="ghost" label="Cancel" @click="onCancel" />
          <oxd-button
            type="submit"
            class="orangehrm-left-space"
            displayType="secondary"
            label="Save"
          />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';

const initialJobTitle = {
  title: '',
  description: '',
  specification: null,
  note: '',
};

export default {
  props: {
    jobTitleId: {
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
        title: [],
        description: [
          v =>
            (v && v.length <= 400) ||
            v === '' ||
            'Should be less than 400 characters',
        ],
        specification: [
          v =>
            v == null ||
            (v && v.size && v.size <= 1024 * 1024) ||
            'Attachment size exceeded',
        ],
        note: [
          v =>
            (v && v.length <= 400) ||
            v === '' ||
            'Should be less than 400 characters',
        ],
      },
    };
  },

  created() {
    this.isLoading = true;
    this.http
      .get(this.jobTitleId)
      .then(response => {
        const {data} = response.data;
        this.jobTitle = {...initialJobTitle, ...data};
        // Fetch list data for unique test
        return this.http.getAll();
      })
      .then(response => {
        const {data} = response.data;
        this.rules.title.push(v => {
          return (!!v && v.trim() !== '') || 'Required';
        });
        this.rules.title.push(v => {
          return (v && v.length < 100) || 'Should be less than 100 characters';
        });
        this.rules.title.push(v => {
          const index = data.findIndex(item => item.title == v);
          if (index > -1) {
            const {id} = data[index];
            return id != this.jobTitle.id ? 'Job title should be unique' : true;
          } else {
            return true;
          }
        });
        this.isLoading = false;
      })
      .catch(error => {
        console.log(error);
      });
  },

  methods: {
    onCancel() {
      navigate('/admin/viewJobTitleList');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.jobTitleId, {
          ...this.jobTitle,
        })
        .then(() => {
          // go back
          this.isLoading = false;
          this.onCancel();
        })
        .catch(error => {
          console.log(error);
        });
    },
  },
};
</script>

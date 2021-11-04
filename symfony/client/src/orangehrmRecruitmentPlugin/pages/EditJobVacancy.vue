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
      <oxd-text tag="h6" class="orangehrm-main-title">Edit Vacancy</oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Vacancy Name"
              v-model="vacancy.name"
              required
              :ruel="rules.name"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <jobtitle-dropdown
              v-model="vacancy.jobTitle"
              :rule="rules.jobTitle"
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              type="textarea"
              label="Description"
              placeholder="Type description here"
              v-model="vacancy.description"
              :rule="rules.description"
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-autocomplete
              :params="{
                includeEmployees: 'currentAndPast',
              }"
              required
              v-model="vacancy.hiringManager"
              :rule="rules.hiringManager"
              label="Hiring Manager"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Number Of Positions"
              v-model.number="vacancy.numOfPositions"
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="1" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-switch-input
              v-model="vacancy.status"
              optionLabel="Active Status"
              labelPosition="left"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-switch-input
              v-model="vacancy.isPublished"
              optionLabel="Publish in RSS feed and web page"
              labelPosition="left"
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button displayType="ghost" label="Cancel" @click="onCancel" />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>
<script>
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import SwitchInput from '@orangehrm/oxd/core/components/Input/SwitchInput';

import {
  required,
  shouldNotExceedCharLength,
} from '@orangehrm/core/util/validation/rules';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';

const vacancyModel = {
  jobTitle: null,
  name: '',
  hiringManager: null,
  numOfPositions: '',
  description: '',
  status: false,
  isPublished: false,
};

export default {
  props: {
    vacancyId: {
      type: String,
      required: true,
    },
  },
  name: 'edit-job-vacancy',
  components: {
    'oxd-switch-input': SwitchInput,
    'employee-autocomplete': EmployeeAutocomplete,
    'jobtitle-dropdown': JobtitleDropdown,
  },

  data() {
    return {
      isLoading: false,
      vacancy: {...vacancyModel},
      rules: {
        jobTitle: [required],
        name: [required],
        hiringManager: [required],
        numOfPositions: [],
        description: [shouldNotExceedCharLength(400)],
        status: [required],
        isPublished: [required],
      },
    };
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/recruitment/vacancies',
    );
    return {
      http,
    };
  },

  methods: {
    onCancel() {
      navigate('/recruitment/viewJobVacancy');
    },
    onSave() {
      this.isLoading = true;
      this.vacancy = {
        name: this.vacancy.name,
        jobTitleId: this.vacancy.jobTitle.id,
        employeeId: this.vacancy.hiringManager.id,
        numOfPositions: this.vacancy.numOfPositions,
        description: this.vacancy.description,
        status: this.vacancy.status ? 1 : 0,
        isPublished: this.vacancy.isPublished ? 1 : 0,
      };
      console.log(this.vacancy);
      this.http
        .update(this.vacancyId, {...this.vacancy})
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
      .get(this.vacancyId)
      .then(response => {
        console.log(response.data);
        const {data} = response.data;
        this.vacancy.name = data.name;
        this.vacancy.description = data.description;
        this.vacancy.numOfPositions = data.numOfPositions;
        this.vacancy.status = data.status === 1 ? true : false;
        this.vacancy.isPublished = data.isPublished;
        this.vacancy.hiringManager = {
          id: data.hiringManager.empNumber,
          label: `${data.hiringManager.firstName} ${data.hiringManager.middleName} ${data.hiringManager.lastName}`,
          isPastEmployee: data.hiringManager.terminationId ? true : false,
        };
        this.vacancy.jobTitle = {
          id: data.jobTitle.id,
          label: data.jobTitle.title,
        };
        return this.http.getAll({limit: 0});
      })
      .then(response => {
        const {data} = response.data;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style lang="scss" scoped></style>

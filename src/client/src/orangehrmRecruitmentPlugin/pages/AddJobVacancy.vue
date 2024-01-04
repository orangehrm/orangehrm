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
        {{ $t('recruitment.add_vacancy') }}
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="vacancy.name"
              :label="$t('recruitment.vacancy_name')"
              required
              :rules="rules.name"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <jobtitle-dropdown
              v-model="vacancy.jobTitle"
              :rules="rules.jobTitle"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangehrm-grid-item-span-2">
            <oxd-input-field
              v-model="vacancy.description"
              type="textarea"
              :label="$t('general.description')"
              :placeholder="$t('general.type_description_here')"
              :rules="rules.description"
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-autocomplete
              v-model="vacancy.hiringManager"
              :params="{
                includeEmployees: 'onlyCurrent',
              }"
              required
              :rules="rules.hiringManager"
              :label="$t('recruitment.hiring_manager')"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-field
                  v-model="vacancy.numOfPositions"
                  :label="$t('recruitment.num_of_positions')"
                  :rules="rules.numOfPositions"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangerhrm-switch-wrapper">
            <oxd-text class="orangehrm-text" tag="p">
              {{ $t('general.active') }}
            </oxd-text>
            <oxd-switch-input v-model="vacancy.status" />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangerhrm-switch-wrapper">
            <oxd-text class="orangehrm-text" tag="p">
              {{ $t('recruitment.publish_in_rss_feed_and_web_page') }}
            </oxd-text>
            <oxd-switch-input v-model="vacancy.isPublished" />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangehrm-grid-item-span-2">
            <div class="orangehrm-vacancy-links">
              <vacancy-link-card
                :label="$t('recruitment.rss_feed_url')"
                :url="rssFeedUrl"
              />
              <vacancy-link-card
                :label="$t('recruitment.web_page_url')"
                :url="webUrl"
              />
            </div>
          </oxd-grid-item>
        </oxd-grid>
        <br />
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
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {
  required,
  numericOnly,
  validSelection,
  shouldNotExceedCharLength,
  numberShouldBeBetweenMinAndMaxValue,
} from '@ohrm/core/util/validation/rules';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import VacancyLinkCard from '../components/VacancyLinkCard.vue';
import {OxdSwitchInput} from '@ohrm/oxd';
import useServerValidation from '@/core/util/composable/useServerValidation';

const vacancyModel = {
  jobTitle: null,
  name: '',
  hiringManager: null,
  numOfPositions: '',
  description: '',
  status: true,
  isPublished: true,
};

const basePath = `${window.location.protocol}//${window.location.host}${window.appGlobal.baseUrl}`;

export default {
  components: {
    'oxd-switch-input': OxdSwitchInput,
    'employee-autocomplete': EmployeeAutocomplete,
    'jobtitle-dropdown': JobtitleDropdown,
    'vacancy-link-card': VacancyLinkCard,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/vacancies',
    );
    const {createUniqueValidator} = useServerValidation(http);
    const vacancyNameUniqueValidation = createUniqueValidator(
      'Vacancy',
      'name',
    );
    return {
      http,
      vacancyNameUniqueValidation,
    };
  },
  data() {
    return {
      isLoading: false,
      vacancy: {...vacancyModel},
      rules: {
        jobTitle: [required],
        name: [
          required,
          this.vacancyNameUniqueValidation,
          shouldNotExceedCharLength(50),
        ],
        hiringManager: [required, validSelection],
        numOfPositions: [
          (value) => {
            if (value === null || value === '') return true;
            return typeof numericOnly(value) === 'string'
              ? numericOnly(value)
              : numberShouldBeBetweenMinAndMaxValue(1, 99)(value);
          },
        ],
        description: [],
        status: [required],
        isPublished: [required],
      },
      rssFeedUrl: `${basePath}/recruitmentApply/jobs.rss`,
      webUrl: `${basePath}/recruitmentApply/jobs.html`,
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
        numOfPositions: this.vacancy.numOfPositions
          ? parseInt(this.vacancy.numOfPositions)
          : null,
        description: this.vacancy.description,
        status: this.vacancy.status,
        isPublished: this.vacancy.isPublished,
      };
      this.http.create({...this.vacancy}).then((response) => {
        const {data} = response.data;
        this.$toast.saveSuccess();
        navigate('/recruitment/addJobVacancy/{id}', {id: data.id});
      });
    },
  },
};
</script>

<style src="./vacancy.scss" lang="scss" scoped></style>

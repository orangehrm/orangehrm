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
        {{ $t('time.edit_project') }}
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="project.name"
              :label="$t('general.name')"
              :rules="rules.name"
              :disabled="!$can.update(`time_projects`)"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <customer-autocomplete
              :key="project.customer"
              v-model="project.customer"
              :label="$t('time.customer_name')"
              :rules="rules.customer"
              :disabled="!$can.update(`time_projects`)"
              required
            />
            <oxd-button
              v-if="$can.update(`time_projects`)"
              icon-name="plus"
              display-type="text"
              :label="$t('time.add_customer')"
              @click="onClickAddCustomer"
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="project.description"
                type="textarea"
                :label="$t('general.description')"
                :placeholder="$t('general.type_description_here')"
                :disabled="!$can.update(`time_projects`)"
                :rules="rules.description"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <project-admin-autocomplete
                v-for="(projectAdmin, index) in projectAdmins"
                :key="index"
                v-model="projectAdmin.value"
                :show-delete="index > 0 && $can.update(`time_projects`)"
                :rules="index > 0 ? rules.projectAdmin : []"
                :disabled="!$can.update(`time_projects`)"
                include-employees="onlyCurrent"
                @remove="onRemoveAdmin(index)"
              />
              <oxd-button
                v-if="projectAdmins.length < 5 && $can.update(`time_projects`)"
                icon-name="plus"
                display-type="text"
                :label="$t('general.add_another')"
                @click="onAddAnother"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <br />
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button v-if="$can.update(`time_projects`)" />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <br />
    <activities
      :project-id="projectId"
      :unselectable-ids="unselectableIds"
    ></activities>
    <add-customer-modal
      v-if="showCustomerModal"
      @close="onCustomerModalClose"
    ></add-customer-modal>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';
import Activities from '@/orangehrmTimePlugin/components/Activities.vue';
import AddCustomerModal from '@/orangehrmTimePlugin/components/AddCustomerModal.vue';
import CustomerAutocomplete from '@/orangehrmTimePlugin/components/CustomerAutocomplete.vue';
import ProjectAdminAutocomplete from '@/orangehrmTimePlugin/components/ProjectAdminAutocomplete.vue';

const defaultProjectModel = {
  name: null,
  customer: {id: null, label: null},
  description: null,
  projectAdminEmpNumbers: [],
};

export default {
  name: 'ProjectEdit',
  components: {
    activities: Activities,
    'add-customer-modal': AddCustomerModal,
    'customer-autocomplete': CustomerAutocomplete,
    'project-admin-autocomplete': ProjectAdminAutocomplete,
  },
  props: {
    projectId: {
      type: Number,
      required: true,
    },
    unselectableIds: {
      type: Array,
      default: () => [],
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/time/projects',
    );
    http.setIgnorePath('api/v2/time/validation/project-name');
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      projectAdmins: [{value: null}],
      project: {...defaultProjectModel},
      showCustomerModal: false,
      rules: {
        name: [required, shouldNotExceedCharLength(50)],
        description: [shouldNotExceedCharLength(255)],
        customer: [required],
        projectAdmin: [
          shouldNotExceedCharLength(100),
          value => {
            return this.projectAdmins.filter(
              ({value: admin}) => admin && admin.id === value?.id,
            ).length < 2
              ? true
              : this.$t('general.already_exists');
          },
        ],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.projectId, {model: 'detailed'})
      .then(response => {
        const {data} = response.data;
        this.project.name = data.name;
        this.project.description = data.description;
        this.project.customer = {
          id: data.customer.id,
          label: data.customer.name,
        };
        if (
          Array.isArray(data.projectAdmins) &&
          data.projectAdmins.length > 0
        ) {
          this.projectAdmins = data.projectAdmins.map(projectAdmin => {
            return {
              value: {
                id: projectAdmin.empNumber,
                label: `${projectAdmin.firstName} ${projectAdmin.middleName} ${projectAdmin.lastName}`,
                isPastEmployee: projectAdmin.terminationId ? true : false,
              },
            };
          });
        }
      })
      .finally(() => {
        this.rules.name.push(promiseDebounce(this.validateProjectName, 500));
        this.isLoading = false;
      });
  },
  methods: {
    onClickAddCustomer() {
      this.showCustomerModal = true;
    },
    onCustomerModalClose(data) {
      if (data !== undefined) {
        const {id, name} = data;
        this.project.customer = {
          id,
          label: name,
        };
      }
      this.showCustomerModal = false;
    },
    onAddAnother() {
      if (this.projectAdmins.length < 5) {
        this.projectAdmins.push({value: null});
      }
    },
    onRemoveAdmin(index) {
      this.projectAdmins.splice(index, 1);
    },
    onCancel() {
      navigate('/time/viewProjects');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.projectId, {
          name: this.project.name,
          description: this.project.description,
          customerId: this.project.customer.id,
          projectAdminsEmpNumbers: this.projectAdmins
            .map(({value}) => value && value.id)
            .filter(Number),
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    validateProjectName(project) {
      return new Promise(resolve => {
        if (project) {
          this.http
            .request({
              method: 'GET',
              url: `api/v2/time/validation/project-name`,
              params: {
                projectId: this.projectId,
                projectName: this.project.name.trim(),
                customerId: this.project.customer?.id,
              },
            })
            .then(response => {
              const {data} = response.data;
              return data.valid === true
                ? resolve(true)
                : resolve(this.$t('general.already_exists'));
            });
        } else {
          resolve(true);
        }
      });
    },
  },
};
</script>

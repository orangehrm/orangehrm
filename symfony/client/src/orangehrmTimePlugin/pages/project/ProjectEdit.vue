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
        Edit Project
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="project.name"
              :label="$t('time.project_name')"
              :rules="rules.name"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-grid-row>
              <customer-autocomplete
                v-model="project.customer"
                label="Customer"
                :rules="rules.customer"
                required
              />
            </oxd-grid-row>
            <oxd-grid-row>
              <oxd-button
                display-type="text"
                :label="$t('time.add_customer')"
                icon-name="plus"
                style=""
                @click="onClickAddCustomer"
              />
            </oxd-grid-row>
          </oxd-grid-item>
        </oxd-grid>
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="project.description"
                type="textarea"
                :label="$t('general.description')"
                placeholder="Type description here"
                :rules="rules.description"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-grid-row
                v-for="projectAdmin in projectAdmins"
                :key="projectAdmin.id"
              >
                <project-admin-autocomplete
                  :id="projectAdmin.id"
                  v-model="projectAdmin.data"
                  :rules="rules.projectAdmin"
                  @remove="removeAdminInputField"
                />
              </oxd-grid-row>
              <oxd-button
                v-if="projectAdmins.length < 5"
                display-type="text"
                label="Add Another"
                icon-name="plus"
                @click="onAddAnother"
              />
              <oxd-grid-row> </oxd-grid-row>
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
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <br />
    <activities :project-id="projectId"></activities>
    <add-customer-modal
      v-if="showCustomerModal"
      @close="onModalClose"
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
import CustomerAutocomplete from '@/orangehrmTimePlugin/components/CustomerAutocomplete.vue';
import ProjectAdminAutocomplete from '@/orangehrmTimePlugin/components/ProjectAdminAutocomplete.vue';
import AddCustomerModal from '@/orangehrmTimePlugin/components/AddCustomerModal.vue';
import Activities from '@/orangehrmTimePlugin/components/Activities.vue';

const defaultProjectAdminModel = {
  id: null,
  data: {
    id: null,
    label: null,
    isPastEmployee: false,
  },
};
const defaultProjectModel = {
  name: null,
  customer: null,
  description: null,
};
export default {
  name: 'ProjectSave',
  components: {
    activities: Activities,
    'customer-autocomplete': CustomerAutocomplete,
    'project-admin-autocomplete': ProjectAdminAutocomplete,
    'add-customer-modal': AddCustomerModal,
  },
  props: {
    projectId: {
      type: Number,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/time/projects',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      projectAdmins: [],
      project: {...defaultProjectModel},
      showCustomerModal: false,
      rules: {
        name: [
          required,
          shouldNotExceedCharLength(50),
          promiseDebounce(this.validateProjectName, 500),
        ],
        description: [],
        customer: [required],
        projectAdmin: [
          value => {
            return this.projectAdmins
              .map(projectAdmin => projectAdmin.data)
              .filter(
                normalizedProjectAdmin =>
                  normalizedProjectAdmin.id === value.id,
              ).length < 2
              ? true
              : 'Already exists';
          },
        ],
      },
    };
  },
  created() {
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
        let fieldId = 0;
        this.projectAdmins = data.projectAdmins.map(projectAdmin => {
          fieldId = fieldId + 1;
          return {
            id: fieldId,
            data: {
              id: projectAdmin.empNumber,
              label: `${projectAdmin.firstName} ${projectAdmin.middleName} ${projectAdmin.lastName}`,
              isPastEmployee: projectAdmin.terminationId ? true : false,
            },
          };
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onClickAddCustomer() {
      this.showCustomerModal = true;
    },
    onModalClose() {
      this.showCustomerModal = false;
    },
    onAddAnother() {
      if (this.projectAdmins.length < 5) {
        const projectAdmin = {...defaultProjectAdminModel};
        projectAdmin.id = this.projectAdmins.length + 1;
        this.projectAdmins.push(projectAdmin);
      }
    },
    removeAdminInputField(id) {
      this.projectAdmins = this.projectAdmins.filter(projectAdmin => {
        return projectAdmin.id !== id;
      });
    },
    onCancel() {
      navigate('/time/viewProjects');
    },
    onSave() {
      this.isLoading = true;
      this.project = {
        name: this.project.name,
        description: this.project.description,
        customerId: this.project.customer.id,
        projectAdminsEmpNumbers: this.projectAdmins.map(projectAdmin => {
          return projectAdmin.data?.id;
        }),
      };
      this.http
        .update(this.projectId, {...this.project})
        .then(() => {
          return this.$toast.saveSuccess();
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
                projectName: this.project.name.trim(),
                projectId: this.projectId,
              },
            })
            .then(response => {
              const {data} = response.data;
              return data.valid === true
                ? resolve(true)
                : resolve('Already exist');
            });
        } else {
          resolve(true);
        }
      });
    },
  },
};
</script>

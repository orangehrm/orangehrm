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
        {{ $t('time.add_project') }}
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="project.name"
              :label="$t('general.name')"
              :rules="rules.name"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <customer-autocomplete
              :key="project.customer"
              v-model="project.customer"
              :rules="rules.customer"
              :label="$t('time.customer_name')"
              required
            />
            <oxd-button
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
                :rules="rules.description"
                :placeholder="$t('general.type_description_here')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <project-admin-autocomplete
                v-for="(projectAdmin, index) in projectAdmins"
                :key="index"
                v-model="projectAdmin.value"
                :show-delete="index > 0"
                :rules="index > 0 ? rules.projectAdmin : []"
                include-employees="onlyCurrent"
                @remove="onRemoveAdmin(index)"
              />
              <oxd-button
                v-if="projectAdmins.length < 5"
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
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
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
import AddCustomerModal from '@/orangehrmTimePlugin/components/AddCustomerModal.vue';
import CustomerAutocomplete from '@/orangehrmTimePlugin/components/CustomerAutocomplete.vue';
import ProjectAdminAutocomplete from '@/orangehrmTimePlugin/components/ProjectAdminAutocomplete.vue';

const defaultProjectModel = {
  name: null,
  customer: null,
  description: null,
  projectAdminEmpNumbers: [],
};

export default {
  name: 'ProjectSave',
  components: {
    'add-customer-modal': AddCustomerModal,
    'customer-autocomplete': CustomerAutocomplete,
    'project-admin-autocomplete': ProjectAdminAutocomplete,
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
      showCustomerModal: false,
      projectAdmins: [{value: null}],
      project: {...defaultProjectModel},
      projectId: null,
      rules: {
        name: [
          required,
          shouldNotExceedCharLength(50),
          promiseDebounce(this.validateProjectName, 500),
        ],
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
        .create({
          name: this.project.name,
          description: this.project.description,
          customerId: this.project.customer.id,
          projectAdminsEmpNumbers: this.projectAdmins
            .map(({value}) => value && value.id)
            .filter(Number),
        })
        .then(result => {
          this.projectId = result.data?.data.id;
          return this.$toast.saveSuccess();
        })
        .then(() => {
          navigate('/time/saveProject/{id}', {id: this.projectId});
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

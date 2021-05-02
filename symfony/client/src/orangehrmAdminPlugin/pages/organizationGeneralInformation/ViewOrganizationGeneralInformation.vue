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
      <oxd-text tag="h6">General Information</oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Organization Name"
                v-model="information.name"
                :rules="rules.name"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Number of Employees"
                v-model="information.noOfEmployees"
                :rules="rules.noOfEmployees"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Registration Number"
                v-model="information.registrationNumber"
                :rules="rules.registrationNumber"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Tax ID"
                v-model="information.taxId"
                :rules="rules.taxId"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Phone"
                v-model="information.phone"
                :rules="rules.phone"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Fax"
                v-model="information.fax"
                :rules="rules.fax"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Email"
                v-model="information.email"
                :rules="rules.email"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Address Street 1"
                v-model="information.street1"
                :rules="rules.street1"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Address Street 2"
                v-model="information.street2"
                :rules="rules.street2"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="City"
                v-model="information.city"
                :rules="rules.city"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="province/Province"
                v-model="information.province"
                :rules="rules.province"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Country"
                v-model="information.country"
                :rules="rules.country"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Zip/Postal Code"
                v-model="information.zipCode"
                :rules="rules.zipCode"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Note"
                type="textarea"
                v-model="information.note"
                :rules="rules.note"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            type="button"
            displayType="ghost"
            label="Cancel"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
      <oxd-form>
        <oxd-grid :cols="4">
          <oxd-grid-item>
            <oxd-form-row>
              <oxd-input-field label="Oraganization Name" />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-form-row>
              <oxd-input-field label="Number of Employees" />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-form-row>
              <oxd-input-field label="Registation Number" />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-form-row>
              <oxd-input-field label="Tax Id" />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-form-row>
              <oxd-input-field label="Phone" />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-form-row>
              <oxd-input-field label="Fax" />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-form-row>
              <oxd-input-field label="Email" />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item> </oxd-grid-item>
          <oxd-grid-item>
            <oxd-form-row>
              <oxd-input-field label="Address Street 1" />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item> </oxd-grid-item>
          <oxd-grid-item>
            <oxd-form-row>
              <oxd-input-field label="Address Street 2" />
            </oxd-form-row>
          </oxd-grid-item>
          <oxd-grid-item> </oxd-grid-item>
        </oxd-grid>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@orangehrm/core/util/services/api.service';

export default {
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/job-categories',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      information: {
        name: '',
        noOfEmployees: '',
        registrationNumber: '',
        taxId: '',
        phone: '',
        fax: '',
        email: '',
        street1: '',
        street2: '',
        city: '',
        province: '',
        country: '',
        zipCode: '',
        note: '',
      },
      rules: {
        name: [],
        noOfEmployees: [],
        registrationNumber: [],
        taxId: [],
        phone: [],
        fax: [],
        email: [],
        street1: [],
        street2: [],
        city: [],
        province: [],
        country: [],
        zipCode: [],
        note: [],
      },
      errors: [],
    };
  },

  methods: {
    onSave() {
      alert('saved successfully')
      // this.isLoading = true;
      // this.http
      //     .update(this.jobCategoryId, {
      //       name: this.category.name,
      //     })
      //     .then(() => {
      //       return this.$toast.success({
      //         title: 'Success',
      //         message: 'Job category updated successfully!',
      //       });
      //     })
      //     .then(() => {
      //       this.onCancel();
      //       this.isLoading = false;
      //     });
    },
    onCancel() {
      navigate('/admin/jobCategory');
    },
  },
  created() {
    this.rules.name.push(v => {
      return (!!v && v.trim() !== '') || 'Required';
    });
    this.rules.name.push(v => {
      return (v && v.length <= 10) || 'Should not exceed 10 characters';
    });
    this.rules.noOfEmployees.push(v => {
      return v.length <= 10 || 'Should not exceed 10 characters';
    });
    this.rules.registrationNumber.push(v => {
      return v.length <= 30 || 'Should not exceed 30 characters';
    });
    this.rules.taxId.push(v => {
      return v.length <= 30 || 'Should not exceed 30 characters';
    });
    this.rules.phone.push(v => {
      return v.length <= 30 || 'Should not exceed 30 characters';
    });
    this.rules.fax.push(v => {
      return v.length <= 30 || 'Should not exceed 30 characters';
    });
    this.rules.email.push(v => {
      return v.length <= 30 || 'Should not exceed 30 characters';
    });
    this.rules.street1.push(v => {
      return v.length <= 100 || 'Should not exceed 100 characters';
    });
    this.rules.street2.push(v => {
      return v.length <= 100 || 'Should not exceed 100 characters';
    });
    this.rules.city.push(v => {
      return v.length <= 30 || 'Should not exceed 30 characters';
    });
    this.rules.province.push(v => {
      return v.length <= 30 || 'Should not exceed 30 characters';
    });
    this.rules.country.push(v => {
      return v.length <= 30 || 'Should not exceed 30 characters';
    });
    this.rules.zipCode.push(v => {
      return v.length <= 30 || 'Should not exceed 30 characters';
    });
    this.rules.note.push(v => {
      return v.length <= 255 || 'Should not exceed 255 characters';
    });
    this.rules.noOfEmployees.push(v => {
      return /^\d+$/.test(v) || 'Should be a valid number';
    });
    // this.isLoading = true;
    // this.http
    //   .get(this.jobCategoryId)
    //   .then(response => {
    //     const {data} = response.data;
    //     this.category.id = data.id;
    //     this.category.name = data.name;
    //     // Fetch list data for unique test
    //     return this.http.getAll();
    //   })
    //   .then(response => {
    //     const {data} = response.data;
    //     this.rules.name.push(v => {
    //       return (!!v && v.trim() !== '') || 'Required';
    //     });
    //     this.rules.name.push(v => {
    //       return (v && v.length <= 50) || 'Should not exceed 50 characters';
    //     });
    //     this.rules.name.push(v => {
    //       const index = data.findIndex(item => item.name == v);
    //       if (index > -1) {
    //         const {id} = data[index];
    //         return id != this.category.id
    //           ? 'Job category name should be unique'
    //           : true;
    //       } else {
    //         return true;
    //       }
    //     });
    //     this.isLoading = false;
    //   })
    //   .finally(() => {
    //     this.isLoading = false;
    //   });
  },
};
</script>

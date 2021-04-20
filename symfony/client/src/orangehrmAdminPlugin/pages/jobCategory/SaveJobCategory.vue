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
      <oxd-text tag="h6">Save Job Category</oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-input-field
            label="Job Category Name"
            v-model="category.name"
            :rules="rules.name"
            required
          />
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            type="button"
            displayType="ghost"
            label="Cancel"
            @click="onCancel"
          />
          <oxd-button
            class="orangehrm-left-space"
            displayType="secondary"
            label="Submit"
            type="submit"
          />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';

export default {
  data() {
    return {
      isLoading: false,
      category: {
        id: '',
        name: '',
      },
      rules: {
        name: [],
      },
      errors: [],
    };
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/job-categories',
    );
    return {
      http,
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          name: this.category.name,
        })
        .then(() => {
          this.category.name = '';
          this.isLoading = false;
          this.onCancel();
        })
        .catch(error => {
          console.log(error);
        });
    },
    onCancel() {
      navigate('/admin/jobCategory');
    },
  },

  created() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.rules.name.push(v => {
          return (!!v && v.trim() !== '') || 'Required';
        });
        this.rules.name.push(v => {
          return (v && v.length < 50) || 'Should be less than 50 characters';
        });
        this.rules.name.push(v => {
          const index = data.findIndex(item => item.name == v);
          return index === -1 || 'Job category name should be unique';
        });
        this.isLoading = false;
      })
      .catch(error => {
        this.isLoading = false;
        console.log(error);
      });
  },
};
</script>

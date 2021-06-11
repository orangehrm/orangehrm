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
      <oxd-text tag="h6"> Edit Skill</oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-input-field
            label="Name"
            v-model="skill.name"
            :rules="rules.name"
            required
          />
        </oxd-form-row>

        <oxd-form-row>
          <oxd-input-field
            type="textarea"
            label="Description"
            placeholder="Type description here"
            v-model="skill.description"
            :rules="rules.description"
          />
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            type="button"
            displayType="ghost"
            label="Cancel"
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
import {APIService} from '@orangehrm/core/util/services/api.service';

const skillModel = {
  id: '',
  name: '',
  description: '',
};

export default {
  props: {
    qualificationSkillId: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      isLoading: false,
      skill: {...skillModel},
      rules: {
        name: [],
        description: [
          v =>
            (v && v.length <= 400) ||
            v === '' ||
            'Should not exceed 400 characters',
        ],
      },
    };
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/skills',
    );
    return {
      http,
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.qualificationSkillId, {
          name: this.skill.name,
          description: this.skill.description,
        })
        .then(() => {
          return this.$toast.success({
            title: 'Success',
            message: 'Successfully Updated',
          });
        })
        .then(() => {
          this.onCancel();
          this.isLoading = false;
        });
    },
    onCancel() {
      navigate('/admin/viewSkills');
    },
  },
  created() {
    this.isLoading = true;
    this.http
      .get(this.qualificationSkillId)
      .then(response => {
        const {data} = response.data;
        this.skill.id = data.id;
        this.skill.name = data.name;
        this.skill.description = data.description;

        // Fetch list data for unique test
        return this.http.getAll();
      })
      .then(response => {
        const {data} = response.data;
        this.rules.name.push(v => {
          return (!!v && v.trim() !== '') || 'Required';
        });
        this.rules.name.push(v => {
          return (v && v.length <= 120) || 'Should not exceed 120 characters';
        });
        this.rules.name.push(v => {
          const index = data.findIndex(item => item.name == v);
          if (index > -1) {
            const {id} = data[index];
            return id != this.category.id ? 'Already exists' : true;
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

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
        Edit Todo
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-input-field
            label="Name"
            v-model="todo.name"
            :rules="rules.name"
            placeholder="Type name here"
            required
          />
          <oxd-input-field
            label="Date"
            v-model="todo.date"
            :rules="rules.date"
            type="date"
            placeholder="yyyy-mm-dd"
            required
          />
          <oxd-input-field
            type="textarea"
            label="Description"
            placeholder="Type description here"
            v-model="todo.description"
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
import {APIService} from '@/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
  validDateFormat,
} from '@orangehrm/core/util/validation/rules';

const todoModel = {
  name: '',
  date: '',
  description: null,
};

export default {
  props: {
    todoId: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      isLoading: false,
      todo: {...todoModel},
      rules: {
        name: [required],
        date: [required, validDateFormat('yyyy-MM-dd')],
        description: [shouldNotExceedCharLength(200)],
      },
    };
  },

  setup() {
    const http = new APIService(window.appGlobal.baseUrl, 'api/v2/todos');
    return {
      http,
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.todoId, {...this.todo})
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/todo/items');
    },
  },

  created() {
    this.isLoading = true;
    this.http
      .get(this.todoId)
      .then(response => {
        const {data} = response.data;
        this.todo = {...data};
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

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
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-button
          label="Add"
          iconName="plus"
          displayType="secondary"
          @click="onClickAdd"
        />
      </div>
      <table-header
        :selected="checkedItems.length"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
        :total="vacancies.length"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="vacancies"
          :loading="isLoading"
          :selectable="true"
          :clickable="false"
          rowDecorator="oxd-table-decorator-card"
          v-model:selected="checkedItems"
        />
      </div>
    </div>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import DeleteConfirmationDialog from '@orangehrm/components/dialogs/DeleteConfirmationDialog';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import useSort from '@orangehrm/core/util/composable/useSort';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';

const userdataNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      vacancy: item.name,
      jobTitle: item.jobTitle?.isDeleted
        ? item.jobTitle.title + ' (Deleted)'
        : item.jobTitle?.title,
      hiringManger: `${item.hiringManager.firstName} ${item.hiringManager.lastName}`,
      status: item.status == 1 ? 'Active' : 'Inactive',
    };
  });
};

export default {
  name: 'view-job-vacancy',
  components: {
    // 'jobtitle-dropdown': JobtitleDropdown,
    'delete-confirmation': DeleteConfirmationDialog,
  },

  data() {
    return {
      headers: [
        {
          name: 'vacancy',
          slot: 'Vacancy',
          title: 'Vacancy',
          style: {flex: 1},
        },
        {
          name: 'jobTitle',
          title: 'Job Title',
          style: {flex: 1},
        },
        {
          name: 'hiringManger',
          title: 'Hiring Manager',
          style: {flex: 1},
        },
        {
          name: 'status',
          title: 'Status',
          style: {flex: 1},
        },
        {
          name: 'actions',
          slot: 'action',
          title: 'Actions',
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            delete: {
              onClick: this.onClickDelete,
              component: 'oxd-icon-button',
              props: {
                name: 'trash',
              },
            },
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: 'pencil-fill',
              },
            },
          },
        },
      ],
      http: null,
      vacancies: [],
      checkedItems: [],
      isLoading: false,
    };
  },

  created() {
    const http = this.getHttp();
    this.getAllData();
  },
  methods: {
    getHttp() {
      const http = new APIService(
        window.appGlobal.baseUrl,
        'api/v2/recruitment/vacancies',
      );
      this.http = http;
      return http;
    },
    onClickAdd() {
      navigate('/recruitment/addJobVacancy');
    },
    onClickEdit(item) {
      navigate('/recruitment/addJobVacancy/{id}', {id: item.id});
    },
    async getAllData() {
      this.isLoading = true;
      const result = await this.http.getAll();
      this.vacancies = userdataNormalizer(result.data.data);
      this.isLoading = false;
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteData([item.id]);
        }
      });
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map(index => {
        return this.vacancies[index]?.id;
      });
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteData(ids);
        }
      });
    },
    async deleteData(items) {
      this.isLoading = true;
      const result = await this.http.deleteAll({ids: items});
      if (result) {
        this.getAllData();
      }
    },
  },
};
</script>

<style lang="scss" scoped></style>

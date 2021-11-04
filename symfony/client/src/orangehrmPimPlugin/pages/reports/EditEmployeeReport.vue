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
      <oxd-text tag="h6" class="orangehrm-main-title">Edit Report</oxd-text>
      <oxd-divider />

      <oxd-form ref="formRef" :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Report Name"
                placeholder="Type here..."
                v-model="report.name"
                :rules="rules.name"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-row>
          <oxd-text class="orangehrm-sub-title" tag="h6">
            Selection Criteria
          </oxd-text>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-report-criteria --span-column-2">
              <oxd-input-field
                type="select"
                label="Selection Criteria"
                v-model="report.criterion"
                :options="availableCriteria"
              />
              <oxd-input-group>
                <oxd-icon-button
                  class="orangehrm-report-icon"
                  name="plus"
                  @click="addCriterion"
                />
              </oxd-input-group>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                label="Include"
                v-model="report.includeEmployees"
                :options="includeOpts"
                :show-empty-selector="false"
              />
            </oxd-grid-item>
            <!-- start selected criteria fields -->
            <report-criterion
              v-for="(criterion, index) in report.criteriaSelected"
              :key="criterion"
              :criterion="criterion"
              v-model:operator="
                report.criteriaFieldValues[criterion.id].operator
              "
              v-model:valueX="report.criteriaFieldValues[criterion.id].valueX"
              v-model:valueY="report.criteriaFieldValues[criterion.id].valueY"
              @delete="removeCriterion(index)"
            >
            </report-criterion>
            <!-- end selected criteria fields -->
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-row>
          <oxd-text class="orangehrm-sub-title" tag="h6">
            Display Fields
          </oxd-text>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                label="Select Display Field Group"
                v-model="report.fieldGroup"
                :options="availableFieldGroups"
              />
            </oxd-grid-item>
            <oxd-grid-item class="orangehrm-report-criteria --span-column-2">
              <oxd-input-field
                type="select"
                label="Select Display Field"
                v-model="report.displayField"
                :options="availableDisplyFields"
              />
              <oxd-input-group>
                <oxd-icon-button
                  class="orangehrm-report-icon"
                  name="plus"
                  @click="addDisplayField"
                />
              </oxd-input-group>
            </oxd-grid-item>
            <oxd-grid-item v-if="errorMsg" class="--offset-column-1">
              <oxd-text class="orangehrm-report-error" tag="p">
                {{ errorMsg }}
              </oxd-text>
            </oxd-grid-item>
            <!-- start display group fields -->
            <report-display-field
              v-for="(fieldGroup, index) in report.fieldGroupSelected"
              :key="fieldGroup"
              :field-group="fieldGroup"
              :selected-fields="
                report.displayFieldSelected[fieldGroup.id].fields
              "
              v-model:includeHeader="
                report.displayFieldSelected[fieldGroup.id].includeHeader
              "
              @delete="removeDisplayFieldGroup(index)"
              @deleteChip="removeDisplayField($event, index)"
            >
            </report-display-field>
            <!-- end display group fields -->
          </oxd-grid>
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
import {
  required,
  shouldNotExceedCharLength,
} from '@orangehrm/core/util/validation/rules';
import {APIService} from '@orangehrm/core/util/services/api.service';
import ReportCriterion from '@/orangehrmPimPlugin/components/ReportCriterion';
import ReportDisplayField from '@/orangehrmPimPlugin/components/ReportDisplayField';
import useEmployeeReport from '@/orangehrmPimPlugin/util/composable/useEmployeeReport';

export default {
  components: {
    'report-criterion': ReportCriterion,
    'report-display-field': ReportDisplayField,
  },

  props: {
    selectionCriteria: {
      type: Array,
      required: true,
    },
    displayFieldGroups: {
      type: Array,
      required: true,
    },
    displayFields: {
      type: Array,
      required: true,
    },
    reportId: {
      type: String,
      required: true,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/pim/reports/defined',
    );
    const {
      report,
      formRef,
      errorMsg,
      addCriterion,
      serializeBody,
      addDisplayField,
      removeCriterion,
      removeDisplayField,
      removeDisplayFieldGroup,
      availableCriteria,
      availableFieldGroups,
      availableDisplyFields,
    } = useEmployeeReport(
      props.selectionCriteria,
      props.displayFields,
      props.displayFieldGroups,
    );

    return {
      http,
      report,
      formRef,
      errorMsg,
      addCriterion,
      serializeBody,
      addDisplayField,
      removeCriterion,
      removeDisplayField,
      removeDisplayFieldGroup,
      availableCriteria,
      availableFieldGroups,
      availableDisplyFields,
    };
  },

  data() {
    return {
      isLoading: false,
      rules: {
        name: [required, shouldNotExceedCharLength(250)],
        includeEmployees: [required],
      },
      includeOpts: [
        {id: 1, key: 'onlyCurrent', label: 'Current Employees Only'},
        {id: 2, key: 'currentAndPast', label: 'Current and Past Employees'},
        {id: 3, key: 'onlyPast', label: 'Past Employees Only'},
      ],
    };
  },

  methods: {
    onCancel() {
      navigate('/pim/viewDefinedPredefinedReports');
    },
    onSave() {
      this.isLoading = true;
      const payload = this.serializeBody(this.report);
      this.http
        .update(this.reportId, payload)
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          navigate('/pim/displayPredefinedReport/{id}', {id: this.reportId});
        });
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.reportId)
      .then(response => {
        const {data} = response.data;
        this.report.name = data.name;
        this.report.includeEmployees = this.includeOpts.find(
          opt => opt.key === data.include,
        );
        const operators = [
          {id: 'eq', label: 'Equal'},
          {id: 'lt', label: 'Less Than'},
          {id: 'gt', label: 'Greater Than'},
          {id: 'between', label: 'Range'},
        ];
        for (const key in data.fieldGroup) {
          const fieldGroup = this.displayFields.find(
            group => group.field_group_id == key,
          );
          this.report.fieldGroupSelected.push(
            this.displayFieldGroups.find(group => group.id == key),
          );
          this.report.displayFieldSelected[key] = {
            fields: data.fieldGroup[key].fields.map(id =>
              fieldGroup.fields.find(field => field.id === id),
            ),
            includeHeader: data.fieldGroup[key].includeHeader,
          };
        }
        for (const key in data.criteria) {
          const criterion = this.selectionCriteria.find(
            criterion => criterion.id == key,
          );
          this.report.criteriaSelected.push(criterion);
          this.report.criteriaFieldValues[key] = {
            valueX: data.criteria[key].x,
            valueY:
              data.criteria[key].y === 'undefined'
                ? null
                : data.criteria[key].y,
            operator: operators.find(o => o.id === data.criteria[key].operator),
          };
        }
        // Fetch list data for unique test
        return this.http.getAll({limit: 0});
      })
      .then(response => {
        const {data} = response.data;
        this.rules.name.push(v => {
          const index = data.findIndex(item => item.name == v);
          if (index > -1) {
            const {id} = data[index];
            return id != this.reportId ? 'Already exists' : true;
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

<style src="./employee-report.scss" lang="scss" scoped></style>

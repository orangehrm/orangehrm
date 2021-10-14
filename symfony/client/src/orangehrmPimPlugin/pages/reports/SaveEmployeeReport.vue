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
      <oxd-text tag="h6" class="orangehrm-main-title">Add Report</oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Report Name"
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
                v-model="report.criteria"
                :rules="rules.criteria"
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
                :rules="rules.fieldGroup"
                :options="availableFieldGroups"
              />
            </oxd-grid-item>
            <oxd-grid-item class="orangehrm-report-criteria --span-column-2">
              <oxd-input-field
                type="select"
                label="Select Display Field"
                v-model="report.displayField"
                :rules="rules.displayField"
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
            <!-- start display group fields -->
            <report-display-field
              v-for="(fieldGroup, index) in report.fieldGroupSelected"
              :key="fieldGroup"
              :fieldGroup="fieldGroup"
              :selectedFields="report.displayFieldSelected[fieldGroup.id]"
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
            label="Back"
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
import {required} from '@orangehrm/core/util/validation/rules';
import ReportCriterion from '@/orangehrmPimPlugin/components/ReportCriterion';
import ReportDisplayField from '@/orangehrmPimPlugin/components/ReportDisplayField';

const reportModel = {
  name: '',
  includeEmployees: {
    id: 1,
    param: 'onlyCurrent',
    label: 'Current Employees Only',
  },
  criteria: null,
  criteriaSelected: [],
  fieldGroup: null,
  fieldGroupSelected: [],
  displayField: null,
  displayFieldSelected: {},
};

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
  },

  data() {
    return {
      isLoading: false,
      report: {...reportModel},
      rules: {
        name: [required],
        criteria: [],
        includeEmployees: [],
        fieldGroup: [],
        displayField: [],
      },
      includeOpts: [
        {id: 1, param: 'onlyCurrent', label: 'Current Employees Only'},
        {id: 2, param: 'currentAndPast', label: 'Current and Past Employees'},
        {id: 3, param: 'onlyPast', label: 'Past Employees Only'},
      ],
    };
  },

  methods: {
    onCancel() {
      navigate('/pim/viewDefinedPredefinedReports');
    },
    onSave() {
      // TODO: save
    },
    addCriterion() {
      if (this.report.criteria) {
        this.report.criteriaSelected.push(this.report.criteria);
        this.report.criteria = null;
      }
    },
    removeCriterion(index) {
      this.report.criteriaSelected.splice(index, 1);
      // TODO: reset vmodel
    },
    addDisplayField() {
      const fieldGroup = this.report.fieldGroup;
      const displayField = this.report.displayField;
      if (displayField) {
        const groupIndex = this.report.fieldGroupSelected.findIndex(
          group => group.id === fieldGroup.id,
        );
        if (groupIndex === -1) {
          this.report.fieldGroupSelected.push(fieldGroup);
          this.report.displayFieldSelected[fieldGroup.id] = [];
        }
        this.report.displayFieldSelected[fieldGroup.id].push(displayField);
        this.report.displayField = null;
      }
    },
    removeDisplayField(item, index) {
      const fieldGroup = this.report.fieldGroupSelected[index];
      const dispalyField = this.report.displayFieldSelected[fieldGroup.id];
      this.report.displayFieldSelected[fieldGroup.id] = dispalyField.filter(
        field => field.id !== item.id,
      );
    },
    removeDisplayFieldGroup(index) {
      const fieldGroup = this.report.fieldGroupSelected[index];
      this.report.fieldGroupSelected.splice(index, 1);
      delete this.report.displayFieldSelected[fieldGroup.id];
    },
  },

  computed: {
    availableCriteria() {
      return this.selectionCriteria.filter(
        criterion =>
          !this.report.criteriaSelected.find(c => c.id === criterion.id),
      );
    },
    availableFieldGroups() {
      return this.displayFieldGroups;
    },
    availableDisplyFields() {
      const fieldGroupId = this.report.fieldGroup?.id;
      const fieldGroup = this.displayFields.find(
        group => group.field_group_id === fieldGroupId,
      );
      return fieldGroup
        ? fieldGroup.fields.filter(
            field =>
              !this.report.displayFieldSelected[fieldGroupId]?.find(
                f => f.id === field.id,
              ),
          )
        : [];
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-report {
  &-criteria {
    display: flex;
    align-items: center;
  }
  &-icon {
    margin-left: 1rem;
  }
}
</style>

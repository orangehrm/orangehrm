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
      // this.isLoading = true;
      console.log(this.report);
    },
    addCriterion() {
      if (this.report.criteria) {
        this.report.criteriaSelected.push(this.report.criteria);
        this.report.criteria = null;
      }
    },
    removeCriterion(index) {
      this.report.criteriaSelected.splice(index, 1);
      // todo reset vmodel
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
      let fields = [];
      const fieldGroupId = this.report.fieldGroup?.id;
      switch (fieldGroupId) {
        case 'display_group_1':
          fields = [
            {id: 'display_field_9', label: 'Employee Id'},
            {id: 'display_field_10', label: 'Employee Last Name'},
            {id: 'display_field_11', label: 'Employee First Name'},
            {id: 'display_field_12', label: 'Employee Middle Name'},
            {id: 'display_field_13', label: 'Date of Birth'},
            {id: 'display_field_14', label: 'Nationality'},
            {id: 'display_field_15', label: 'Gender'},
            {id: 'display_field_17', label: 'Marital Status'},
            {id: 'display_field_18', label: 'Driver License Number'},
            {id: 'display_field_19', label: 'License Expiry Date'},
            {id: 'display_field_97', label: 'Other Id'},
          ];
          break;
        case 'display_group_2':
          fields = [
            {id: 'display_field_20', label: 'Address'},
            {id: 'display_field_21', label: 'Home Telephone'},
            {id: 'display_field_22', label: 'Mobile'},
            {id: 'display_field_23', label: 'Work Telephone'},
            {id: 'display_field_24', label: 'Work Email'},
            {id: 'display_field_25', label: 'Other Email'},
          ];
          break;
        case 'display_group_3':
          fields = [
            {id: 'display_field_26', label: 'Name'},
            {id: 'display_field_27', label: 'Home Telephone'},
            {id: 'display_field_28', label: 'Work Telephone'},
            {id: 'display_field_29', label: 'Relationship'},
            {id: 'display_field_30', label: 'Mobile'},
          ];
          break;
        case 'display_group_4':
          fields = [
            {id: 'display_field_31', label: 'Name'},
            {id: 'display_field_32', label: 'Relationship'},
            {id: 'display_field_33', label: 'Date of Birth'},
          ];
          break;
        case 'display_group_15':
          fields = [
            {id: 'display_field_35', label: 'Membership'},
            {id: 'display_field_36', label: 'Subscription Paid By'},
            {id: 'display_field_37', label: 'Subscription Amount'},
            {id: 'display_field_38', label: 'Currency'},
            {id: 'display_field_39', label: 'Subscription Commence Date'},
            {id: 'display_field_40', label: 'Subscription Renewal Date'},
          ];
          break;
        case 'display_group_10':
          fields = [
            {id: 'display_field_41', label: 'Company'},
            {id: 'display_field_42', label: 'Job Title'},
            {id: 'display_field_43', label: 'From'},
            {id: 'display_field_44', label: 'To'},
            {id: 'display_field_45', label: 'Comment'},
            {id: 'display_field_112', label: 'Duration'},
          ];
          break;
        case 'display_group_11':
          fields = [
            {id: 'display_field_47', label: 'Level'},
            {id: 'display_field_48', label: 'Year'},
            {id: 'display_field_49', label: 'Score'},
            {id: 'display_field_115', label: 'Institute'},
            {id: 'display_field_116', label: 'Major/Specialization'},
            {id: 'display_field_117', label: 'Start Date'},
            {id: 'display_field_118', label: 'End Date'},
          ];
          break;
        case 'display_group_12':
          fields = [
            {id: 'display_field_52', label: 'Skill'},
            {id: 'display_field_53', label: 'Years of Experience'},
            {id: 'display_field_54', label: 'Comments'},
          ];
          break;
        case 'display_group_13':
          fields = [
            {id: 'display_field_55', label: 'Language'},
            {id: 'display_field_57', label: 'Competency'},
            {id: 'display_field_58', label: 'Comments'},
            {id: 'display_field_92', label: 'Fluency'},
          ];
          break;
        case 'display_group_14':
          fields = [
            {id: 'display_field_59', label: 'License Type'},
            {id: 'display_field_60', label: 'Issued Date'},
            {id: 'display_field_61', label: 'Expiry Date'},
            {id: 'display_field_119', label: 'License Number'},
          ];
          break;
        case 'display_group_9':
          fields = [
            {id: 'display_field_62', label: 'First Name'},
            {id: 'display_field_64', label: 'Last Name'},
            {id: 'display_field_93', label: 'Reporting Method'},
          ];
          break;
        case 'display_group_8':
          fields = [
            {id: 'display_field_63', label: 'First Name'},
            {id: 'display_field_91', label: 'Last Name'},
            {id: 'display_field_94', label: 'Reporting Method'},
          ];
          break;
        case 'display_group_7':
          fields = [
            {id: 'display_field_65', label: 'Pay Grade'},
            {id: 'display_field_66', label: 'Salary Component'},
            {id: 'display_field_67', label: 'Amount'},
            {id: 'display_field_68', label: 'Comments'},
            {id: 'display_field_69', label: 'Pay Frequency'},
            {id: 'display_field_70', label: 'Currency'},
            {id: 'display_field_71', label: 'Direct Deposit Account Number'},
            {id: 'display_field_72', label: 'Direct Deposit Account Type'},
            {id: 'display_field_73', label: 'Direct Deposit Routing Number'},
            {id: 'display_field_74', label: 'Direct Deposit Amount'},
          ];
          break;
        case 'display_group_6':
          fields = [
            {id: 'display_field_75', label: 'Contract Start Date'},
            {id: 'display_field_76', label: 'Contract End Date'},
            {id: 'display_field_77', label: 'Job Title'},
            {id: 'display_field_78', label: 'Employment Status'},
            {id: 'display_field_80', label: 'Job Category'},
            {id: 'display_field_81', label: 'Joined Date'},
            {id: 'display_field_82', label: 'Sub Unit'},
            {id: 'display_field_83', label: 'Location'},
            {id: 'display_field_113', label: 'Termination Date'},
            {id: 'display_field_114', label: 'Termination Reason'},
            {id: 'display_field_120', label: 'Termination Note'},
          ];
          break;
        default:
          fields = [
            {id: 'display_field_84', label: 'Number'},
            {id: 'display_field_85', label: 'Issued Date'},
            {id: 'display_field_86', label: 'Expiry Date'},
            {id: 'display_field_87', label: 'Eligibility Status'},
            {id: 'display_field_88', label: 'Issued By'},
            {id: 'display_field_89', label: 'Eligibility Review Date'},
            {id: 'display_field_90', label: 'Comments'},
            {id: 'display_field_95', label: 'Document Type'},
          ];
      }
      return fields.filter(
        field =>
          !this.report.displayFieldSelected[fieldGroupId]?.find(
            f => f.id === field.id,
          ),
      );
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

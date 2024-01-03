<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
 -->

<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">{{
        $t('pim.edit_report')
      }}</oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="report.name"
                :label="$t('general.report_name')"
                :placeholder="$t('general.type_here_message')"
                :rules="rules.name"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-row>
          <oxd-text class="orangehrm-sub-title" tag="h6">
            {{ $t('pim.selection_criteria') }}
          </oxd-text>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-report-criteria --span-column-2">
              <oxd-input-field
                v-model="report.criterion"
                type="select"
                :label="$t('pim.selection_criteria')"
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
                v-model="report.includeEmployees"
                type="select"
                :label="$t('pim.include')"
                :options="includeOpts"
                :show-empty-selector="false"
              />
            </oxd-grid-item>
            <!-- start selected criteria fields -->
            <report-criterion
              v-for="(criterion, index) in report.criteriaSelected"
              :key="criterion"
              v-model:operator="
                report.criteriaFieldValues[criterion.id].operator
              "
              v-model:valueX="report.criteriaFieldValues[criterion.id].valueX"
              v-model:valueY="report.criteriaFieldValues[criterion.id].valueY"
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
            {{ $t('pim.display_fields') }}
          </oxd-text>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="report.fieldGroup"
                type="select"
                :label="$t('pim.select_display_field_group')"
                :options="availableFieldGroups"
              />
            </oxd-grid-item>
            <oxd-grid-item class="orangehrm-report-criteria --span-column-2">
              <oxd-input-field
                v-model="report.displayField"
                type="select"
                :label="$t('pim.select_display_field')"
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
              v-model:includeHeader="
                report.displayFieldSelected[fieldGroup.id].includeHeader
              "
              :field-group="fieldGroup"
              :selected-fields="
                report.displayFieldSelected[fieldGroup.id].fields
              "
              @delete="removeDisplayFieldGroup(index)"
              @delete-chip="removeDisplayField($event, index)"
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
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@ohrm/core/util/helper/navigation';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import {APIService} from '@ohrm/core/util/services/api.service';
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
      type: Number,
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
        {
          id: 1,
          key: 'onlyCurrent',
          label: this.$t('general.current_employees_only'),
        },
        {
          id: 2,
          key: 'currentAndPast',
          label: this.$t('general.current_and_past_employees'),
        },
        {id: 3, key: 'onlyPast', label: this.$t('general.past_employees_only')},
      ],
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.reportId)
      .then((response) => {
        const {data} = response.data;
        this.report.name = data.name;
        this.report.includeEmployees = this.includeOpts.find(
          (opt) => opt.key === data.include,
        );
        const operators = [
          {id: 'eq', label: 'Equal'},
          {id: 'lt', label: 'Less Than'},
          {id: 'gt', label: 'Greater Than'},
          {id: 'between', label: 'Range'},
        ];
        for (const key in data.fieldGroup) {
          const fieldGroup = this.displayFields.find(
            (group) => group.field_group_id == key,
          );
          this.report.fieldGroupSelected.push(
            this.displayFieldGroups.find((group) => group.id == key),
          );
          this.report.displayFieldSelected[key] = {
            fields: data.fieldGroup[key].fields.map((id) =>
              fieldGroup.fields.find((field) => field.id === id),
            ),
            includeHeader: data.fieldGroup[key].includeHeader,
          };
        }
        for (const key in data.criteria) {
          const criterion = this.selectionCriteria.find(
            (criterion) => criterion.id == key,
          );
          this.report.criteriaSelected.push(criterion);
          this.report.criteriaFieldValues[key] = {
            valueX: data.criteria[key].x,
            valueY:
              data.criteria[key].y === 'undefined'
                ? null
                : data.criteria[key].y,
            operator: operators.find(
              (o) => o.id === data.criteria[key].operator,
            ),
          };
        }
        // Fetch list data for unique test
        return this.http.getAll({limit: 0});
      })
      .then((response) => {
        const {data} = response.data;
        this.rules.name.push((v) => {
          const index = data.findIndex((item) => item.name == v);
          if (index > -1) {
            const {id} = data[index];
            return id != this.reportId
              ? this.$t('general.already_exists')
              : true;
          } else {
            return true;
          }
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onCancel() {
      navigate('/pim/viewDefinedPredefinedReports');
    },
    onSave() {
      if (Object.keys(this.report.displayFieldSelected).length === 0) {
        return this.$toast.warn({
          title: this.$t('general.warning'),
          message: this.$t('pim.at_least_one_display_field_should_be_added'),
        });
      }

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
};
</script>

<style src="./employee-report.scss" lang="scss" scoped></style>

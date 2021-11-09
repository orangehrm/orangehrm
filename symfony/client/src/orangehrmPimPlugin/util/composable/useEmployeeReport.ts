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

import {reactive, toRefs, computed} from 'vue';
interface Option {
  id: number;
  label: string;
}
interface IncludeOption extends Option {
  key: string;
}
interface CriterionOption extends Option {
  key: string;
}
interface DisplayFieldOption {
  field_group_id: number;
  fields: Option[];
}
interface Criterion {
  operator: {id: string; label: string} | null;
  valueX: string | Option | null;
  valueY: string | Option | null;
}
interface DisplayField {
  fields: Option[];
  includeHeader: boolean;
}
interface Criteria {
  [id: number]: Criterion;
}
interface DisplayFieldGroup {
  [id: number]: DisplayField;
}
interface ReportModel {
  name: string;
  includeEmployees: IncludeOption;
  criterion: CriterionOption | null;
  criteriaSelected: CriterionOption[];
  criteriaFieldValues: Criteria;
  fieldGroup: Option | null;
  fieldGroupSelected: Option[];
  displayField: Option | null;
  displayFieldSelected: DisplayFieldGroup;
}
interface ReportRequestBody {
  name: string;
  include: string;
  criteria: {
    [id: number]: {
      operator: string;
      x: string;
      y: string;
    };
  };
  fieldGroup: {
    [id: number]: {
      fields: number[];
      includeHeader: boolean;
    };
  };
}

const reportModel: ReportModel = {
  name: '',
  includeEmployees: {
    id: 1,
    key: 'onlyCurrent',
    label: 'Current Employees Only',
  },
  criterion: null,
  criteriaSelected: [],
  criteriaFieldValues: {},
  fieldGroup: null,
  fieldGroupSelected: [],
  displayField: null,
  displayFieldSelected: {},
};

export default function useEmployeeReport(
  selectionCriteria: CriterionOption[],
  displayFields: DisplayFieldOption[],
  displayFieldGroups: Option[],
) {
  const state = reactive({report: {...reportModel}});

  const getAllDisplayFieldsByGroupId = (groupId: number) => {
    const fieldGroup = displayFields.find(
      group => group.field_group_id === groupId,
    );
    return fieldGroup ? fieldGroup.fields : [];
  };

  const getUnusedDisplayFieldsByGroupId = (groupId: number) => {
    const selectedFieldGroup = state.report.displayFieldSelected[groupId];
    const usedDisplayFields = selectedFieldGroup
      ? selectedFieldGroup.fields
      : [];
    return getAllDisplayFieldsByGroupId(groupId).filter(
      field => !usedDisplayFields.find(f => f.id === field.id),
    );
  };

  const addCriterion = () => {
    const criterion = state.report.criterion;
    if (criterion) {
      state.report.criteriaSelected.push(criterion);
      state.report.criteriaFieldValues[criterion.id] = {
        valueX: null,
        valueY: null,
        operator: null,
      };
      state.report.criterion = null;
    }
  };

  const removeCriterion = (index: number) => {
    const criterion = state.report.criteriaSelected.splice(index, 1);
    delete state.report.criteriaFieldValues[criterion[0].id];
  };

  const addDisplayField = () => {
    const fieldGroup = state.report.fieldGroup;
    const displayField = state.report.displayField;
    if (fieldGroup) {
      const groupIndex = state.report.fieldGroupSelected.findIndex(
        group => group.id === fieldGroup.id,
      );
      if (groupIndex === -1) {
        state.report.fieldGroupSelected.push(fieldGroup);
        state.report.displayFieldSelected[fieldGroup.id] = {
          fields: [],
          includeHeader: false,
        };
      }
      if (displayField) {
        state.report.displayFieldSelected[fieldGroup.id].fields.push(
          displayField,
        );
        state.report.displayField = null;
      } else {
        getUnusedDisplayFieldsByGroupId(fieldGroup.id).forEach(displayField => {
          state.report.displayFieldSelected[fieldGroup.id].fields.push(
            displayField,
          );
        });
      }
      // unselect fieldGroup if all fields are used
      if (getUnusedDisplayFieldsByGroupId(fieldGroup.id).length === 0) {
        state.report.fieldGroup = null;
      }
    }
  };

  const removeDisplayFieldGroup = (index: number) => {
    const fieldGroup = state.report.fieldGroupSelected[index];
    state.report.fieldGroupSelected.splice(index, 1);
    delete state.report.displayFieldSelected[fieldGroup.id];
  };

  const removeDisplayField = (item: Option, index: number) => {
    const fieldGroup = state.report.fieldGroupSelected[index];
    const fields = state.report.displayFieldSelected[fieldGroup.id].fields;
    state.report.displayFieldSelected[fieldGroup.id].fields = fields.filter(
      field => field.id !== item.id,
    );
    // remove field group if no fields
    if (state.report.displayFieldSelected[fieldGroup.id].fields.length === 0) {
      removeDisplayFieldGroup(index);
    }
  };

  const serializeBody = (reportModel: ReportModel) => {
    const payload: ReportRequestBody = {
      name: reportModel.name,
      include: reportModel.includeEmployees.key,
      criteria: {},
      fieldGroup: {},
    };
    reportModel.fieldGroupSelected.forEach(group => {
      const fields = reportModel.displayFieldSelected[group.id].fields;
      const includeHeader =
        reportModel.displayFieldSelected[group.id].includeHeader;
      payload.fieldGroup[group.id] = {
        fields: fields.map(field => field.id),
        includeHeader,
      };
    });
    reportModel.criteriaSelected.forEach(criterion => {
      const criteriaField = reportModel.criteriaFieldValues[criterion.id];
      payload.criteria[criterion.id] = {
        operator: criteriaField.operator ? criteriaField.operator.id : '',
        x:
          typeof criteriaField.valueX === 'object'
            ? String(criteriaField.valueX?.id)
            : criteriaField.valueX,
        y:
          typeof criteriaField.valueY === 'object'
            ? String(criteriaField.valueY?.id)
            : criteriaField.valueY,
      };
    });

    return payload;
  };

  const availableCriteria = computed(() => {
    return selectionCriteria.filter(
      criterion =>
        !state.report.criteriaSelected.find(c => c.id === criterion.id),
    );
  });

  const availableFieldGroups = computed(() => {
    return displayFieldGroups.filter(
      group => getUnusedDisplayFieldsByGroupId(group.id).length !== 0,
    );
  });

  const availableDisplyFields = computed(() => {
    const fieldGroupId = state.report.fieldGroup?.id;
    return fieldGroupId ? getUnusedDisplayFieldsByGroupId(fieldGroupId) : [];
  });

  return {
    ...toRefs(state),
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
}

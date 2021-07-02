<template>
  <edit-employee-layout :employee-id="empNumber" screen="report-to">
    <div class="orangehrm-horizontal-padding orangehrm-top-padding">
      <oxd-text tag="h6" class="orangehrm-main-title">Report to</oxd-text>
      <oxd-divider />
    </div>
    <employee-supervisors
      :employee-id="empNumber"
      :reporting-methods="reportingMethods"
    ></employee-supervisors>
    <employee-subordinates
      :employee-id="empNumber"
      :reporting-methods="reportingMethods"
    ></employee-subordinates>
  </edit-employee-layout>
</template>

<script>
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import EmployeeSupervisors from '@/orangehrmPimPlugin/components/EmployeeSupervisors';
import EmployeeSubordinates from '@/orangehrmPimPlugin/components/EmployeeSubordinates';

export default {
  components: {
    'edit-employee-layout': EditEmployeeLayout,
    'employee-supervisors': EmployeeSupervisors,
    'employee-subordinates': EmployeeSubordinates,
  },
  props: {
    empNumber: {
      type: String,
      required: true,
    },
    reportingMethods: {
      type: Array,
      required: true,
    },
  },
};
</script>

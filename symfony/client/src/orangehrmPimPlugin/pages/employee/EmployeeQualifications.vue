<template>
  <edit-employee-layout :employee-id="empNumber" screen="qualifications">
    <div class="orangehrm-horizontal-padding orangehrm-top-padding">
      <oxd-text tag="h6" class="orangehrm-main-title">Qualifications</oxd-text>
      <oxd-divider />
    </div>
    <employee-skills :employee-id="empNumber"></employee-skills>
  </edit-employee-layout>
</template>

<script>
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import EmployeeSkills from '@/orangehrmPimPlugin/components/EmployeeSkills';

export default {
  components: {
    'edit-employee-layout': EditEmployeeLayout,
    'employee-skills': EmployeeSkills,
  },
  props: {
    empNumber: {
      type: String,
      required: true,
    },
  },
};
</script>

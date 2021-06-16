<template>
  <edit-employee-layout :employee-id="empNumber" screen="qualifications">
    <div class="orangehrm-horizontal-padding orangehrm-top-padding">
      <oxd-text tag="h6" class="orangehrm-main-title">Qualifications</oxd-text>
      <oxd-divider />
    </div>
    <employee-skills :employee-id="empNumber"></employee-skills>
    <employee-education :employee-id="empNumber"></employee-education>
    <employee-languages
      :employee-id="empNumber"
      :fluencies="fluencies"
      :competencies="competencies"
    ></employee-languages>
    <employee-work-experience
      :employee-id="empNumber"
    ></employee-work-experience>
  </edit-employee-layout>
</template>

<script>
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import EmployeeSkills from '@/orangehrmPimPlugin/components/EmployeeSkills';
import EmployeeEducation from '@/orangehrmPimPlugin/components/EmployeeEducation';
import EmployeeLanguages from '@/orangehrmPimPlugin/components/EmployeeLanguages';
import EmployeeWorkExperience from '@/orangehrmPimPlugin/components/EmployeeWorkExperience';

export default {
  components: {
    'edit-employee-layout': EditEmployeeLayout,
    'employee-skills': EmployeeSkills,
    'employee-education': EmployeeEducation,
    'employee-languages': EmployeeLanguages,
    'employee-work-experience': EmployeeWorkExperience,
  },
  props: {
    empNumber: {
      type: String,
      required: true,
    },
    fluencies: {
      type: Array,
      required: true,
    },
    competencies: {
      type: Array,
      required: true,
    },
  },
};
</script>

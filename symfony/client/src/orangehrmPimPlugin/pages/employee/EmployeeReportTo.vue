<template>
  <edit-employee-layout :employee-id="empNumber" screen="qualifications">
    <div class="orangehrm-horizontal-padding orangehrm-top-padding">
      <oxd-text tag="h6" class="orangehrm-main-title">Report to</oxd-text>
      <oxd-divider />
    </div>
    <employee-supervisors :employee-id="empNumber"></employee-supervisors>
    <employee-subordinates :employee-id="empNumber"></employee-subordinates>
  </edit-employee-layout>
</template>

<script>
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import EmployeeSkills from '@/orangehrmPimPlugin/components/EmployeeSkills';
import EmployeeEducation from '@/orangehrmPimPlugin/components/EmployeeEducation';
import EmployeeLanguages from '@/orangehrmPimPlugin/components/EmployeeLanguages';
import EmployeeWorkExperience from '@/orangehrmPimPlugin/components/EmployeeWorkExperience';
import EmployeeLicense from '@/orangehrmPimPlugin/components/EmployeeLicense';
import EmployeeSupervisors from "@/orangehrmPimPlugin/components/EmployeeSupervisors";
import EmployeeSubordinates from "@/orangehrmPimPlugin/components/EmployeeSubordinates";

export default {
  components: {
    EmployeeSubordinates,
    EmployeeSupervisors,
    'edit-employee-layout': EditEmployeeLayout,
    'employee-supervisors': EmployeeSupervisors,
    'employee-subordinates': EmployeeSubordinates,
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

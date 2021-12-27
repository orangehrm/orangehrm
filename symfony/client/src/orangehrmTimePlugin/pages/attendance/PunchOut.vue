<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        Punch Out
      </oxd-text>

      <oxd-divider />

      <!-- wrapper -->
      <div class="orangehrm-paper-container">
        <punch-form v-bind="$attrs"></punch-form>
      </div>
    </div>
  </div>
</template>

<script>
import PunchForm from '@/orangehrmTimePlugin/components/PunchIn.vue';
export default {
  inheritAttrs: false,
  components: {
    'punch-form': PunchForm,
  },
};
</script>

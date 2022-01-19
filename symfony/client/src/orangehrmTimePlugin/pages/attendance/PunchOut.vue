<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('time.punch_out') }}
      </oxd-text>

      <oxd-divider />

      <!-- wrapper -->
      <div class="orangehrm-paper-container">
        <record-attendance v-bind="$attrs"></record-attendance>
      </div>
    </div>
  </div>
</template>

<script>
import RecordAttendance from '@/orangehrmTimePlugin/components/RecordAttendance.vue';
export default {
  components: {
    'record-attendance': RecordAttendance,
  },
  inheritAttrs: false,
};
</script>

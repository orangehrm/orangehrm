<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('time.punch_in') }}
      </oxd-text>

      <oxd-divider />

      <!-- wrapper -->
      <div class="orangehrm-paper-container">
        <punch-form></punch-form>
      </div>
    </div>
  </div>
</template>

<script>
import PunchForm from '@/orangehrmTimePlugin/components/PunchIn.vue';
export default {
  components: {
    'punch-form': PunchForm,
  },
};
</script>

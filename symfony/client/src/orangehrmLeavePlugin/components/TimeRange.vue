<template>
  <oxd-grid-item>
    <time-input
      :label="$t('general.from')"
      :modelValue="fromTime"
      :rules="rules.fromTime"
      @update:modelValue="$emit('update:fromTime', $event)"
      required
    />
  </oxd-grid-item>
  <oxd-grid-item>
    <time-input
      :label="$t('general.to')"
      :modelValue="toTime"
      :rules="rules.toTime"
      @update:modelValue="$emit('update:toTime', $event)"
      required
    />
  </oxd-grid-item>
</template>

<script>
export default {
  name: 'time-range',
  inheritAttrs: false,
  props: {
    fromTime: {
      type: String,
      required: false,
    },
    toTime: {
      type: String,
      required: false,
    },
    rules: {
      type: Object,
      required: true,
    },
    workShift: {
      type: Object,
      required: true,
    },
  },
  mounted() {
    if (this.workShift?.startTime) {
      this.$emit('update:fromTime', this.workShift.startTime);
    }
    if (this.workShift?.endTime) {
      this.$emit('update:toTime', this.workShift.endTime);
    }
  },
};
</script>

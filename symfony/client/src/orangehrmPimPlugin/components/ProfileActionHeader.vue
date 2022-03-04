<template>
  <div class="orangehrm-action-header">
    <oxd-text tag="h6" class="orangehrm-main-title">
      <slot></slot>
    </oxd-text>
    <oxd-button
      v-if="actionButtonShown"
      :label="$t('general.add')"
      icon-name="plus"
      display-type="text"
      v-bind="$attrs"
    />
  </div>
</template>

<script>
export default {
  name: 'ProfileActionHeader',
  inheritAttrs: false,
  props: {
    actionButtonShown: {
      type: Boolean,
      required: false,
      default: true,
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-action {
  &-header {
    display: flex;
    overflow-wrap: break-word;
    align-items: center;
    button {
      margin-left: 1rem;
      white-space: nowrap;
    }
  }
}
</style>

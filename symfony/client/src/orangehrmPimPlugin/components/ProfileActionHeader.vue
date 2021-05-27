<template>
  <div class="orangehrm-action-header">
    <oxd-text tag="h6">
      <slot></slot>
    </oxd-text>
    <oxd-button
      label="Add"
      iconName="plus"
      displayType="text"
      v-bind="$attrs"
    />
  </div>
</template>

<script>
export default {
  name: 'profile-action-header',
  inheritAttrs: false,
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

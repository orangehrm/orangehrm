<template>
  <teleport to="body">
    <oxd-toast-container
      v-model:toasts="toasts"
      position="bottom"
      class="orangehrm-toast-container"
    />
  </teleport>
</template>

<script>
import {defineComponent} from 'vue';
import ToastContainer from '@orangehrm/oxd/core/components/Toast/Container.vue';

export default defineComponent({
  data() {
    return {
      toasts: [],
    };
  },

  components: {
    'oxd-toast-container': ToastContainer,
  },

  methods: {
    push(toast) {
      if (toast.show === undefined) {
        toast.show = true;
      }
      this.toasts.push(toast);
    },
  },
});
</script>

<style lang="scss" scoped>
.orangehrm-toast-container {
  position: fixed;
  z-index: 999999;
  bottom: 12px;
  left: 12px;
  min-width: 400px;
}
</style>

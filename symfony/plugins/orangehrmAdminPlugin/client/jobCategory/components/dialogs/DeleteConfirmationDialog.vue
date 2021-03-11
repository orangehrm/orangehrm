<template>
  <oxd-dialog v-model:show="show" :style="{ maxWidth: '450px' }">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">Are you sure?</oxd-text>
    </div>
    <div class="orangehrm-text-center-align">
      <oxd-text type="subtitle-2">
        The selected item will be permanently deleted. Are you sure you want to
        continue?
      </oxd-text>
    </div>
    <div class="orangehrm-modal-footer">
      <oxd-button
        label="No, Cancel"
        type="text"
        class="orangehrm-button-margin"
        @click="onCancel"
      />
      <oxd-button
        label="Yes, Delete"
        iconName="trash"
        type="label-danger"
        class="orangehrm-button-margin"
        @click="onDelete"
      />
    </div>
  </oxd-dialog>
</template>

<script>
import Dialog from "@orangehrm/oxd/core/components/Dialog/Dialog";

export default {
  components: {
    "oxd-dialog": Dialog,
  },
  data() {
    return {
      show: false,
      reject: null,
      resolve: null,
    };
  },
  methods: {
    showDialog() {
      return new Promise((resolve, reject) => {
        this.resolve = resolve;
        this.reject = reject;
        this.show = true;
      });
    },
    onDelete() {
      this.show = false;
      this.resolve && this.resolve("ok");
    },
    onCancel() {
      this.show = false;
      this.resolve && this.resolve("cancel");
    },
  },
};
</script>

<style>
.orangehrm-modal-header {
  margin-bottom: 1.2rem;
  display: flex;
  justify-content: center;
}
.orangehrm-modal-footer {
  margin-top: 1.2rem;
  display: flex;
  justify-content: center;
}
.orangehrm-button-margin {
  margin: 0.25rem;
}
.orangehrm-text-center-align {
  text-align: center;
}
</style>

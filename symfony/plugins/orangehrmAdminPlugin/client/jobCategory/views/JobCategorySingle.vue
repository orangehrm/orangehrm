<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6">{{ title }}</oxd-text>

      <oxd-divider />

      <oxd-form novalidate="true">
        <oxd-form-row>
          <oxd-input-field label="Job Title" />
        </oxd-form-row>
      </oxd-form>

      <oxd-divider />

      <oxd-form-actions>
        <oxd-button type="ghost" label="Cancel" @click="onCancel" />
        <oxd-button
          class="orangehrm-left-space"
          type="secondary"
          label="Save"
          @click="onSave"
        />
      </oxd-form-actions>
    </div>
  </div>
</template>

<script>
// TODO: Component logic
export default {
  props: ["title", "jobCategory"],
  data() {
    return {};
  },
  methods: {
    onSave() {
      console.log("do save");
    },
    onCancel() {
      console.log("go back");
    },
  },
};
</script>

<style scoped>
.orangehrm-left-space {
  margin-left: 10px;
}

.orangehrm-card-container {
  background-color: white;
  border-radius: 1.2rem;
  padding: 1.2rem;
}

.orangehrm-background-container {
  background-color: #f6f5fb;
  padding: 2rem;
  flex: 1;
}
</style>
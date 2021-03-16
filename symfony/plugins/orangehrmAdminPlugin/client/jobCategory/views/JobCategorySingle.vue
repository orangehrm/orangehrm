<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6">{{ title }}</oxd-text>

      <oxd-divider />

      <oxd-form @submitValid="onSave">
        <oxd-form-row>
          <oxd-input-field
            label="Job Category Name"
            v-model="category.name"
            :rules="rules.name"
          />
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            type="button"
            displayType="ghost"
            label="Cancel"
            @click="onCancel"
          />
          <oxd-button
            class="orangehrm-left-space"
            displayType="secondary"
            :label="action"
            type="submit"
          />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    action: String,
    jobCategory: Object,
  },
  data() {
    return {
      category: {
        id: "",
        name: "",
      },
      rules: {
        name: [],
      },
      errors: [],
    };
  },
  methods: {
    onSave() {
      // TODO: Loading
      if (this.action == "Update") {
        this.$http
          .put(`api/v1/admin/job-categories/${this.category.id}`, {
            name: this.category.name,
          })
          .then(() => {
            // go back
            this.onCancel();
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        this.$http
          .post(`api/v1/admin/job-categories`, {
            name: this.category.name,
          })
          .then(() => {
            // go back
            this.onCancel();
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    onCancel() {
      this.$emit("onCancel", { viewId: 1, payload: null });
    },
  },
  created() {
    if (this.jobCategory) {
      this.category.id = this.jobCategory.id ? this.jobCategory.id : "";
      this.category.name = this.jobCategory.name ? this.jobCategory.name : "";
    }

    // Fetch list data for unique test
    this.$http.get(`api/v1/admin/job-categories`).then((response) => {
      const { data } = response.data;
      this.rules.name.push((v) => {
        return (!!v && v.trim() !== "") || "Required";
      });
      this.rules.name.push((v) => {
        return (v && v.length <= 100) || "Should be less than 50 characters";
      });
      this.rules.name.push((v) => {
        const index = data.findIndex((item) => item.name == v);
        if (index > -1) {
          const { id } = data[index];
          return id != this.category.id
            ? "Job category name should be unique"
            : true;
        } else {
          return true;
        }
      });
    });
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-left-space {
  margin-left: 10px;
}

.orangehrm-card-container {
  background-color: $oxd-white-color;
  border-radius: $oxd-border-radius;
  padding: 1.2rem;
}

.orangehrm-background-container {
  background-color: $oxd-background-pastel-white-color;
  padding: 2rem;
  flex: 1;
}
</style>

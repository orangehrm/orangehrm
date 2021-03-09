<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6">Add Job Title</oxd-text>

      <oxd-divider />

      <oxd-form novalidate="true">
        <oxd-form-row>
          <oxd-input-field
            label="Job Title"
            v-model="jobTitle.title"
            :rules="rules.title"
            @errors="onError"
          />
        </oxd-form-row>

        <oxd-form-row>
          <oxd-input-field
            type="textarea"
            label="Job Description"
            placeholder="Type description here"
            v-model="jobTitle.description"
            :rules="rules.description"
            @errors="onError"
          />
        </oxd-form-row>

        <oxd-form-row>
          <oxd-input-field
            type="file"
            label="Job Specification"
            buttonLabel="Browse"
            v-model="jobTitle.specification"
            :rules="rules.specification"
            @errors="onError"
          />
        </oxd-form-row>
      </oxd-form>

      <oxd-form-row>
        <oxd-input-field
          type="textarea"
          label="Note"
          placeholder="Add note"
          v-model="jobTitle.note"
          :rules="rules.note"
          @errors="onError"
        />
      </oxd-form-row>

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
import Form from "@orangehrm/oxd/core/components/Form/Form";
import FormRow from "@orangehrm/oxd/core/components/Form/FormRow";
import FormActions from "@orangehrm/oxd/core/components/Form/FormActions";
import Divider from "@orangehrm/oxd/core/components/Divider/Divider";
import Button from "@orangehrm/oxd/core/components/Button/Button";
import Text from "@orangehrm/oxd/core/components/Text/Text";
import InputField from "@orangehrm/oxd/core/components/InputField/InputField";

const initialJobTitle = {
  title: "",
  description: "",
  specification: null,
  note: ""
};

export default {
  data() {
    return {
      jobTitle: { ...initialJobTitle },
      rules: {
        title: [
          v => (!!v && v.trim() !== "") || "Required",
          v => (v && v.length <= 100) || "Should be less than 100 characters"
        ],
        description: [
          v =>
            (v && v.length <= 400) ||
            v === "" ||
            "Should be less than 400 characters"
        ],
        specification: [
          v =>
            v === null ||
            (v && v.size && v.size <= 1024 * 1024) ||
            "Attachment size exceeded"
        ],
        note: [
          v =>
            (v && v.length <= 400) ||
            v === "" ||
            "Should be less than 400 characters"
        ]
      },
      errors: []
    };
  },

  components: {
    "oxd-form": Form,
    "oxd-form-row": FormRow,
    "oxd-form-actions": FormActions,
    "oxd-divider": Divider,
    "oxd-button": Button,
    "oxd-text": Text,
    "oxd-input-field": InputField
  },

  methods: {
    onCancel() {
      window.location.reload();
    },
    onError() {},
    onSave() {
      const headers = new Headers();
      headers.append("Content-Type", "application/json");
      headers.append("Accept", "application/json");

      fetch(`${this.global.baseUrl}/api/v1/admin/job-titles`, {
        method: "POST",
        headers: headers,
        body: JSON.stringify(this.jobTitle)
      }).then(async res => {
        if (res.status === 200) {
          this.jobTitle = { ...initialJobTitle };
          window.location.reload();
        } else {
          console.error(res);
        }
      });
    }
  }
};
</script>

<style lang="scss">
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

body {
  margin: 0;
  background-color: #f6f5fb;
}

#app {
  display: flex;
}
</style>

<template>
  <oxd-text tag="h5">Add Job Title</oxd-text>

  <oxd-divider />

  <oxd-form novalidate="true">
    <oxd-form-row>
      <oxd-input-field
        label="Job Title"
        v-model="jobTitle.title"
        :rules="rules.title"
      />
    </oxd-form-row>

    <oxd-form-row>
      <oxd-input-field
        type="textarea"
        label="Job Description"
        placeholder="Type description here"
        v-model="jobTitle.description"
        :rules="rules.description"
      />
    </oxd-form-row>

    <oxd-form-row>
      <oxd-input-field
        type="file"
        label="Job Specification"
        buttonLabel="Browse"
        v-model="jobTitle.specification"
        :rules="rules.specification"
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
    />
  </oxd-form-row>

  <oxd-divider />

  <oxd-form-actions>
    <oxd-button type="ghost" label="Cancel" @click="onCancel" />
    <oxd-button
      class="orangehrm-left-space"
      type="secondary"
      label="Add"
      @click="onSave"
    />
  </oxd-form-actions>
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
      }
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
    // eslint-disable-next-line @typescript-eslint/no-empty-function
    onCancel() {},
    onSave() {
      const headers = new Headers();
      headers.append("Content-Type", "application/json");
      headers.append("Accept", "application/json");

      fetch("<base_url>/symfony/web/index-new.php/api/v1/admin/job-title", {
        method: "POST",
        headers: headers,
        body: JSON.stringify(this.jobTitle)
      }).then(async res => {
        if (res.status === 200) {
          this.jobTitle = { ...initialJobTitle };
          // eslint-disable-next-line no-empty
        } else {
        }
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.orangehrm-left-space {
  margin-left: 10px;
}
</style>

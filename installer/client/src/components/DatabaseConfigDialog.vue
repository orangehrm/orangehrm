<template>
  <oxd-dialog
    class="orangehrm-consent-dialog"
    :style="{width: '90%', maxWidth: '600px'}"
    @update:show="cancelInstallation"
  >
    <oxd-form class="orangehrm-database-config-dialog" @submit="submitInfo">
      <oxd-text tag="h6" class="orangehrm-database-config-dialog-title">
        Warning
      </oxd-text>
      <oxd-divider class="orangehrm-divider" />
      <oxd-text
        tag="p"
        class="orangehrm-database-config-dialog-content orangehrm-database-config--title"
      >
        Following modules or add-ons are not supported by OrangeHRM Starter
        version 5.0. You may continue to upgrade your system to version 5.0, but
        please note that any data used in these modules will be inaccessible.
      </oxd-text>
      <oxd-classic-table
        :headers="headers"
        :items="items"
        class="orangehrm-database-config-dialog-table"
      ></oxd-classic-table>
      <oxd-check-box
        v-model="checked"
        option-label="I want to continue upgrading the OrangeHRM system to version 5.0 and I am aware that by doing so, any data gathered in incompatible modules/add-ons will be inaccessible."
      ></oxd-check-box>
      <oxd-divider class="orangehrm-divider" />
      <oxd-form-actions class="orangehrm-database-config-dialog-action">
        <oxd-button
          display-type="ghost"
          label="Cancel"
          type="button"
          @click="cancelInstallation"
        />
        <oxd-button
          class="orangehrm-left-space"
          display-type="secondary"
          label="Continue"
          :disabled="!checked"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import DialogWithClose from '@ohrm/oxd/core/components/Dialog/Dialog.vue';
import ClassicTableStory from '@ohrm/oxd/core/components/Table/ClassicTable.vue';
import CheckBoxInput from '@ohrm/oxd/core/components/Input/CheckboxInput.vue';

export default {
  name: 'DatabaseConfigDialog',
  components: {
    'oxd-dialog': DialogWithClose,
    'oxd-check-box': CheckBoxInput,
    'oxd-classic-table': ClassicTableStory,
  },
  emits: ['closeModel'],
  data() {
    return {
      checked: false,
      headers: [
        {title: 'Modules', name: 'module'},
        {title: 'Add-ons', name: 'addon'},
        {title: 'Other', name: 'other'},
      ],
      items: [
        {
          module: '- Recruitment Module',
          addon: '- Corporate Branding',
          other: '- Dashboard',
        },
        {
          module: '- Performance Module',
          addon: '- LDAP',
          other: '- Marketplace',
        },
        {
          module: '- Buzz',
          addon: '- Claim',
          other: '- Custom Language Packages',
        },
        {
          module: '- Directory',
          addon: '- Toggl',
          other: '- Custom Date Formats',
        },
        {
          module: '',
          addon: '',
          other: '- Encrypted Data',
        },
        {
          module: '',
          addon: '',
          other: '- Mobile App',
        },
      ],
    };
  },
  methods: {
    submitInfo() {
      this.$emit('closeModel', this.checked);
    },
    cancelInstallation() {
      this.$emit('closeModel', false);
    },
  },
};
</script>

<style scoped lang="scss">
::v-deep(.oxd-table) {
  th,
  td {
    padding: 0.1rem 0;
  }
  tr,
  tr:hover {
    border: unset;
    cursor: unset;
    color: unset;
    background-color: unset;
  }
}
::v-deep(.oxd-checkbox-wrapper) {
  span {
    flex-shrink: 0;
  }
  label {
    font-weight: bold;
  }
}
.orangehrm-database-config-dialog {
  font-size: $oxd-input-control-font-size;
  &-title {
    font-weight: 700;
  }
  &-content {
    padding-bottom: 1rem;
  }
  &-table {
    margin-bottom: 2em;
  }
}
</style>

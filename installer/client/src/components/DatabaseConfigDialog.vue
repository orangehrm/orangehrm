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
        The following features and add-ons are not supported in OrangeHRM
        Starter version 5.6.1. You may continue to upgrade your system to
        version 5.6.1, but please note that any data used in these features will
        be inaccessible.
      </oxd-text>
      <oxd-classic-table
        :headers="headers"
        :items="items"
        class="orangehrm-database-config-dialog-table"
      ></oxd-classic-table>
      <oxd-check-box
        v-model="checked"
        option-label="I want to continue upgrading the OrangeHRM system to version 5.6.1 and I am aware that by doing so, any gathered data in incomplete features/add-ons will be inaccessible."
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
import {OxdCheckboxInput, OxdClassicTable, OxdDialog} from '@ohrm/oxd';

export default {
  name: 'DatabaseConfigDialog',
  components: {
    'oxd-dialog': OxdDialog,
    'oxd-check-box': OxdCheckboxInput,
    'oxd-classic-table': OxdClassicTable,
  },
  emits: ['closeModel'],
  data() {
    return {
      checked: false,
      headers: [
        {title: 'Add-ons', name: 'addon'},
        {title: 'Other', name: 'other'},
      ],
      items: [
        {
          addon: '- Toggl (Discontinued)',
          other: '- Marketplace (Discontinued)',
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
    display: table;
    table-layout: fixed;
  }
}
</style>

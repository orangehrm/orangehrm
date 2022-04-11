<template>
  <oxd-dialog>
    <oxd-form class="orangehrm-database-config-dialog" @submit="submitInfo">
      <oxd-text tag="h6" class="orangehrm-database-config-dialog-title">
        Warning
      </oxd-text>
      <oxd-divider class="orangehrm-database-config-dialog-divider" />
      <oxd-text
        tag="p"
        class="orangehrm-system-check-content orangehrm-system-check-title"
      >
        It appears that you are currently using modules or add-on which are not
        supported by OrangeHRM Starter version 5.0. you may continue to upgrade
        your system to version 5.0, but please keep in mind that any data used
        in these modules will be in accessible.
      </oxd-text>
      <oxd-classic-table :headers="headers" :items="items"></oxd-classic-table>
      <oxd-check-box
        v-model="selected"
        option-label="I want to continue upgrading the orangeHRM system to version 5.0 and i'm aware that any data gathered in incompatible modules/add-ons will be inaccessible"
      ></oxd-check-box>
      <oxd-divider class="orangehrm-database-config-dialog-divider" />
      <oxd-form-actions class="orangehrm-database-config-dialog-action">
        <required-text />
        <oxd-button
          class="orangehrm-database-config-dialog-button"
          display-type="ghost"
          label="Cancel"
          type="button"
          @click="cancelInstallation"
        />
        <oxd-button
          class="orangehrm-database-config-dialog-button"
          display-type="secondary"
          label="Continue"
          :disabled="!selected"
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
    'oxd-classic-table': ClassicTableStory,
    'oxd-check-box': CheckBoxInput,
  },
  emits: ['closeModel'],
  data() {
    return {
      selected: false,
      headers: [
        {title: 'Modules', name: 'module'},
        {title: 'Add-ons', name: 'addon'},
        {title: 'Other', name: 'other'},
      ],
      items: [
        {
          module: '-Recuriment Module',
          addon: '-Corporate Branding',
          other: '-Dashboard',
        },
        {
          module: '-Performance Module',
          addon: '-LDAP',
          other: '-MarketPlace',
        },
        {
          module: '-Buzz',
          addon: '-Claim',
          other: 'Custom Language packages',
        },
        {
          module: '-Directory',
          addon: '-toggl',
          other: '',
        },
      ],
    };
  },
  methods: {
    submitInfo() {
      this.$emit('closeModel', this.selected);
    },
    cancelInstallation() {
      this.$emit('closeModel', false);
    },
  },
};
</script>

<style scoped lang="scss">
::v-deep(.oxd-dialog-sheet) {
  width: 100%;
}
.orangehrm-database-config-dialog {
  ::v-deep(.oxd-table-row) {
    border-top: 0px;
  }
  ::v-deep(.oxd-table-row:hover) {
    background-color: transparent;
    cursor: inherit;
  }
  ::v-deep(.oxd-padding-cell:hover) {
  }
  &-divider {
    border-top-color: $oxd-interface-gray-darken-1-color;
  }
  &-button {
    margin-left: 0.5rem;
  }
}
</style>

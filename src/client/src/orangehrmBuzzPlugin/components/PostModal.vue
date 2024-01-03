<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
 -->

<template>
  <oxd-dialog
    class="orangehrm-dialog-modal"
    :persistent="true"
    @update:show="onCancel"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ title }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="loading" @submit-valid="onSubmit">
      <div class="orangehrm-buzz-post-modal-header">
        <profile-image :employee="employee"></profile-image>
        <div class="orangehrm-buzz-post-modal-header-text">
          <slot name="header"></slot>
        </div>
      </div>
      <slot></slot>
      <oxd-form-actions class="orangehrm-buzz-post-modal-actions">
        <oxd-button
          type="submit"
          :disabled="disabled"
          :label="actionLabel || $t('buzz.share')"
        />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import ProfileImage from '@/orangehrmBuzzPlugin/components/ProfileImage';
import {OxdDialog} from '@ohrm/oxd';

export default {
  name: 'PostModal',

  components: {
    'oxd-dialog': OxdDialog,
    'profile-image': ProfileImage,
  },

  props: {
    title: {
      type: String,
      required: true,
    },
    employee: {
      type: Object,
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    actionLabel: {
      type: String,
      default: null,
      required: false,
    },
  },

  emits: ['close', 'submit'],

  setup(_, context) {
    const onSubmit = () => {
      context.emit('submit');
    };

    const onCancel = () => {
      context.emit('close');
    };

    return {
      onSubmit,
      onCancel,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-post-modal {
  &-header {
    gap: 1rem;
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
    &-text {
      width: 100%;
    }
  }
  &-actions {
    display: flex;
    margin-top: 1rem;
    justify-content: center;
    ::v-deep(.oxd-button) {
      width: 90%;
    }
  }
}
.orangehrm-modal-header {
  text-align: center;
}
</style>

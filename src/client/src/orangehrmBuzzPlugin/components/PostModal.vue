<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
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
    <oxd-form :loading="loading" @submitValid="onSubmit">
      <div class="orangehrm-buzz-post-modal-header">
        <div class="orangehrm-buzz-post-modal-profile-image">
          <img
            alt="profile picture"
            class="employee-image"
            :src="`../pim/viewPhoto/empNumber/${employee.empNumber}`"
          />
        </div>
        <div class="orangehrm-buzz-post-modal-header-text">
          <slot name="header"></slot>
        </div>
      </div>
      <slot></slot>
      <oxd-form-actions class="orangehrm-buzz-post-modal-actions">
        <oxd-button type="submit" :label="$t('buzz.share')" />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import Dialog from '@ohrm/oxd/core/components/Dialog/Dialog';

export default {
  name: 'PostModal',

  components: {
    'oxd-dialog': Dialog,
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
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
    &-text {
      width: 100%;
    }
  }
  &-profile-image {
    & img {
      width: 45px;
      height: 45px;
      display: flex;
      flex-shrink: 0;
      border-radius: 100%;
      justify-content: center;
      box-sizing: border-box;
      margin-right: 0.5rem;
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

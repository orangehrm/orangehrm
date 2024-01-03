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
  <oxd-text
    :class="{
      'orangehrm-card-note': true,
      'orangehrm-card-note--background': hasNote,
      'orangehrm-card-note--icon': hasNoteIcon,
    }"
  >
    <oxd-text v-if="hasNote" class="orangehrm-sub-title">
      {{ $t('general.note') }}:
    </oxd-text>
    <oxd-text v-if="hasNoteIcon" tag="span" class="orangehrm-card-note-icon">
      <oxd-icon name="exclamation-triangle" class="orangehrm-card-icon" />
    </oxd-text>
    <oxd-text :class="{'orangehrm-card-note-text': hasNoteIcon}">
      {{ noteText }}
    </oxd-text>
  </oxd-text>
</template>

<script>
import {OxdIcon} from '@ohrm/oxd';

export default {
  name: 'CardNote',
  components: {
    'oxd-icon': OxdIcon,
  },
  props: {
    noteText: {
      type: String,
      required: true,
    },
    hasNote: {
      type: Boolean,
      default: false,
    },
    hasNoteIcon: {
      type: Boolean,
      required: false,
    },
  },
};
</script>

<style scoped lang="scss">
.orangehrm-card {
  &-icon {
    font-size: 1.5rem;
    color: $oxd-feedback-danger-color;
    padding: 1rem;
  }

  &-note {
    font-size: $oxd-input-control-font-size;

    &-text {
      color: $oxd-feedback-danger-color;
    }
    &-icon {
      flex-shrink: 0;
    }
  }

  &-note--background {
    background-color: $oxd-interface-gray-lighten-2-color;
    border-radius: $oxd-border-radius;
    padding: 1.2rem;
  }

  &-note--icon {
    display: flex;
    align-items: center;
    background-color: rgba($oxd-feedback-danger-color, 0.06);
    border-radius: $oxd-border-radius;
    padding: 1rem;
  }
}
</style>

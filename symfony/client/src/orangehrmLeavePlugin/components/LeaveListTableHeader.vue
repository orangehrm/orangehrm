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
  <div class="orangehrm-header-container">
    <div class="actions" v-if="bulkActions && selected > 0">
      <oxd-text tag="span">
        {{ itemSelectedText }}
      </oxd-text>
      <oxd-button
        v-if="bulkActions.APPROVE"
        label="Approve"
        displayType="label-success"
        @click="$emit('onActionClick', 'APPROVE')"
      />
      <oxd-button
        v-if="bulkActions.REJECT"
        label="Reject"
        displayType="label-danger"
        @click="$emit('onActionClick', 'REJECT')"
      />
      <oxd-button
        v-if="bulkActions.CANCEL"
        label="Cancel"
        displayType="label-warn"
        @click="$emit('onActionClick', 'CANCEL')"
      />
    </div>
    <oxd-text tag="span" v-else>{{ itemCountText }}</oxd-text>
  </div>
</template>

<script>
import {computed} from 'vue';

export default {
  name: 'leave-list-table-header',

  emits: ['onActionClick'],

  props: {
    loading: {
      type: Boolean,
      required: true,
    },
    selected: {
      type: Number,
      required: true,
    },
    total: {
      type: Number,
      required: true,
    },
    bulkActions: {
      type: Object,
      required: false,
    },
  },

  setup(props) {
    const getNoun = count => {
      if (!count) return `No Records`;
      return count === 1 ? `(${count}) Record` : `(${count}) Records`;
    };

    const itemCountText = computed(() => {
      return `${getNoun(props.total)} Found`;
    });
    const itemSelectedText = computed(() => {
      return `${getNoun(props.selected)} Selected`;
    });

    return {
      itemCountText,
      itemSelectedText,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-header-container {
  .actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
  }
  span {
    margin-right: 20px;
  }
}
</style>

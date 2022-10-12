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
  <!-- TODO: Placeholder component -->
  <oxd-sheet class="orangehrm-buzz-post">
    <div class="orangehrm-buzz-post-profile">
      <img
        alt="profile picture"
        class="employee-image"
        :src="`../pim/viewPhoto/empNumber/${employee.empNumber}`"
      />
      <oxd-text type="card-title">
        {{ employeeFullName }}
      </oxd-text>
    </div>
    <oxd-divider></oxd-divider>
    <oxd-text type="card-title">{{ content }}</oxd-text>
  </oxd-sheet>
</template>

<script>
import {computed} from 'vue';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'BasePost',

  components: {
    'oxd-sheet': Sheet,
  },

  props: {
    postId: {
      type: Number,
      required: true,
    },
    content: {
      type: String,
      required: true,
    },
    employee: {
      type: Object,
      required: true,
    },
  },

  setup(props) {
    const {$tEmpName} = useEmployeeNameTranslate();

    const employeeFullName = computed(() => {
      return $tEmpName(props.employee, {
        includeMiddle: true,
        excludePastEmpTag: false,
      });
    });

    return {
      employeeFullName,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-post {
  height: 150px;
  margin-bottom: 1rem;
  &-profile {
    display: flex;
    align-items: center;
    & img {
      width: 40px;
      height: 40px;
      display: flex;
      flex-shrink: 0;
      margin-right: 1rem;
      border-radius: 100%;
      justify-content: center;
      box-sizing: border-box;
    }
  }
}
</style>

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
  <oxd-sheet type="gray-lighten-2">
    <oxd-text tag="p" class="notice-title">{{ title }}</oxd-text>
    <div>
      <slot></slot>
    </div>
  </oxd-sheet>
</template>

<script>
import {OxdSheet, OxdText} from '@ohrm/oxd';

export default {
  components: {
    'oxd-sheet': OxdSheet,
    'oxd-text': OxdText,
  },
  props: {
    title: {
      type: String,
      required: true,
    },
  },
};
</script>

<style lang="scss" scoped>
.notice-title {
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 0.5rem;
  text-transform: uppercase;
}
</style>

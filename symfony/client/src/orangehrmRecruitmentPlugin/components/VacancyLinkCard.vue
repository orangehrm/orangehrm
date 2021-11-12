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
  <oxd-grid :cols="1" class="orangehrm-full-width-grid">
    <oxd-grid-item class="orangehrm-label-link-wrapper">
      <oxd-text class="orangehrm-text orangehrm-text-label" tag="label"
        >{{ label }} :
      </oxd-text>
      <a
        :href="url"
        target="_blank"
        class="orangehrm-text orangehrm-text--colored orangehrm-href"
        >{{ url }}
      </a>
    </oxd-grid-item>
  </oxd-grid>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      required: true,
    },
    url: {
      type: String,
      required: true,
    },
  },
};
</script>

<style src="../pages/vacancy.scss" lang="scss" scoped></style>

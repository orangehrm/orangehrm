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
  <div class="orangehrm-label-link-wrapper">
    <oxd-text class="orangehrm-text-label" tag="p">{{ label }} : </oxd-text>
    <a :href="url" target="_blank" class="orangehrm-vacancy-link">
      {{ url }}
    </a>
  </div>
</template>

<script>
export default {
  name: 'VacancyLinkCard',
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

<style lang="scss" scoped>
.orangehrm-label-link-wrapper {
  display: flex;
  flex-wrap: wrap;
  &:first-of-type {
    margin-bottom: 0.25rem;
  }
}
.orangehrm-text-label {
  font-size: 12px;
  font-weight: 600;
  padding-right: 0.2rem;
  color: $oxd-interface-gray-darken-1-color;
}
.orangehrm-vacancy-link {
  font-size: 12px;
  font-weight: 600;
  margin-left: 1rem;
  text-decoration: none;
  word-break: break-all;
  font-family: $oxd-font-family;
  color: $oxd-primary-one-color;
}
</style>

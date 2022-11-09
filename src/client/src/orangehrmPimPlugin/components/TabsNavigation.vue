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
  <div role="tablist" class="orangehrm-tabs">
    <div
      v-for="tab in tabs"
      :key="tab"
      role="tab"
      class="orangehrm-tabs-wrapper"
    >
      <a
        :class="{'orangehrm-tabs-item': true, '--active': tab.active}"
        :href="tab.url"
        >{{ tab.name }}</a
      >
    </div>
  </div>
</template>

<script>
export default {
  name: 'TabsNavigation',
  props: {
    tabs: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-tabs {
  display: flex;
  flex-wrap: nowrap;
  &-wrapper {
    display: flex;
  }
  &-item {
    height: 35px;
    line-height: 35px;
    padding: 0rem 1rem;
    border-radius: 0.85rem;
    flex: 100%;
    color: $oxd-interface-gray-darken-1-color;
    text-decoration: none;
    font-size: 14px;
    font-family: $oxd-font-family;
    font-weight: 700;
    @include oxd-respond-to('xs') {
      margin-right: 0.25rem;
      background-color: $oxd-background-pastel-white-color;
      white-space: nowrap;
      &.--active,
      &:hover {
        background-color: $oxd-primary-one-color;
        color: $oxd-white-color;
      }
    }
    @include oxd-respond-to('md') {
      margin-right: unset;
      margin-bottom: 0.25rem;
      background-color: unset;
      white-space: wrap;
      &.--active,
      &:hover {
        background-color: $oxd-background-pastel-white-color;
        color: $oxd-interface-gray-darken-1-color;
      }
    }
  }
  @include oxd-respond-to('xs') {
    flex-direction: row;
    overflow: scroll hidden;
    padding-bottom: 5px;
  }
  @include oxd-respond-to('md') {
    flex-direction: column;
    overflow: visible;
    padding-bottom: unset;
  }
  @include oxd-scrollbar();
}
</style>

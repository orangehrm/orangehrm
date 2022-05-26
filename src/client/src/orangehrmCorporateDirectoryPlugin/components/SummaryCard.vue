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
  <oxd-sheet :gutters="false" class="orangehrm-scroll-card" type="white">
    <div class="orangehrm-scroll-card-header">
      <oxd-text type="card-title">
        {{ employeeName }}
      </oxd-text>
    </div>
    <div class="image-wrapper">
      <img
        alt="Profile Picture"
        class="employee-image"
        src="https://media.istockphoto.com/photos/mountain-landscape-picture-id517188688"
      />
    </div>
    <div class="orangehrm-scroll-card-header">
      <oxd-text type="toast-title">
        {{ employeeDesignation }}
      </oxd-text>
    </div>
    <div class="corporate-employee-title">
      <span class="corporate-loc-group-icon">
        <oxd-icon name="geo-alt-fill"></oxd-icon>
      </span>
      <span>
        <div class="corporate-unit">
          <oxd-text type="toast-message">
            {{ employeeSubUnit }}
          </oxd-text>
        </div>
        <div class="corporate-loc">
          <oxd-text type="toast-message">
            {{ employeeLocation }}
          </oxd-text>
        </div>
      </span>
    </div>
  </oxd-sheet>
</template>

<script>
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import Icon from '@ohrm/oxd/core/components/Icon/Icon';

export default {
  name: 'SummaryCard',
  components: {
    'oxd-sheet': Sheet,
    'oxd-icon': Icon,
  },
  props: {
    employeeName: {
      type: String,
      required: true,
    },
    employeeDesignation: {
      type: String,
      required: true,
    },
    employeeSubUnit: {
      type: String,
      required: true,
    },
    employeeLocation: {
      type: String,
      required: true,
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-scroll-card {
  padding: 0.5rem 1rem;
  margin-bottom: 0.5rem;

  &-header {
    padding-top: 1rem;
    padding-bottom: 1rem;
    text-align: center;
    justify-content: space-between;
  }

  .employee-image {
    height: 120px;
    width: 120px;
  }

  .image-wrapper {
    height: 90px;
    width: 90px;
    overflow: hidden;
    border-radius: 50%;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }

  .corporate-employee-title {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-align: center;
    align-items: center;
    padding: 0.25rem 0.5rem;
    margin-top: 0.25rem;
    background-color: #fafafc;
    border-radius: 0.5rem;
  }

  .corporate-loc-group-icon {
    margin-right: 0.5rem;
    color: #64728c;
    font-size: 24px;
  }

  .corporate-unit {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
  }

  .corporate-loc {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
  }
}
</style>

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
<script>
import {h} from 'vue';
import {OxdText} from '@ohrm/oxd';

export default {
  name: 'AuthButton',
  props: {
    url: {
      type: String,
      required: true,
    },
    color: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    return () =>
      h(
        'a',
        {
          href: props.url,
          title: props.label,
          class: 'orangehrm-auth-button',
          style: {
            backgroundColor: props.color,
          },
        },
        h(
          OxdText,
          {
            tag: 'p',
            class: 'orangehrm-auth-button-text',
          },
          () => props.label.charAt(0),
        ),
      );
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-auth-button {
  width: 28px;
  height: 28px;
  display: flex;
  flex-shrink: 0;
  border-radius: 100%;
  align-items: center;
  text-decoration: none;
  justify-content: center;
  &-text {
    line-height: 1;
    font-size: 14px;
    font-weight: 800;
    color: $oxd-white-color;
  }
}
</style>

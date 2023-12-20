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
<script>
import {h} from 'vue';
import {OxdText} from '@ohrm/oxd';

export default {
  name: 'AuthButton',
  props: {
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
          title: props.label,
          class: 'orangehrm-auth-button',
        },
        h(
          OxdText,
          {
            tag: 'p',
            class: 'orangehrm-auth-button-text',
          },
          () => props.label,
        ),
      );
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-auth-button {
  width: 14rem;
  height: 2.5rem;
  display: flex;
  flex-shrink: 0;
  border: 1px solid;
  border-radius: 1.5rem;
  border-color: $oxd-primary-one-color;
  align-items: center;
  text-decoration: none;
  justify-content: center;
  cursor: pointer;
  margin-bottom: 0.5rem;
  &-text {
    @include truncate(1, 1, #fff);
    width: 50%;
    line-height: 1;
    font-size: 14px;
    font-weight: 800;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    color: $oxd-primary-one-color;
  }
}
</style>

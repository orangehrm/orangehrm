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

import {App} from 'vue';
import {
  OxdForm,
  OxdFormRow,
  OxdFormActions,
  OxdInputField,
  OxdInputGroup,
  OxdGrid,
  OxdGridItem,
  OxdText,
  OxdButton,
  OxdDivider,
} from '@ohrm/oxd';
import InstallerLayout from '@/components/InstallerLayout.vue';
import RequiredText from '@/components/RequiredText.vue';

export default {
  install: (app: App) => {
    app.component('RequiredText', RequiredText);
    app.component('OxdDivider', OxdDivider);
    app.component('OxdForm', OxdForm);
    app.component('OxdFormRow', OxdFormRow);
    app.component('OxdFormActions', OxdFormActions);
    app.component('OxdInputField', OxdInputField);
    app.component('OxdInputGroup', OxdInputGroup);
    app.component('OxdGrid', OxdGrid);
    app.component('OxdGridItem', OxdGridItem);
    app.component('InstallerLayout', InstallerLayout);
    app.component('OxdText', OxdText);
    app.component('OxdButton', OxdButton);
  },
};

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
import SubmitButton from '@ohrm/components/buttons/SubmitButton.vue';
import TableHeader from '@ohrm/components/table/TableHeader.vue';
import RequiredText from '@ohrm/components/labels/RequiredText.vue';
import Layout from '@ohrm/components/layout/Layout.vue';
import DateInput from '@ohrm/components/inputs/DateInput.vue';
import TimeInput from '@ohrm/components/inputs/TimeInput.vue';

import {
  OxdCardTable,
  OxdButton,
  OxdPagination,
  OxdDivider,
  OxdText,
  OxdIconButton,
  OxdForm,
  OxdFormRow,
  OxdFormActions,
  OxdInputField,
  OxdInputGroup,
  OxdGrid,
  OxdGridItem,
  OxdTableFilter,
} from '@ohrm/oxd';

export default {
  install: (app: App) => {
    app.component('OxdLayout', Layout);
    app.component('OxdCardTable', OxdCardTable);
    app.component('OxdButton', OxdButton);
    app.component('OxdPagination', OxdPagination);
    app.component('OxdDivider', OxdDivider);
    app.component('OxdText', OxdText);
    app.component('OxdIconButton', OxdIconButton);
    app.component('OxdForm', OxdForm);
    app.component('OxdFormRow', OxdFormRow);
    app.component('OxdFormActions', OxdFormActions);
    app.component('OxdInputField', OxdInputField);
    app.component('OxdInputGroup', OxdInputGroup);
    app.component('OxdGrid', OxdGrid);
    app.component('OxdGridItem', OxdGridItem);
    app.component('OxdTableFilter', OxdTableFilter);
    app.component('SubmitButton', SubmitButton);
    app.component('TableHeader', TableHeader);
    app.component('RequiredText', RequiredText);
    app.component('DateInput', DateInput);
    app.component('TimeInput', TimeInput);
  },
};

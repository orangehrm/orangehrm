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

import {App} from 'vue';
import CardTable from '@ohrm/oxd/core/components/CardTable/CardTable.vue';
import Button from '@ohrm/oxd/core/components/Button/Button.vue';
import IconButton from '@ohrm/oxd/core/components/Button/Icon.vue';
import Pagination from '@ohrm/oxd/core/components/Pagination/Pagination.vue';
import Divider from '@ohrm/oxd/core/components/Divider/Divider.vue';
import Text from '@ohrm/oxd/core/components/Text/Text.vue';
import Form from '@ohrm/oxd/core/components/Form/Form.vue';
import FormRow from '@ohrm/oxd/core/components/Form/FormRow.vue';
import FormActions from '@ohrm/oxd/core/components/Form/FormActions.vue';
import InputField from '@ohrm/oxd/core/components/InputField/InputField.vue';
import InputGroup from '@ohrm/oxd/core/components/InputField/InputGroup.vue';
import TableFilter from '@ohrm/oxd/core/components/TableFilter/TableFilter.vue';
import Grid from '@ohrm/oxd/core/components/Grid/Grid.vue';
import GridItem from '@ohrm/oxd/core/components/Grid/GridItem.vue';
import SubmitButton from '@ohrm/components/buttons/SubmitButton.vue';
import TableHeader from '@ohrm/components/table/TableHeader.vue';
import RequiredText from '@ohrm/components/labels/RequiredText.vue';
import Layout from '@ohrm/components/layout/Layout.vue';
import DateInput from '@ohrm/components/inputs/DateInput.vue';
import TimeInput from '@ohrm/components/inputs/TimeInput.vue';

export default {
  install: (app: App) => {
    app.component('OxdLayout', Layout);
    app.component('OxdCardTable', CardTable);
    app.component('OxdButton', Button);
    app.component('OxdPagination', Pagination);
    app.component('OxdDivider', Divider);
    app.component('OxdText', Text);
    app.component('OxdIconButton', IconButton);
    app.component('OxdForm', Form);
    app.component('OxdFormRow', FormRow);
    app.component('OxdFormActions', FormActions);
    app.component('OxdInputField', InputField);
    app.component('OxdInputGroup', InputGroup);
    app.component('OxdGrid', Grid);
    app.component('OxdGridItem', GridItem);
    app.component('OxdTableFilter', TableFilter);
    app.component('SubmitButton', SubmitButton);
    app.component('TableHeader', TableHeader);
    app.component('RequiredText', RequiredText);
    app.component('DateInput', DateInput);
    app.component('TimeInput', TimeInput);
  },
};

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
import Form from '@ohrm/oxd/core/components/Form/Form.vue';
import FormRow from '@ohrm/oxd/core/components/Form/FormRow.vue';
import FormActions from '@ohrm/oxd/core/components/Form/FormActions.vue';
import InputField from '@ohrm/oxd/core/components/InputField/InputField.vue';
import InputGroup from '@ohrm/oxd/core/components/InputField/InputGroup.vue';
import Grid from '@ohrm/oxd/core/components/Grid/Grid.vue';
import GridItem from '@ohrm/oxd/core/components/Grid/GridItem.vue';
import Text from '@ohrm/oxd/core/components/Text/Text.vue';
import Button from '@ohrm/oxd/core/components/Button/Button.vue';
import Divider from '@ohrm/oxd/core/components/Divider/Divider.vue';
import InstallerLayout from '@/components/InstallerLayout.vue';
import RequiredText from '@/components/RequiredText.vue';
export default {
  install: (app: App) => {
    app.component('RequiredText', RequiredText);
    app.component('OxdDivider', Divider);
    app.component('OxdForm', Form);
    app.component('OxdFormRow', FormRow);
    app.component('OxdFormActions', FormActions);
    app.component('OxdInputField', InputField);
    app.component('OxdInputGroup', InputGroup);
    app.component('OxdGrid', Grid);
    app.component('OxdGridItem', GridItem);
    app.component('InstallerLayout', InstallerLayout);
    app.component('OxdText', Text);
    app.component('OxdButton', Button);
  },
};

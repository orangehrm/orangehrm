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
import Layout from '@orangehrm/oxd/src/core/components/Layout/Layout.vue';
import CardTable from '@orangehrm/oxd/src/core/components/CardTable/CardTable.vue';
import Button from '@orangehrm/oxd/src/core/components/Button/Button.vue';
import IconButton from '@orangehrm/oxd/src/core/components/Button/Icon.vue';
import Pagination from '@orangehrm/oxd/src/core/components/Pagination/Pagination.vue';
import Divider from '@orangehrm/oxd/src/core/components/Divider/Divider.vue';
import Text from '@orangehrm/oxd/src/core/components/Text/Text.vue';
import Form from '@orangehrm/oxd/src/core/components/Form/Form.vue';
import FormRow from '@orangehrm/oxd/src/core/components/Form/FormRow.vue';
import FormActions from '@orangehrm/oxd/src/core/components/Form/FormActions.vue';
import InputField from '@orangehrm/oxd/src/core/components/InputField/InputField.vue';
import Grid from '@orangehrm/oxd/src/core/components/Grid/Grid.vue';
import GridItem from '@orangehrm/oxd/src/core/components/Grid/GridItem.vue';

export default {
  install: (app: App) => {
    app.component('oxd-layout', Layout);
    app.component('oxd-card-table', CardTable);
    app.component('oxd-button', Button);
    app.component('oxd-pagination', Pagination);
    app.component('oxd-divider', Divider);
    app.component('oxd-text', Text);
    app.component('oxd-icon-button', IconButton);
    app.component('oxd-form', Form);
    app.component('oxd-form-row', FormRow);
    app.component('oxd-form-actions', FormActions);
    app.component('oxd-input-field', InputField);
    app.component('oxd-grid', Grid);
    app.component('oxd-grid-item', GridItem);
  },
};

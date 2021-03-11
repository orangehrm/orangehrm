import { createApp } from "vue";
import axios from 'axios';
import App from "./App.vue";


import CardTable from "@orangehrm/oxd/core/components/CardTable/CardTable";
import Button from "@orangehrm/oxd/core/components/Button/Button";
import IconButton from "@orangehrm/oxd/core/components/Button/Icon";
import Pagination from "@orangehrm/oxd/core/components/Pagination/Pagination";
import Divider from "@orangehrm/oxd/core/components/Divider/Divider";
import Text from "@orangehrm/oxd/core/components/Text/Text";

import Form from "@orangehrm/oxd/core/components/Form/Form";
import FormRow from "@orangehrm/oxd/core/components/Form/FormRow";
import FormActions from "@orangehrm/oxd/core/components/Form/FormActions";
import InputField from "@orangehrm/oxd/core/components/InputField/InputField";

const app = createApp(App);

// Globally Register Frequently Used Components
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

// Other init
const regex = /symfony\/client\/dist\/.+/i;
const url = window.location.href;
const baseUrl = url.replace(regex, "symfony/web/index-new.php");
app.config.globalProperties.global = {
  baseUrl: baseUrl
};

// Globally Register Axios
app.config.globalProperties.$http = axios.create({
  baseURL: baseUrl,
  timeout: 3000,
});

app.mount("#app");

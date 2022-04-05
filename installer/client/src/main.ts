import { createApp } from "vue";
import DatabaseConfig from "./pages/DatabaseConfig.vue";
import Form from "@ohrm/oxd/core/components/Form/Form.vue";
import FormRow from "@ohrm/oxd/core/components/Form/FormRow.vue";
import FormActions from "@ohrm/oxd/core/components/Form/FormActions.vue";
import InputField from "@ohrm/oxd/core/components/InputField/InputField.vue";
import InputGroup from "@ohrm/oxd/core/components/InputField/InputGroup.vue";
import Grid from "@ohrm/oxd/core/components/Grid/Grid.vue";
import GridItem from "@ohrm/oxd/core/components/Grid/GridItem.vue";

const app = createApp({
    name: "App",
    components: {
        "database-config": DatabaseConfig,
    },
});

app.component("OxdForm", Form);
app.component("OxdFormRow", FormRow);
app.component("OxdFormActions", FormActions);
app.component("OxdInputField", InputField);
app.component("OxdInputGroup", InputGroup);
app.component("OxdGrid", Grid);
app.component("OxdGridItem", GridItem);

app.mount("#app");

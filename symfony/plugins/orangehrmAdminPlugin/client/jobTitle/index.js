import { createApp, h } from "vue";
import JobTitle from "./JobTitle.vue";

const app = createApp(JobTitle);

const regex = /symfony\/client\/dist\/.+/i;
const url = window.location.href;
const baseUrl = url.replace(regex, "symfony/web/index-new.php");
app.config.globalProperties.global = {
  baseUrl: baseUrl
};

app.mount("#app");

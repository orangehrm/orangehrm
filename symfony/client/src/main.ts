import {createApp} from 'vue';
import axios from 'axios';
import components from './components';
import pages from './pages';

const app = createApp({
  name: "App",
  components: pages
});

// Global Register Components
app.use(components);

const regex = /symfony\/client\/dist\/.+/i;
const url = window.location.href;
const baseUrl = url.replace(regex, 'symfony/web/index-new.php');

app.config.globalProperties.global = {
  baseUrl: baseUrl,
};

app.config.globalProperties.$http = axios.create({
  baseURL: baseUrl,
  timeout: 3000,
});

app.mount('#app');

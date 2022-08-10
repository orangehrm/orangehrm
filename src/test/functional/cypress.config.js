const {defineConfig} = require('cypress');
const {
  truncateTable,
  resetDatabase,
  createSavePoint,
  deleteSavePoints,
  restoreToSavePoint,
} = require('./cypress/support/tasks');

module.exports = defineConfig({
  video: false,
  scrollBehavior: 'top',
  defaultCommandTimeout: 6000,
  eexperimentalInteractiveRunEvents: true,
  e2e: {
    specPattern: 'cypress/integration/**/*.spec.js',
    baseUrl: 'http://php80/orangehrm/web/index.php',
    setupNodeEvents(on, config) {
      const testingApiEndpoint = `${config.baseUrl}/functional-testing`;

      on('task', {
        async 'db:reset'() {
          const [response] = await resetDatabase(testingApiEndpoint);
          return response ? response.data : undefined;
        },
        async 'db:truncate'(payload) {
          const [response] = await truncateTable(
            testingApiEndpoint,
            payload.tables,
          );
          return response ? response.data : undefined;
        },
        async 'db:snapshot'(payload) {
          const [response] = await createSavePoint(
            testingApiEndpoint,
            payload.name,
          );
          return response ? response.data : undefined;
        },
        async 'db:restore'(payload) {
          const [response] = await restoreToSavePoint(
            testingApiEndpoint,
            payload.name,
          );
          return response ? response.data : undefined;
        },
        async 'db:clearSnapshots'(payload) {
          const [response] = await deleteSavePoints(
            testingApiEndpoint,
            payload.names,
          );
          return response ? response.data : undefined;
        },
      });

      on('before:run', async () => {
        const [response] = await deleteSavePoints(testingApiEndpoint, {
          names: undefined,
        });
        return response ? response.data : undefined;
      });

      on('before:spec', async () => {
        if (config.env.runner === 'interactive') {
          const [response] = await deleteSavePoints(testingApiEndpoint, {
            names: undefined,
          });
          return response ? response.data : undefined;
        }
      });
    },
  },
});

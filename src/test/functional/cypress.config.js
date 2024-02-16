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

const {defineConfig} = require('cypress');

/// <reference types="cypress" />
const axios = require('axios');

module.exports = defineConfig({
  defaultCommandTimeout: 6000,
  video: false,
  scrollBehavior: 'top',
  experimentalInteractiveRunEvents: true,
  e2e: {
    baseUrl: 'http://php80/orangehrm/web/index.php',
    supportFile: 'cypress/support/index.js',
    setupNodeEvents(on, config) {
      // `on` is used to hook into various events Cypress emits
      // `config` is the resolved Cypress config

      const asyncWrapper = async (promise) => {
        try {
          return [await promise, null];
        } catch (err) {
          // eslint-disable-next-line no-console
          console.error(err);
          return [null, err];
        }
      };

      const testingApiEndpoint = `${config.baseUrl}/functional-testing`;

      /**
       * Creates a savepoint of DB data
       * @param {String} savepointName
       * @returns {AxiosResponse | error}
       */
      const createSavePoint = async (savepointName) => {
        return await asyncWrapper(
          axios.post(`${testingApiEndpoint}/database/create-savepoint`, {
            savepointName,
          }),
        );
      };

      /**
       * Restore the DB to defined savepoint
       * @param {String} savepointName
       * @returns {AxiosResponse | error}
       */
      const restoreToSavePoint = async (savepointName) => {
        return await asyncWrapper(
          axios.post(`${testingApiEndpoint}/database/restore-to-savepoint`, {
            savepointName,
          }),
        );
      };

      /**
       * Delete defined savepoints from system
       * @param {String} savepointNames
       * @returns {AxiosResponse | error}
       */
      const deleteSavePoints = async (savepointNames) => {
        return await asyncWrapper(
          axios.post(`${testingApiEndpoint}/database/delete-savepoints`, {
            savepointNames,
          }),
        );
      };

      /**
       * Reset the DB back to fresh install state
       * @returns {AxiosResponse | error}
       */
      const resetDatabase = async () => {
        return await asyncWrapper(
          axios.post(`${testingApiEndpoint}/database/reset`),
        );
      };

      /**
       * Truncate the data of given tables
       * @param {Array<String>} tables
       * @returns {AxiosResponse | error}
       */
      const truncateTable = async (tables) => {
        return await asyncWrapper(
          axios.post(`${testingApiEndpoint}/truncate-table`, {tables}),
        );
      };

      on('task', {
        async 'db:reset'() {
          const [response] = await resetDatabase();
          return response ? response.data : undefined;
        },
        async 'db:truncate'(payload) {
          const [response] = await truncateTable(payload.tables);
          return response ? response.data : undefined;
        },
        async 'db:snapshot'(payload) {
          const [response] = await createSavePoint(payload.name);
          return response ? response.data : undefined;
        },
        async 'db:restore'(payload) {
          const [response] = await restoreToSavePoint(payload.name);
          return response ? response.data : undefined;
        },
        async 'db:clearSnapshots'(payload) {
          const [response] = await deleteSavePoints(payload.names);
          return response ? response.data : undefined;
        },
      });

      on('before:run', async () => {
        const [response] = await deleteSavePoints({names: undefined});
        return response ? response.data : undefined;
      });

      on('before:spec', async () => {
        if (config.env.runner === 'interactive') {
          const [response] = await deleteSavePoints({names: undefined});
          return response ? response.data : undefined;
        }
      });
    },
  },
});

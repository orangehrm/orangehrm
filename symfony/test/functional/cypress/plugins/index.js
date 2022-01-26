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

/// <reference types="cypress" />
const axios = require('axios');

const asyncWrapper = async (promise) => {
  try {
    return [await promise, null];
  } catch (err) {
    // eslint-disable-next-line no-console
    console.error(err);
    return [null, err];
  }
};

/**
 * @type {Cypress.PluginConfig}
 */
// eslint-disable-next-line no-unused-vars
module.exports = (on, config) => {
  // `on` is used to hook into various events Cypress emits
  // `config` is the resolved Cypress config
  const testingApiEndpoint = `${config.baseUrl}/functional-testing`;

  /**
   * Creates a savepoint of DB data
   * @param {String} savePointName
   * @returns {AxiosResponse | error}
   */
  const createSavePoint = async (savePointName) => {
    return await asyncWrapper(
      axios.post(`${testingApiEndpoint}/database/create-savepoint`, {
        savePointName,
      }),
    );
  };

  /**
   * Restore the DB to defined savepoint
   * @param {String} savePointName
   * @returns {AxiosResponse | error}
   */
  const restoreToSavePoint = async (savePointName) => {
    return await asyncWrapper(
      axios.post(`${testingApiEndpoint}/database/restore-to-savepoint`, {
        savePointName,
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
  });
};

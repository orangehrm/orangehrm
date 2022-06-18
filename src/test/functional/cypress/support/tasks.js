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
 * Creates a savepoint of DB data
 * @param {string} testingApiEndpoint
 * @param {string} savepointName
 * @returns {AxiosResponse | error}
 */
const createSavePoint = async (testingApiEndpoint, savepointName) => {
  return await asyncWrapper(
    axios.post(`${testingApiEndpoint}/database/create-savepoint`, {
      savepointName,
    }),
  );
};

/**
 * Restore the DB to defined savepoint
 * @param {string} testingApiEndpoint
 * @param {string} savepointName
 * @returns {AxiosResponse | error}
 */
const restoreToSavePoint = async (testingApiEndpoint, savepointName) => {
  return await asyncWrapper(
    axios.post(`${testingApiEndpoint}/database/restore-to-savepoint`, {
      savepointName,
    }),
  );
};

/**
 * Delete defined savepoints from system
 * @param {string} testingApiEndpoint
 * @param {string} savepointName
 * @returns {AxiosResponse | error}
 */
const deleteSavePoints = async (testingApiEndpoint, savepointNames) => {
  return await asyncWrapper(
    axios.post(`${testingApiEndpoint}/database/delete-savepoints`, {
      savepointNames,
    }),
  );
};

/**
 * Reset the DB back to fresh install state
 * @param {string} testingApiEndpoint
 * @returns {AxiosResponse | error}
 */
const resetDatabase = async (testingApiEndpoint) => {
  return await asyncWrapper(axios.post(`${testingApiEndpoint}/database/reset`));
};

/**
 * Truncate the data of given tables
 * @param {string} testingApiEndpoint
 * @param {Array<string>} tables
 * @returns {AxiosResponse | error}
 */
const truncateTable = async (testingApiEndpoint, tables) => {
  return await asyncWrapper(
    axios.post(`${testingApiEndpoint}/truncate-table`, {tables}),
  );
};

module.exports = {
  truncateTable,
  resetDatabase,
  createSavePoint,
  deleteSavePoints,
  restoreToSavePoint,
};

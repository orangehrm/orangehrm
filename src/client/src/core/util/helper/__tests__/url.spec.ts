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

import {prepare} from '../url';

describe('core/util/helper/url', () => {
  test('prepare::without params', () => {
    const result = prepare('/api/v2/employees');
    expect(result).toBe('/api/v2/employees');
  });

  test('prepare::with params (number)', () => {
    const result = prepare('/api/v2/employees/{id}', {id: 1});
    expect(result).toBe('/api/v2/employees/1');
  });

  test('prepare::with params (string)', () => {
    const result = prepare('/api/v2/employees/{id}', {id: '1'});
    expect(result).toBe('/api/v2/employees/1');
  });

  test('prepare::with query', () => {
    const result = prepare(
      '/api/v2/employees',
      {},
      {offset: 5, limit: 50, sortField: 'firstName', activeOnly: true},
    );
    expect(result).toBe(
      '/api/v2/employees?offset=5&limit=50&sortField=firstName&activeOnly=true',
    );
  });

  test('prepare::with query (array type)', () => {
    const result = prepare(
      '/api/v2/employees',
      {},
      {empNumbers: ['1', '2', '3']},
    );
    expect(result).toBe(
      // /api/v2/employees?empNumbers[]=1&empNumbers[]=2&empNumbers[]=3
      '/api/v2/employees?empNumbers%5B%5D=1&empNumbers%5B%5D=2&empNumbers%5B%5D=3',
    );
  });
});

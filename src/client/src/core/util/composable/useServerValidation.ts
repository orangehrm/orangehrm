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

import {AxiosResponse} from 'axios';
import {promiseDebounce} from '@ohrm/oxd';
import {APIService} from '@/core/util/services/api.service';
import {translate as translatorFactory} from '@/core/plugins/i18n/translate';

type useServerValidationOptions = {
  debounce?: boolean;
  debounceOffset?: number;
};

// If a secondary attribute is needed, assign the name and value to matchByField and matchByValue respectively
type createUniqueValidatorOptions = {
  entityId?: number;
  matchByField?: string;
  matchByValue?: string;
  translateKey?: string;
};

interface UniqueValidationResponse {
  data: {
    valid: boolean;
  };
  meta: [];
}

export default function useServerValidation(
  http: APIService,
  options: useServerValidationOptions = {debounce: true, debounceOffset: 500},
) {
  const translate = translatorFactory();

  const createUniqueValidator = (
    entityName: string,
    attributeName: string,
    validationOptions: createUniqueValidatorOptions = {
      translateKey: 'general.already_exists',
    },
  ) => {
    const validationRequest = (value: string) => {
      return new Promise((resolve, reject) => {
        if (value.trim()) {
          http
            .request({
              method: 'GET',
              url: 'api/v2/core/validation/unique',
              params: {
                value,
                entityName,
                attributeName,
                entityId: validationOptions.entityId,
                matchByField: validationOptions.matchByField,
                matchByValue: validationOptions.matchByValue,
              },
            })
            .then((response: AxiosResponse<UniqueValidationResponse>) => {
              const {data} = response.data;
              if (data.valid === true) {
                resolve(true);
              } else {
                resolve(
                  translate(
                    validationOptions.translateKey ?? 'general.already_exists',
                  ),
                );
              }
            })
            .catch((error) => reject(error));
        } else {
          resolve(true);
        }
      });
    };

    return options.debounce
      ? promiseDebounce(validationRequest, options.debounceOffset)
      : validationRequest;
  };

  return {
    createUniqueValidator,
  };
}

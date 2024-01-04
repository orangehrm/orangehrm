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

import {ref} from 'vue';
import {AxiosResponse} from 'axios';
import {APIService} from '@/core/util/services/api.service';

export interface PasswordValidationResponse {
  data: {
    messages: string[];
  };
  meta: {
    strength: number;
  };
}

export default function usePasswordPolicy(http: APIService) {
  const passwordStrength = ref(0);

  const validatePassword = (password: string) => {
    return new Promise((resolve) => {
      if (password.trim() !== '') {
        http
          .request({
            method: 'POST',
            url: `/api/v2/auth/public/validation/password`,
            data: {
              password,
            },
          })
          .then((response: AxiosResponse<PasswordValidationResponse>) => {
            const {data, meta} = response.data;
            passwordStrength.value = meta?.strength || 0;
            if (Array.isArray(data?.messages) && data.messages.length > 0) {
              resolve(data.messages[0]);
            } else {
              resolve(true);
            }
          });
      } else {
        passwordStrength.value = 0;
        resolve(true);
      }
    });
  };

  return {
    passwordStrength,
    validatePassword,
  };
}

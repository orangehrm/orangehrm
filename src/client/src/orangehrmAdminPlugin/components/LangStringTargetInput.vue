<!--
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
 -->

<template>
  <oxd-input-field type="input" :rules="rules.langStringTarget" />
</template>

<script>
import {promiseDebounce} from '@ohrm/oxd';
import {APIService} from '@/core/util/services/api.service';
import usei18n from '@/core/util/composable/usei18n';
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';

export default {
  props: {
    langStringId: {
      type: Number,
      required: true,
    },
    required: {
      type: Boolean,
      default: false,
    },
  },
  setup(props) {
    const {$t} = usei18n();

    const validateLangString = (value) => {
      return new Promise((resolve) => {
        if (value) {
          const http = new APIService(
            window.appGlobal.baseUrl,
            `/api/v2/admin/i18n/translation/${props.langStringId}/validate`,
          );
          http
            .request({
              method: 'GET',
              params: {
                translation: value,
              },
            })
            .then((response) => {
              const {data} = response.data;
              return data.valid === true
                ? resolve(true)
                : resolve($t('admin.' + data.code));
            });
        } else {
          resolve(true);
        }
      });
    };

    return {
      validateLangString,
    };
  },
  data() {
    return {
      rules: {
        langStringTarget: this.required
          ? [
              required,
              shouldNotExceedCharLength(1000),
              promiseDebounce(this.validateLangString, 500),
            ]
          : [
              shouldNotExceedCharLength(1000),
              promiseDebounce(this.validateLangString, 500),
            ],
      },
    };
  },
};
</script>

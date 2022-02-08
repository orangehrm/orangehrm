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

import {AxiosResponse} from 'axios';
import {App, ComponentOptions, ref} from 'vue';
import IntlMessageFormat from 'intl-messageformat';
import {APIService} from '@/core/util/services/api.service';

export type Language = {
  [key: string]: IntlMessageFormat;
};

export interface LanguageResponse {
  [key: string]: {
    source: string;
    target: string;
    description: string;
  };
}

export interface LanguageOptions {
  baseUrl: string;
  resourceUrl: string;
}

export type TranslateAPI = (key: string, fallback?: string) => string;

const langStrings = ref<Language>({});

const translate = () => {
  // TODO: Other format options
  return (key: string, parameters: {[key: string]: string}): string => {
    if (!langStrings.value[key]) return key;
    const translatedString = langStrings.value[key].format<string>(parameters);
    // TODO: Array remove if not necessary ?
    return Array.isArray(translatedString)
      ? translatedString.join(' ')
      : translatedString;
  };
};

const defineMixin = (): ComponentOptions => {
  return {
    beforeCreate(): void {
      this.$t = translate();
    },
  };
};

function createI18n(options: LanguageOptions) {
  const http = new APIService(options.baseUrl, options.resourceUrl);
  return {
    init: function() {
      http
        .request({
          method: 'GET',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            // TODO: max-age header
            'Cache-Control': 'public, only-if-cached, stale-while-revalidate',
          },
        })
        .then((response: AxiosResponse<LanguageResponse>) => {
          const {data} = response;
          for (const key in data) {
            langStrings.value[key] = new IntlMessageFormat(
              data[key].target || data[key].source,
            );
          }
        });
      return this;
    },
    install: function(app: App) {
      // Re add json file as a fallback
      app.mixin(defineMixin());
    },
  };
}

export default createI18n;

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
import {App, ComponentOptions} from 'vue';
import IntlMessageFormat from 'intl-messageformat';
import {APIService} from '@/core/util/services/api.service';
import {mergeConfig} from '@ohrm/oxd/services/store';

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

export const langStrings: Record<string, IntlMessageFormat> = {};

/**
 * A factory function that will return translator function
 * @return {function(key, parameters): string}
 */
export const translate = () => (
  key: string,
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  parameters: {[key: string]: any} = {},
): string => {
  // IntlMessageFormat.format method will throw error if not every argument in the message pattern
  // has been provided. sourrounded by try catch to fallback incase of param resolution
  try {
    if (!langStrings[key]) return key;
    const translatedString = langStrings[key].format<string>(parameters);
    if (Array.isArray(translatedString)) {
      return typeof translatedString[0] === 'string'
        ? translatedString[0]
        : key;
    }
    return translatedString;
  } catch (error) {
    // eslint-disable-next-line no-console
    console.error(error);
    return key;
  }
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
      return new Promise<void>(resolve => {
        http
          .request({
            method: 'GET',
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json',
              'Cache-Control':
                'public, only-if-cached, stale-while-revalidate, max-age=60',
            },
          })
          .then((response: AxiosResponse<LanguageResponse>) => {
            const {data} = response;
            const language: {[key: string]: string} = {};
            for (const key in data) {
              // https://formatjs.io/docs/intl-messageformat#intlmessageformat-constructor
              language[key] = data[key].target || data[key].source;
              langStrings[key] = new IntlMessageFormat(
                data[key].target || data[key].source,
                undefined,
                undefined,
                {ignoreTag: true}, // no html/xml markup parsing
              );
            }
            mergeConfig({
              language,
            });
          })
          .finally(() => resolve());
      });
    },
    i18n: function(app: App) {
      app.mixin(defineMixin());
    },
  };
}

export default createI18n;

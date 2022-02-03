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

import {App, ComponentOptions} from 'vue';
import {APIService} from '@/core/util/services/api.service';

export type Language = {
  [key: string]: {
    source: string;
    target: string;
    description: string;
  };
};

export interface LanguageOptions {
  baseUrl: string;
  resourceUrl: string;
  languagePack: Language;
}

export type TranslateAPI = (key: string, fallback?: string) => string;

const translate = (language: Language) => {
  return (key: string, fallback = ''): string => {
    return language[key] ? language[key].target : fallback;
  };
};

const defineMixin = (language: Language): ComponentOptions => {
  return {
    beforeCreate(): void {
      this.$t = translate(language);
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
            // 'Cache-Control': 'no-store, no-cache, must-revalidate',
            'Cache-Control': 'public, only-if-cached, stale-if-error',
          },
        })
        .then(response => {
          console.log(response);
        });
      return this;
    },
    install: function(app: App) {
      if (!options.languagePack) {
        throw new Error('Language pack not found!');
      }
      app.mixin(defineMixin(options.languagePack));
    },
  };
}

export default createI18n;

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

export type Language = {
  source_language: string;
  target_language: string;
  date: string;
  modules: {
    [moduleName: string]: {
      version: string;
      messages: {
        [key: string]: {
          source: string;
          target: string;
          description: string;
        };
      };
    };
  };
};

export interface LanguageOptions {
  langugePack: Language;
}

export type TranslateAPI = (key: string, fallback?: string) => string;

const translate = (language: Language) => {
  return (key: string, fallback = ''): string => {
    const [moduleName, messageKey] = key.split('.');
    if (moduleName && messageKey) {
      return language.modules[moduleName]?.messages[messageKey]?.target;
    }
    return fallback;
  };
};

function defineMixin(language: Language): ComponentOptions {
  return {
    beforeCreate(): void {
      this.$t = translate(language);
    },
  };
}

export default {
  install: (app: App, options: LanguageOptions) => {
    const translations = options.langugePack;
    if (!translations) {
      throw new Error('Language pack not found!');
    }
    app.mixin(defineMixin(translations));
  },
};

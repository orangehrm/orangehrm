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

import {App, inject, ComponentOptions, getCurrentInstance} from 'vue';

export type Capability = 'canRead' | 'canCreate' | 'canUpdate' | 'canDelete';

export type DataGroup = {
  [key in Capability]: boolean;
};

export interface Permission {
  [key: string]: DataGroup;
}

export interface AclAPI {
  read: (...args: string[]) => boolean;
  create: (...args: string[]) => boolean;
  update: (...args: string[]) => boolean;
  delete: (...args: string[]) => boolean;
}

const ResolvePermissions = (capability: Capability) => {
  return (...args: string[]): boolean => {
    const instance = getCurrentInstance();
    if (!instance) {
      throw new Error('Vue app context not found!');
    }
    const permissions = inject('permissions', undefined) as
      | Permission
      | undefined;
    if (permissions) {
      return args.reduce(
        (acc: boolean, rule: string) =>
          acc && Boolean(permissions[rule]) && permissions[rule][capability],
        true,
      );
    }
    return false;
  };
};

function defineMixin(): ComponentOptions {
  return {
    beforeCreate(): void {
      this.$can = {
        read: ResolvePermissions('canRead'),
        create: ResolvePermissions('canCreate'),
        update: ResolvePermissions('canUpdate'),
        delete: ResolvePermissions('canDelete'),
      };
    },
  };
}

export default {
  install: (app: App) => {
    app.mixin(defineMixin());
  },
};

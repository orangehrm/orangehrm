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

import {getCurrentInstance} from 'vue';
import {ToasterAPI} from '../../plugins/toaster/toaster';

export default function useToast() {
  const $toast: ToasterAPI | undefined =
    getCurrentInstance()?.appContext.config.globalProperties.$toast;

  /**
   * typesafe & nullsafe wrapper for functions
   * https://stackoverflow.com/a/61212868/2182418
   */
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const wrap = <Func extends (...args: any[]) => any>(
    fn: Func | undefined,
  ): ((...args: Parameters<Func>) => ReturnType<Func>) => {
    return (...args: Parameters<Func>): ReturnType<Func> => {
      return fn && fn(...args);
    };
  };

  return {
    notify: wrap($toast?.notify),
    show: wrap($toast?.show),
    success: wrap($toast?.success),
    error: wrap($toast?.error),
    info: wrap($toast?.info),
    warn: wrap($toast?.warn),
    clear: wrap($toast?.clear),
    clearAll: wrap($toast?.clearAll),
    saveSuccess: wrap($toast?.saveSuccess),
    addSuccess: wrap($toast?.addSuccess),
    updateSuccess: wrap($toast?.updateSuccess),
    deleteSuccess: wrap($toast?.deleteSuccess),
    cannotDelete: wrap($toast?.cannotDelete),
    noRecordsFound: wrap($toast?.noRecordsFound),
  };
}

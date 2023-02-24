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

import {inject, InjectionKey} from 'vue';
import {convertPHPDateFormat} from '@ohrm/oxd';

type DateFormat = {
  id: string;
  label: string;
};

export const dateFormatKey: InjectionKey<DateFormat | null> =
  Symbol('dateFormat');

export default function useDateFormat() {
  const dateFormat = inject(dateFormatKey);
  if (!dateFormat) throw new Error('Date format is invalid');
  const jsDateFormat = convertPHPDateFormat(dateFormat.id);
  const userDateFormat = dateFormat.label;
  const timeFormat = 'HH:mm';
  const jsTimeFormat = 'hh:mm a';

  return {
    timeFormat,
    jsTimeFormat,
    jsDateFormat,
    userDateFormat,
  };
}

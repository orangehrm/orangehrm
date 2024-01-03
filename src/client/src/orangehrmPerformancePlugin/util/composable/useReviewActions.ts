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

type ReviewAction = {
  component: string;
  props: object;
};

export const viewIcon: ReviewAction = {
  component: 'oxd-icon-button',
  props: {
    name: 'eye-fill',
    title: 'View',
  },
};

export const editIcon: ReviewAction = {
  component: 'oxd-icon-button',
  props: {
    name: 'pencil-fill',
    title: 'Edit',
  },
};

export const evaluateIcon: ReviewAction = {
  component: 'oxd-icon-button',
  props: {
    name: 'file-text-fill',
    title: 'Evaluate',
  },
};

export const viewLabel: ReviewAction = {
  component: 'oxd-button',
  props: {
    label: 'View',
    displayType: 'text',
    size: 'medium',
    style: {
      'min-width': '120px',
    },
  },
};

export const editLabel: ReviewAction = {
  component: 'oxd-button',
  props: {
    label: 'Edit',
    displayType: 'text',
    size: 'medium',
    style: {
      'min-width': '120px',
    },
  },
};

export const evaluateLabel: ReviewAction = {
  component: 'oxd-button',
  props: {
    label: 'Evaluate',
    displayType: 'text',
    size: 'medium',
    style: {
      'min-width': '120px',
    },
  },
};

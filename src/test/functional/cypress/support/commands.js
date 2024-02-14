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

import {OXD_INPUT_ELEMENTS, OXD_ELEMENTS, OXD_TOASTS} from './oxd';

Cypress.Commands.add('login', ({username, password}) => {
  cy.visit('/auth/login');
  cy.get('input[name=username]').setValue(username);
  cy.get('input[name=password]').setValue(password);
  cy.get('form').submit();
});

Cypress.Commands.add(
  'loginTo',
  ({username, password}, url, options = undefined) => {
    cy.visit(url, options);
    cy.get('input[name=username]').setValue(username);
    cy.get('input[name=password]').setValue(password);
    cy.get('form').submit();
  },
);

Cypress.Commands.add('apiLogin', ({username, password}) => {
  cy.request({
    method: 'POST',
    url: '/functional-testing/auth/validate',
    body: {
      username,
      password,
    },
  });
});

Cypress.Commands.add('getOXD', (type, options = {}) => {
  return cy.get(OXD_ELEMENTS[type], options);
});

Cypress.Commands.add('getOXDInput', (label) => {
  let element;
  let count;

  const log = Cypress.log({
    autoEnd: false,
    name: 'getOXDInput',
    displayName: 'get oxd input',
    consoleProps() {
      return {
        label: label,
        yielded: element,
        elements: count,
      };
    },
  });

  cy.get(OXD_ELEMENTS.inputGroup, {log: false})
    .contains(label, {log: false})
    .closest(OXD_ELEMENTS.inputGroup, {log: false})
    .find(Object.values(OXD_INPUT_ELEMENTS).join(', '), {log: false})
    .then(($el) => {
      element = Cypress.dom.getElements($el);
      count = $el.length;
      log.set({$el});
      log.snapshot().end();
    });

  cy.on('fail', (err) => {
    log.error(err);
    log.end();
    throw err;
  });
});

Cypress.Commands.add('toast', (type, message, options = {}) => {
  return cy.get(OXD_TOASTS[type], options).contains(message);
});

Cypress.Commands.add(
  'setValue',
  {
    prevSubject: 'element',
  },
  (subject, value) => {
    const element = subject[0];

    const inputEvent = new Event('input', {bubbles: true});

    const setter =
      element.tagName.toLowerCase() === 'input'
        ? Object.getOwnPropertyDescriptor(
            window.HTMLInputElement.prototype,
            'value',
          ).set
        : Object.getOwnPropertyDescriptor(
            window.HTMLTextAreaElement.prototype,
            'value',
          ).set;
    setter && setter.call(element, value);
    element.dispatchEvent(inputEvent);

    Cypress.log({
      name: 'setValue',
      displayName: 'set value',
      message: value,
      $el: subject,
      consoleProps: () => {
        return {
          value,
        };
      },
    });

    cy.wrap(element, {log: false});
  },
);

Cypress.Commands.add(
  'isInvalid',
  {
    prevSubject: 'element',
  },
  (subject, message) => {
    let element;
    let count;

    const log = Cypress.log({
      autoEnd: false,
      name: 'isInvalid',
      displayName: 'is invalid',
      consoleProps() {
        return {
          message: message,
          yielded: element,
          elements: count,
        };
      },
    });

    cy.get(subject, {log: false})
      .closest(OXD_ELEMENTS.inputGroup, {log: false})
      .contains(message, {log: false})
      .then(($el) => {
        element = Cypress.dom.getElements($el);
        count = $el.length;
        log.set({$el});
        log.snapshot().end();
      });

    cy.on('fail', (err) => {
      log.error(err);
      log.end();
      throw err;
    });
  },
);

Cypress.Commands.add(
  'selectOption',
  {
    prevSubject: 'element',
  },
  (subject, value) => {
    const element = subject[0];
    const log = Cypress.log({
      autoEnd: false,
      name: 'selectOption',
      displayName: 'select option',
      consoleProps() {
        return {
          value: value,
          yielded: element,
        };
      },
    });

    cy.wrap(element, {log: false}).click('center', {log: false});
    cy.wrap(element, {log: false}).then(($el) => {
      cy.wrap($el.closest(OXD_ELEMENTS.selectWrapper), {log: false})
        .find(OXD_ELEMENTS.option, {log: false})
        .contains(value, {log: false})
        .click('center', {log: false});
    });

    cy.on('fail', (err) => {
      log.error(err);
      log.end();
      throw err;
    });
  },
);

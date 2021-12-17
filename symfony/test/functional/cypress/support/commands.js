// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

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

Cypress.Commands.add(
  'apiLogin',
  ({username, password}, url = '/auth/login') => {
    cy.visit(url);
    cy.get('input[name=_token]').then(($token) => {
      const csrfToken = $token.val();
      cy.request({
        method: 'POST',
        url: '/index.php/auth/validate',
        form: false,
        body: {
          username,
          password,
          _token: csrfToken,
        },
      });
    });
  },
);

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

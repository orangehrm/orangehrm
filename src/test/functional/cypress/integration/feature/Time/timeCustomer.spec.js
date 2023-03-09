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

describe('Time - Customer', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('viewport').then(({HD}) => {
      cy.viewport(HD.width, HD.height);
    });
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/time/customers/*').as('getCustomerById');
    cy.intercept('GET', '**/api/v2/time/customers/*').as('getAllCustomer');
    cy.intercept('PUT', '**/api/v2/time/customers/*').as('updateCustomer');
    cy.intercept('POST', '**/api/v2/time/customers').as('addCustomer');
    cy.intercept('DELETE', '**/api/v2/time/customers/*').as('deleteCustomer');
    cy.intercept('GET', '**/api/v2/time/time-sheet-period').as(
      'getTimeSheetPeriod',
    );
    cy.intercept('PUT', '**/api/v2/time/time-sheet-period').as(
      'updateTimeSheetPeriod',
    );
    cy.intercept('GET', '**/api/v2/time/validation/customer-name*').as(
      'lengthyValidation',
    );
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  describe('Configuring time sheet period', function () {
    it('Verify admin ability to successfully save the timesheet period.', function () {
      cy.loginTo(this.user, '/time/viewEmployeeTimesheet');
      cy.wait('@getTimeSheetPeriod');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('First Day of the Week').selectOption('Tuesday');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateTimeSheetPeriod');
      cy.toast('success', 'Successfully Saved');
      cy.task('db:snapshot', {name: 'defineTimeSheetPeriod'});
    });
  });

  describe('Verify page headers and titles in the Time - customer screen', function () {
    it('Verifying headers and titles', function () {
      cy.task('db:restore', {name: 'defineTimeSheetPeriod'});
      cy.loginTo(this.user, '/time/viewCustomers');
      cy.getOXD('pageTitle').contains('Customers');
      cy.get('.oxd-topbar-header-breadcrumb-module').should('contain', 'Time');
    });
  });

  describe('Verifying adding customer scenarios', function () {
    it('Verify admin addding customer details and clicking cancel', function () {
      cy.task('db:restore', {name: 'defineTimeSheetPeriod'});
      cy.loginTo(this.user, '/time/addCustomer');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXDInput('Description').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.getOXD('pageTitle').should('contain', 'Customers');
    });

    it('Verify admin adding customers without filling required field', function () {
      cy.task('db:restore', {name: 'defineTimeSheetPeriod'});
      cy.loginTo(this.user, '/time/addCustomer');
      cy.scrollTo('top');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Description').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
        cy.get('.oxd-input-group > .oxd-text').should('contain', 'Required');
      });
    });

    it('Verify adding customer successfully', function () {
      cy.task('db:restore', {name: 'defineTimeSheetPeriod'});
      cy.loginTo(this.user, '/time/addCustomer');
      cy.scrollTo('top');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXDInput('Description').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@addCustomer');
      cy.toast('success', 'Successfully Saved');
      cy.task('db:snapshot', {name: 'addedFirstCustomer'});
    });

    it('Verify duplicate validation for customer name field', function () {
      cy.task('db:restore', {name: 'addedFirstCustomer'});
      cy.loginTo(this.user, '/time/addCustomer');
      cy.scrollTo('top');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .type(this.strings.chars30.text)
          .isInvalid('Already exists');
      });
    });

    it('Verify text field character validations', function () {
      cy.task('db:restore', {name: 'defineTimeSheetPeriod'});
      cy.loginTo(this.user, '/time/addCustomer');
      cy.scrollTo('top');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type('@#Aa12');
        cy.getOXDInput('Description').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@addCustomer');
      cy.toast('success', 'Successfully Saved');
    });
  });

  describe('Verifying updating scenarios', function () {
    it('Verify admin ability to edit customers and toast message', function () {
      cy.task('db:restore', {name: 'addedFirstCustomer'});
      cy.loginTo(this.user, '/time/addCustomer/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type('Updated Customer Name');
        cy.getOXDInput('Description').clear().type('Updated Description');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateCustomer');
      cy.toast('success', 'Successfully Updated');
    });

    it('Verify admin editing customers and clicking cancel', function () {
      cy.task('db:restore', {name: 'addedFirstCustomer'});
      cy.loginTo(this.user, '/time/addCustomer/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type('Updated Customer Name');
        cy.getOXDInput('Description').clear().type('Updated Description');
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.getOXD('pageTitle').should('contain', 'Customers');
    });

    it('Verify admin removing required field data when editing', function () {
      cy.task('db:restore', {name: 'addedFirstCustomer'});
      cy.loginTo(this.user, '/time/addCustomer/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().setValue('').isInvalid('Required');
      });
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(4000);
    });

    it('Verify admin editing fields with different character values', function () {
      cy.task('db:restore', {name: 'addedFirstCustomer'});
      cy.loginTo(this.user, '/time/addCustomer/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type('Updated Customer Name with @#12');
        cy.getOXDInput('Description')
          .clear()
          .type('Updated Description with @#12');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateCustomer');
      cy.toast('success', 'Successfully Updated');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(4000);
    });
  });

  describe('Delete Added customers', function () {
    it('Delete a single Customer', function () {
      cy.task('db:restore', {name: 'addedFirstCustomer'});
      cy.loginTo(this.user, '/time/viewCustomers');
      cy.get(
        '.oxd-table-body > :nth-child(1) .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.toast('success', 'Successfully Deleted');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
    });
    it('Bulk Delete Customers', function () {
      cy.task('db:restore', {name: 'addedFirstCustomer'});
      cy.loginTo(this.user, '/time/viewCustomers');
      cy.get('.oxd-table-header .oxd-checkbox-input').click();
      cy.getOXD('button').contains('Delete Selected').click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.toast('success', 'Successfully Deleted');
    });
  });
});

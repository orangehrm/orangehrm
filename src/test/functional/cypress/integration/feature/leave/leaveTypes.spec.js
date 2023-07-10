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
describe('Leave - leave types', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/leave/leave-types*').as('getLeaveTypes');
    cy.intercept('POST', '**/api/v2/leave/leave-types').as('saveLeaveType');
    cy.intercept('PUT', '**/api/v2/leave/leave-types/*').as('putLeaveType');
    cy.intercept('DELETE', '**/api/v2/leave/leave-types').as('deleteLeaveType');
    cy.intercept('GET', '**/api/v2/admin/validation/unique*').as(
      'getUniqueValidation',
    );
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  describe('get leave type list', function () {
    it('get leave type list', function () {
      cy.loginTo(this.user, '/leave/leaveTypeList');
      cy.wait('@getLeaveTypes');
      cy.toast('info', 'No Records Found');
    });
  });

  describe('create snapshot with leave type', function () {
    it('create snapshot with leave type', function () {
      cy.loginTo(this.user, '/leave/defineLeaveType');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.leaveTypes.leavetype2);
        cy.wait('@getUniqueValidation')
          .its('response.statusCode')
          .should('eq', 200);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@saveLeaveType').then(function () {
        cy.task('db:snapshot', {name: 'leaveTypes'});
      });
    });
  });

  describe('add leave type', function () {
    it('add a leave type and save', function () {
      cy.loginTo(this.user, '/leave/defineLeaveType');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.leaveTypes.leavetype1);
        cy.wait('@getUniqueValidation')
          .its('response.statusCode')
          .should('eq', 200);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@saveLeaveType');
      cy.toast('success', 'Successfully Saved');
    });

    it('add a leave type and cancel', function () {
      cy.loginTo(this.user, '/leave/defineLeaveType');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.leaveTypes.leavetype2);
        cy.wait('@getUniqueValidation')
          .its('response.statusCode')
          .should('eq', 200);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getLeaveTypes');
      cy.getOXD('pageTitle').should('include.text', 'Leave Types');
    });

    it('add leave type form validations', function () {
      cy.task('db:restore', {name: 'leaveTypes'});
      cy.loginTo(this.user, '/leave/defineLeaveType');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .type(this.strings.chars100.text)
          .isInvalid('Should not exceed 50 characters');
        cy.getOXDInput('Name').setValue('').isInvalid('Required');
        cy.getOXDInput('Name')
          .type(this.strings.leaveTypes.leavetype2)
          .isInvalid('Already exists');
      });
    });
  });

  describe('update leave type', function () {
    it('update leave type', function () {
      cy.task('db:restore', {name: 'leaveTypes'});
      cy.loginTo(this.user, '/leave/leaveTypeList');
      cy.wait('@getLeaveTypes');
      cy.get(
        '.oxd-table-body > :nth-child(1) .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type(this.strings.chars30.text);
        cy.wait('@getUniqueValidation')
          .its('response.statusCode')
          .should('eq', 200);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putLeaveType');
      cy.toast('success', 'Successfully Updated');
    });
  });

  describe('delete leave type', function () {
    it('delete leave type', function () {
      cy.task('db:restore', {name: 'leaveTypes'});
      cy.loginTo(this.user, '/leave/leaveTypeList');
      cy.get(
        '.oxd-table-body > :nth-child(1) .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@deleteLeaveType');
      cy.toast('success', 'Successfully Deleted');
    });
  });
});

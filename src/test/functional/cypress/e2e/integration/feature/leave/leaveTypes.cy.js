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

describe('Leave - leave types', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/leave/leave-types*').as('getLeaveTypes');
    cy.intercept('POST', '**/api/v2/leave/leave-types').as('saveLeaveType');
    cy.intercept('PUT', '**/api/v2/leave/leave-types/*').as('putLeaveType');
    cy.intercept('DELETE', '**/api/v2/leave/leave-types').as('deleteLeaveType');
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
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@saveLeaveType');
      cy.toast('success', 'Successfully Saved');
    });

    it('add a leave type and cancel', function () {
      cy.loginTo(this.user, '/leave/defineLeaveType');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.leaveTypes.leavetype2);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getLeaveTypes');
      cy.getOXD('pageTitle').should('include.text', 'Leave Types');
    });

    it('add leave type form validations', function () {
      cy.task('db:restore', {name: 'leaveTypes'});
      cy.loginTo(this.user, '/leave/defineLeaveType');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').then(($input) => {
          cy.wrap($input).type(this.strings.chars100.text);
          cy.wrap($input).isInvalid('Should not exceed 50 characters');
          cy.wrap($input).setValue('');
          cy.wrap($input).isInvalid('Required');
          cy.wrap($input).type(this.strings.leaveTypes.leavetype2);
          cy.wrap($input).isInvalid('Already exists');
        });
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
        cy.getOXDInput('Name').then(($input) => {
          cy.wrap($input).clear();
          cy.wrap($input).type(this.strings.chars30.text);
        });
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

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

import user from '../../../fixtures/user.json';

describe('Leave - Entitlements', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/leave/leave-types*').as('getLeaveTypes');
    cy.intercept('POST', '**/api/v2/leave/leave-types').as('saveLeaveType');
    cy.intercept('POST', '**/api/v2/admin/locations').as('saveLocation');
    cy.intercept('POST', '**/api/v2/pim/employees').as('addEmployee');
    cy.intercept('POST', '**/api/v2/leave/leave-entitlements').as(
      'saveEntitlement',
    );
    cy.intercept('GET', '**/api/v2/leave/leave-period').as('getLeavePeriod');
    cy.intercept('PUT', '**/api/v2/leave/leave-period').as('putLeavePeriod');
    cy.intercept(
      'GET',
      '**/api/v2/pim/employees?nameOrId=j&includeEmployees=currentAndPast',
    ).as('employees');
    cy.intercept('PUT', '**/api/v2/pim/employees/2/job-details').as(
      'putPimJob',
    );
    cy.intercept('GET', '**/api/v2/admin/subunits*').as('getSubunit');
    cy.intercept('POST', '**/api/v2/admin/subunits').as('addSubunit');
    cy.fixture('user').then((data) => {
      this.adminUser = data.admin;
    });
    cy.fixture('viewport').then(({HD}) => {
      cy.viewport(HD.width, HD.height);
    });
  });

  describe('create snapshot with leave period', function () {
    it.only('create snapshot with leave period', function () {
      cy.loginTo(user.admin, '/leave/defineLeavePeriod');
      cy.getOXD('form').within(() => {
        cy.wait('@getLeavePeriod');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putLeavePeriod').then(function () {
        cy.task('db:snapshot', {name: 'leavePeriods'});
      });
    });
  });

  describe('create snapshot with leave type', function () {
    it('Add leave type1', function () {
      cy.task('db:restore', {name: 'leavePeriods'});
      cy.loginTo(user.admin, '/leave/defineLeaveType');
      cy.wait('@getLeaveTypes');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.leaveTypes.leavetype1);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@saveLeaveType').then(function () {
        cy.task('db:snapshot', {name: 'leaveType'});
      });
    });
  });

  describe('create snapshot with location', function () {
    it('Add location1', function () {
      cy.task('db:restore', {name: 'leaveType'});
      cy.loginTo(user.admin, '/admin/saveLocation');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars10.text);
        cy.getOXDInput('Country').selectOption('Sri Lanka');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@saveLocation').then(function () {
        cy.task('db:snapshot', {name: 'locations'});
      });
    });

    it('Add location2', function () {
      cy.task('db:restore', {name: 'leaveType'});
      cy.loginTo(user.admin, '/admin/saveLocation');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXDInput('Country').selectOption('India');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@saveLocation').then(function () {
        cy.task('db:snapshot', {name: 'locations2'});
      });
    });
  });

  describe('create snapshot with sub unit', function () {
    it.skip('Add sub unit', function () {
      cy.loginTo(user.admin, '/admin/viewCompanyStructure');
      cy.get('[type="checkbox"]').check();
      cy.wait('@getSubunit');
      cy.get('.oxd-button').click();
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@addSubunit').then(function () {
        cy.task('db:snapshot', {name: 'newtest'});
      });
    });
  });

  describe('create snapshot with employees', function () {
    it('Add employees', function () {
      cy.task('db:restore', {name: 'leaveType'});
      cy.loginTo(user.admin, '/pim/addEmployee');
      cy.getOXD('form').within(() => {
        cy.get(
          '.--name-grouped-field > :nth-child(1) > :nth-child(2) > .oxd-input',
        ).type('Linda');
        cy.get(':nth-child(2) > :nth-child(2) > .oxd-input').type('Jane');
        cy.get(':nth-child(3) > :nth-child(2) > .oxd-input').type('Anderson');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@addEmployee').then(function () {
        cy.task('db:snapshot', {name: 'employee1'});
      });
    });

    it('Add employee with location', function () {
      cy.task('db:restore', {name: 'locations'});
      cy.loginTo(user.admin, '/pim/addEmployee');
      cy.getOXD('form').within(() => {
        cy.get(
          '.--name-grouped-field > :nth-child(1) > :nth-child(2) > .oxd-input',
        ).type('Cobby');
        cy.get(':nth-child(2) > :nth-child(2) > .oxd-input').type('Paul');
        cy.get(':nth-child(3) > :nth-child(2) > .oxd-input').type('Killby');
        cy.getOXD('button').contains('Save').click();
        cy.wait('@addEmployee');
      });
      cy.visit('/pim/viewJobDetails/empNumber/2');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Location').selectOption(this.strings.chars10.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putPimJob').then(function () {
        cy.task('db:snapshot', {name: 'employee2'});
      });
    });

    it.skip('Add employee with sub unit', function () {
      cy.task('db:restore', {name: 'newtest'});
      cy.loginTo(user.admin, '/pim/addEmployee');
      cy.getOXD('form').within(() => {
        cy.get(
          '.--name-grouped-field > :nth-child(1) > :nth-child(2) > .oxd-input',
        ).type('Kevin');
        cy.get(':nth-child(2) > :nth-child(2) > .oxd-input').type('Paul');
        cy.get(':nth-child(3) > :nth-child(2) > .oxd-input').type('Mathews');
        cy.getOXD('button').contains('Save').click();
        cy.wait('@addEmployee');
      });
      cy.visit('/pim/viewJobDetails/empNumber/2');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Sub Unit').selectOption(this.strings.chars30.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putPimJob').then(function () {
        cy.task('db:snapshot', {name: 'employee3'});
      });
    });
  });

  describe('Add entitlements', function () {
    it('Add an entitlement to a single user', function () {
      cy.task('db:restore', {name: 'leaveType'});
      cy.loginTo(user.admin, '/leave/addLeaveEntitlement');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Employee Name').type('Jacqueline');
        cy.getOXD('option2').contains('Jacqueline White').click();
        cy.getOXDInput('Leave Type').selectOption(
          this.strings.leaveTypes.leavetype1,
        );
        cy.getOXDInput('Entitlement').type('10');
        cy.getOXD('button').contains('Save').click();
      });
      cy.get('.orangehrm-modal-footer > .oxd-button--secondary').click();
      cy.wait('@saveEntitlement');
      cy.toast('success', 'Successfully Saved').then(function () {
        cy.task('db:snapshot', {name: 'addentitlementtosingleemp'});
      });
    });

    it('Add entitlements for all employees', function () {
      cy.task('db:restore', {name: 'employee1'});
      cy.loginTo(user.admin, '/leave/addLeaveEntitlement');
      cy.getOXD('form').within(() => {
        cy.get(
          ':nth-child(2) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
        ).click();
        cy.get('.orangehrm-leave-entitled > .oxd-text').should(
          'include.text',
          'matches (2) employees',
        );
        cy.getOXDInput('Leave Type').selectOption(
          this.strings.leaveTypes.leavetype1,
        );
        cy.getOXDInput('Entitlement').type('10');
        cy.getOXD('button').contains('Save').click();
      });
      cy.get(
        ':nth-child(6) > .oxd-form-actions > .oxd-button--secondary',
      ).click();
      cy.wait('@saveEntitlement');
      cy.toast('success', 'Entitlement added to 2 employees').then(function () {
        cy.task('db:snapshot', {name: 'addentitlementtoallemp'});
      });
    });

    it('Add an entitlement to employee with location', function () {
      cy.task('db:restore', {name: 'employee2'});
      cy.loginTo(user.admin, '/leave/addLeaveEntitlement');
      cy.getOXD('form').within(() => {
        cy.get(
          ':nth-child(2) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
        ).click();
        cy.getOXDInput('Location').selectOption(this.strings.chars10.text);
        cy.getOXDInput('Leave Type').selectOption(
          this.strings.leaveTypes.leavetype1,
        );
        cy.getOXDInput('Entitlement').type('5');
        cy.getOXD('button').contains('Save').click();
      });
      cy.get(
        ':nth-child(6) > .oxd-form-actions > .oxd-button--secondary',
      ).click();
      cy.wait('@saveEntitlement');
      cy.toast('success', 'Entitlement added to 1 employee');
    });

    it('Add an entitlement to a different time period', function () {
      cy.task('db:restore', {name: 'leaveType'});
      cy.loginTo(user.admin, '/leave/addLeaveEntitlement');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Employee Name').type('Jacqueline');
        cy.getOXD('option2').contains('Jacqueline White').click();
        cy.getOXDInput('Leave Type').selectOption(
          this.strings.leaveTypes.leavetype1,
        );
        cy.getOXDInput('Leave Period').selectOption('2023-01-01 - 2023-12-31');
        cy.getOXDInput('Entitlement').type('10');
        cy.getOXD('button').contains('Save').click();
      });
      cy.get('.orangehrm-modal-footer > .oxd-button--secondary').click();
      cy.wait('@saveEntitlement');
      cy.toast('success', 'Successfully Saved');
    });

    it('Add entitlements to a location with no employees', function () {
      cy.task('db:restore', {name: 'locations2'});
      cy.loginTo(user.admin, '/leave/addLeaveEntitlement');
      cy.getOXD('form').within(() => {
        cy.get(
          ':nth-child(2) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
        ).click();
        cy.getOXDInput('Location').selectOption(this.strings.chars30.text);
        cy.get('.orangehrm-leave-entitled > .oxd-text').should(
          'include.text',
          'Matches no employee',
        );
        cy.getOXDInput('Leave Type').selectOption(
          this.strings.leaveTypes.leavetype1,
        );
        cy.getOXDInput('Entitlement').type('10');
        cy.getOXD('button').contains('Save').click();
      });
      cy.get('.orangehrm-modal-header > .oxd-text').should(
        'include.text',
        'No matching employees',
      );
      cy.get('.orangehrm-modal-footer > .oxd-button').click();
    });
  });

  describe('Validate add entitlement form', function () {
    it('Validate add entitlement form leave type, employee name and leave period field', function () {
      cy.task('db:restore', {name: 'leavePeriods'});
      cy.loginTo(user.admin, '/leave/addLeaveEntitlement');
      cy.getOXD('form').within(() => {
        //cy.getOXDInput('Leave Type').contains('No leave types defined');
        cy.getOXD('button').contains('Save').click();
        cy.getOXDInput('Leave Type').isInvalid('Required');
        cy.getOXDInput('Leave Period').isInvalid('Required');
        cy.getOXDInput('Employee Name').isInvalid('Required');
      });
    });

    it.only('Validate add entitlement form entitlements field', function () {
      cy.task('db:restore', {name: 'leavePeriods'});
      cy.loginTo(user.admin, '/leave/addLeaveEntitlement');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Entitlement')
          .type('test')
          .isInvalid('Should be a number with upto 2 decimal places');
        cy.getOXDInput('Entitlement')
          .clear()
          .type('5.123')
          .isInvalid('Should be a number with upto 2 decimal places');
        cy.getOXDInput('Entitlement').setValue('').isInvalid('Required');
      });
    });
  });
});

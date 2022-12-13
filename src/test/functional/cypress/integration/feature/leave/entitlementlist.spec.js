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
    cy.intercept(
      'GET',
      '**/api/v2/admin/validation/user-name?userName=Mike22*',
    ).as('getESSusername');
    cy.intercept(
      'GET',
      '**/api/v2/admin/validation/user-name?userName=John22*',
    ).as('getSupusername');
    cy.intercept('POST', '**/api/v2/admin/users').as('postESSuser');
    cy.intercept('GET', '**/api/v2/leave/leave-entitlements/1').as(
      'getEntitlements',
    );
    cy.intercept('PUT', '**/api/v2/leave/leave-entitlements/1').as(
      'putEntitlements',
    );
    cy.intercept('POST', '**/api/v2/leave/leave-requests').as('applyLeave');
    cy.intercept('DELETE', '**/api/v2/leave/leave-entitlements').as(
      'deleteEntitlements',
    );
    cy.intercept('POST', '**/api/v2/pim/employees/2/subordinates').as(
      'postSubordinate',
    );
    cy.fixture('user').then((data) => {
      this.adminUser = data.admin;
      this.essUser = data.mike;
    });
    cy.fixture('viewport').then(({HD}) => {
      cy.viewport(HD.width, HD.height);
    });
  });

  describe('create snapshot with leave period', function () {
    it('create snapshot with leave period', function () {
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
  });

  describe('create snapshot with employees', function () {
    it('Add employee1', function () {
      cy.task('db:restore', {name: 'leaveType'});
      cy.loginTo(user.admin, '/pim/addEmployee');
      cy.getOXD('form').within(() => {
        cy.get(
          '.--name-grouped-field > :nth-child(1) > :nth-child(2) > .oxd-input',
        ).type('John');
        cy.get(':nth-child(2) > :nth-child(2) > .oxd-input').type('Paul');
        cy.get(':nth-child(3) > :nth-child(2) > .oxd-input').type('Perera');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@addEmployee').then(function () {
        cy.task('db:snapshot', {name: 'employeejohn'});
      });
    });

    it('Add employee2', function () {
      cy.task('db:restore', {name: 'leaveType'});
      cy.loginTo(user.admin, '/pim/addEmployee');
      cy.getOXD('form').within(() => {
        cy.get(
          '.--name-grouped-field > :nth-child(1) > :nth-child(2) > .oxd-input',
        ).type('Mike');
        cy.get(':nth-child(2) > :nth-child(2) > .oxd-input').type('Peter');
        cy.get(':nth-child(3) > :nth-child(2) > .oxd-input').type('Combas');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@addEmployee').then(function () {
        cy.task('db:snapshot', {name: 'employeemike'});
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
  });

  describe('create snapshots with users', function () {
    it('Add ESS user', function () {
      cy.task('db:restore', {name: 'employeemike'});
      cy.loginTo(user.admin, '/admin/saveSystemUser');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('User Role').selectOption('ESS');
        cy.getOXDInput('Employee Name').type('Mike');
        cy.getOXD('option2').contains('Mike Peter Combas').click();
        cy.getOXDInput('Status').selectOption('Enabled');
        cy.getOXDInput('Username').type('Mike22');
        cy.getOXDInput('Password').type('Mike@123');
        cy.getOXDInput('Confirm Password').type('Mike@123');
      });
      cy.wait('@getESSusername');
      cy.getOXD('button').contains('Save').click();
      cy.wait('@postESSuser').then(function () {
        cy.task('db:snapshot', {name: 'mikeESSuser'});
      });
    });

    it('Add supervisor user', function () {
      cy.task('db:restore', {name: 'employeejohn'});
      cy.loginTo(user.admin, '/admin/saveSystemUser');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('User Role').selectOption('ESS');
        cy.getOXDInput('Employee Name').type('John');
        cy.getOXD('option2').contains('John Paul Perera').click();
        cy.getOXDInput('Status').selectOption('Enabled');
        cy.getOXDInput('Username').type('John22');
        cy.getOXDInput('Password').type('John@123');
        cy.getOXDInput('Confirm Password').type('John@123');
      });
      cy.wait('@getSupusername');
      cy.getOXD('button').contains('Save').click();
      cy.wait('@postESSuser').then(function () {
        cy.task('db:snapshot', {name: 'johnsupervisoruser'});
      });
    });

    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('Add subordinate to supervisor', function () {
      cy.task('db:restore', {name: 'johnsupervisoruser'});
      cy.loginTo(user.admin, '/pim/viewReportToDetails/empNumber/2');
      cy.get(
        ':nth-child(3) > :nth-child(1) > .orangehrm-action-header > .oxd-button',
      ).click();
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type('Jacqueline');
        cy.getOXD('option2').contains('Jacqueline White').click();
        cy.getOXDInput('Reporting Method').selectOption('Direct');
      });
      cy.getOXD('button').contains('Save').click();
      cy.wait('@postSubordinate').then(function () {
        cy.task('db:snapshot', {name: 'johnwithsubordinate'});
      });
    });
  });

  describe('create snapshot with entitlements', function () {
    it('Add an entitlement to a single user', function () {
      //cy.task('db:restore', {name: 'johnwithsubordinate'});
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
      cy.task('db:restore', {name: 'mikeESSuser'});
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
  });

  describe('Create snapshot with apply leave', function () {
    it('Apply leave', function () {
      cy.task('db:restore', {name: 'addentitlementtosingleemp'});
      cy.loginTo(user.admin, '/leave/applyLeave');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Leave Type').selectOption('Annual Leave');
        cy.getOXDInput('From Date').type('2022-08-25');
        cy.getOXD('button').contains('Apply').click();
      });
      cy.wait('@applyLeave');
      cy.toast('success', 'Successfully Saved').then(function () {
        cy.task('db:snapshot', {name: 'applyleave'});
      });
    });
  });

  describe('Filter employee entitlements and list view', function () {
    it('Filter entitlement of a employee with entitlements', function () {
      cy.task('db:restore', {name: 'addentitlementtosingleemp'});
      cy.loginTo(user.admin, '/leave/viewLeaveEntitlements');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Employee Name').type('Jacqueline');
        cy.getOXD('option2').contains('Jacqueline White').click();
        cy.getOXD('button').contains('Search').click();
      });
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Total 10.00 Day(s)',
      );
      cy.get('.orangehrm-horizontal-padding > .oxd-text').should(
        'include.text',
        '(1) Record Found',
      );
      cy.get('.oxd-table-row > :nth-child(2) > div').should(
        'include.text',
        this.strings.leaveTypes.leavetype1,
      );
      cy.get('.oxd-table-row > :nth-child(3) > div').should(
        'include.text',
        'Added',
      );
      cy.get(':nth-child(4) > div').should('include.text', '2022-01-01');
      cy.get(':nth-child(5) > div').should('include.text', '2022-12-31');
      cy.get(':nth-child(6) > div').should('include.text', '10');
    });

    it('Filter entitlement of a employee without entitlements', function () {
      cy.task('db:restore', {name: 'leavePeriods'});
      cy.loginTo(user.admin, '/leave/viewLeaveEntitlements');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Employee Name').type('Jacqueline');
        cy.getOXD('option2').contains('Jacqueline White').click();
        cy.getOXD('button').contains('Search').click();
      });
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Total 0.00 Day(s)',
      );
      cy.get('.orangehrm-horizontal-padding > .oxd-text').should(
        'include.text',
        'No Records Found',
      );
    });

    it('Filter field validations', function () {
      cy.task('db:restore', {name: 'leavePeriods'});
      cy.loginTo(user.admin, '/leave/viewLeaveEntitlements');
      cy.getOXD('form').within(() => {
        cy.getOXD('button').contains('Search').click();
        cy.getOXDInput('Employee Name').isInvalid('Required');
        //cy.getOXDInput('Leave Type').should('include.text', 'No Records Found')
      });
    });
  });

  describe('Edit and delete employee entitlements', function () {
    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('Admin ability to edit their own entitlements', function () {
      cy.task('db:restore', {name: 'addentitlementtoallemp'});
      cy.loginTo(user.admin, '/leave/editLeaveEntitlement/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Entitlement').clear().type('5.00');
      });
      cy.getOXD('button').contains('Save').click();
      //cy.wait('@putEntitlements')
      cy.toast('success', 'Successfully Updated');
    });

    it('Edit entitlements field validation', function () {
      cy.task('db:restore', {name: 'applyleave'});
      cy.loginTo(user.admin, '/leave/editLeaveEntitlement/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Entitlement').clear().type('0');
        cy.getOXDInput('Entitlement').isInvalid(
          'Used amount exceeds the current amount',
        );
        cy.getOXDInput('Entitlement').clear().isInvalid('Required');
      });
    });

    it('Delete employee entitlements', function () {
      cy.task('db:restore', {name: 'addentitlementtosingleemp'});
      cy.loginTo(user.admin, '/leave/viewMyLeaveEntitlements');
      cy.get('.oxd-table-cell-actions > :nth-child(1)').click();
      cy.get('.oxd-button--label-danger').click();
      cy.wait('@deleteEntitlements');
      cy.toast('success', 'Successfully Deleted');
    });
  });

  describe('Login as an ESS user', function () {
    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('ESS user ability to access employee entitlements', function () {
      cy.task('db:restore', {name: 'mikeESSuser'});
      cy.loginTo(user.mike, '/leave/viewLeaveEntitlements');
      cy.get('.oxd-alert-content > .oxd-text').should(
        'include.text',
        'Credential Required',
      );
    });

    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('ESS user ability to view their own entitlements', function () {
      cy.task('db:restore', {name: 'addentitlementtoallemp'});
      cy.loginTo(user.mike, '/leave/viewMyLeaveEntitlements');
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Total 10.00 Day(s)',
      );
      cy.get('.orangehrm-horizontal-padding > .oxd-text').should(
        'include.text',
        '(1) Record Found',
      );
      cy.get('.oxd-table-row > :nth-child(1) > div').should(
        'include.text',
        this.strings.leaveTypes.leavetype1,
      );
      cy.get('.oxd-table-row > :nth-child(2) > div').should(
        'include.text',
        'Added',
      );
      cy.get('.oxd-table-row > :nth-child(3) > div').should(
        'include.text',
        '2022-01-01',
      );
      cy.get(':nth-child(4) > div').should('include.text', '2022-12-31');
      cy.get(':nth-child(5) > div').should('include.text', '10');
    });

    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('ESS user ability to filter entitlements', function () {
      cy.task('db:restore', {name: 'addentitlementtoallemp'});
      cy.loginTo(user.mike, '/leave/viewMyLeaveEntitlements');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Leave Type').selectOption(
          this.strings.leaveTypes.leavetype1,
        );
        cy.getOXD('button').contains('Search').click();
      });
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Total 10.00 Day(s)',
      );
      cy.get('.orangehrm-horizontal-padding > .oxd-text').should(
        'include.text',
        '(1) Record Found',
      );
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Leave Period').selectOption('2023-01-01 - 2023-12-31');
      });
      cy.getOXD('button').contains('Search').click();
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Total 0.00 Day(s)',
      );
      cy.get('.orangehrm-horizontal-padding > .oxd-text').should(
        'include.text',
        'No Records Found',
      );
    });

    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('ESS user ability to edit their own entitlements', function () {
      cy.task('db:restore', {name: 'addentitlementtoallemp'});
      cy.loginTo(user.mike, '/leave/editLeaveEntitlement/1');
      cy.get('.oxd-alert-content > .oxd-text').should(
        'include.text',
        'Credential Required',
      );
    });
  });
});

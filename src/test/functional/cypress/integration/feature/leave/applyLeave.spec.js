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

describe('Leave - Apply Leave', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/leave/holidays*').as('getHoliday');
    cy.intercept('PUT', '**/api/v2/leave/leave-period').as('putLeavePeriod');
    cy.intercept('GET', '**/api/v2/leave/leave-period').as('getLeavePeriod');
    cy.intercept('GET', '**/api/v2/leave/leave-types*').as('getLeaveTypes');
    cy.intercept('POST', '**/api/v2/leave/leave-types').as('saveLeaveType');
    cy.intercept('GET', '**/api/v2/leave/leave-entitlements*').as(
      'getLeaveEntitlements',
    );
    cy.intercept('POST', '**/api/v2/leave/leave-entitlements').as(
      'saveLeaveEntitlements',
    );
    cy.intercept('POST', '**/api/v2/leave/leave-requests').as(
      'postLeaveRequest',
    );
    cy.intercept('GET', '**/api/v2/admin/users').as('getUser');
    cy.intercept('GET', '**/api/v2/pim/employees').as('getEmployee');
    cy.intercept('POST', '**/api/v2/leave/holidays').as('postHolidays');
    cy.intercept('POST', '**/api/v2/pim/employees').as('saveEmployee');
    cy.intercept('POST', '**/api/v2/admin/users').as('saveUser');
    cy.intercept('GET', '**/api/v2/admin/validation/user-name*').as(
      'userNameValidation',
    );
    cy.fixture('user').then((data) => {
      this.adminUser = data.admin;
      this.essUser = data.john;
    });
  });

  //Creating snapshots for applying leave
  describe('create snapshots', function () {
    it('create snapshot with leaveperiod', function () {
      cy.loginTo(user.admin, '/leave/defineLeavePeriod');
      cy.wait('@getLeavePeriod');
      cy.getOXD('form').within(() => {
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putLeavePeriod').then(function () {
        cy.task('db:snapshot', {name: 'lPeriodforApplyleave'});
      });
    });
    it('create snapshot with leave type', function () {
      cy.task('db:restore', {name: 'lPeriodforApplyleave'}); // leave period is restored
      cy.loginTo(user.admin, '/leave/defineLeaveType');
      cy.wait('@getLeaveTypes');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.leaveTypes.leavetype2);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@saveLeaveType').then(function () {
        cy.task('db:snapshot', {name: 'lTypesforApplyleave'});
      });
    });
    it('create snapshot with holiday', function () {
      cy.task('db:restore', {name: 'lTypesforApplyleave'}); // leave period + leave type are restored
      cy.loginTo(user.admin, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars10.text);
        cy.getOXDInput('Date').type('2022-08-03');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postHolidays').then(function () {
        cy.toast('success', 'Successfully Saved');
        cy.task('db:snapshot', {name: 'holidayforleave'});
      });
    });
    it('create snapshot with leave entitlement', function () {
      cy.task('db:restore', {name: 'holidayforleave'}); // leave period + leave type + holiday are restored
      cy.loginTo(user.admin, '/leave/addLeaveEntitlement');
      cy.wait('@getLeaveTypes');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Multiple Employees').click();
        cy.getOXDInput('Leave Type').selectOption('Casual Leave');
        cy.getOXDInput('Entitlement').type('5');
        cy.getOXD('button').contains('Save').click();
      });
      cy.get(
        ':nth-child(6) > .oxd-form-actions > .oxd-button--secondary',
      ).click();
      cy.wait('@saveLeaveEntitlements').then(function () {
        cy.task('db:snapshot', {name: 'leaveEntitlements'});
      });
    });
  });

  describe('Add new employee', function () {
    it('Add Employee John Perera', function () {
      cy.task('db:restore', {name: 'leaveEntitlements'}); // leave period + leave type + holiday + admin leave entitlments are restored
      cy.loginTo(user.admin, '/pim/addEmployee');
      cy.wait('@getUser');
      cy.wait('@getEmployee');
      cy.getOXD('form').within(() => {
        cy.get('.orangehrm-firstname').type('John');
        cy.get('.orangehrm-lastname').type('Perera');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@saveEmployee');
      cy.task('db:snapshot', {name: 'ESSEmployee'});
    });
  });

  describe('Add new user', function () {
    it('Add John Perera as ESS user', function () {
      cy.task('db:restore', {name: 'ESSEmployee'});
      cy.loginTo(user.admin, '/admin/saveSystemUser');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('User Role').selectOption('ESS');
        cy.getOXDInput('Employee Name').type('John');
        cy.getOXD('autoCompleteOption').contains('John Perera').click();
        cy.getOXDInput('Status').selectOption('Enabled');
        cy.getOXDInput('Username').type('John22');
        cy.getOXDInput('Password').type('John@123');
        cy.getOXDInput('Confirm Password').type('John@123');
      });
      cy.wait('@userNameValidation');
      cy.getOXD('button').contains('Save').click();
      cy.wait('@saveUser');
      cy.task('db:snapshot', {name: 'ESSUser'});
    });
  });

  //Add leave entitlement for ess user
  describe('Add an entitlement to ESS user', function () {
    it('Add 5 Casual leaves for John Perera', function () {
      cy.task('db:restore', {name: 'ESSUser'});
      cy.loginTo(user.admin, '/leave/addLeaveEntitlement');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Employee Name')
          .type('John')
          .selectOption('John Perera');
        cy.getOXDInput('Leave Type').selectOption('Casual Leave');
        cy.getOXDInput('Entitlement').type('5');
        cy.getOXD('button').contains('Save').click();
      });
      cy.get('.orangehrm-modal-footer > .oxd-button--secondary').click();
      cy.wait('@saveLeaveEntitlements').then(function () {
        cy.task('db:snapshot', {name: 'ESSleaveEntitlements'});
      });
    });
  });

  //Apply leave when no leave types are defined
  describe('Apply leave when no leave types are defined', function () {
    it('Apply leave when no leave types are defined', function () {
      cy.task('db:restore', {name: 'lPeriodforApplyleave'});
      cy.loginTo(user.admin, '/leave/applyLeave');
      cy.getOXD('subTitle').should(
        'include.text',
        'No Leave Types with Leave Balance',
      );
    });
  });

  // eslint-disable-next-line jest/no-disabled-tests
  describe.skip('skipped for bug fixing', () => {
    //Form Validations
    describe('Apply leave-form validations', function () {
      it('Apply leave-form validations', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXD('button').contains('Apply').click();
          cy.getOXDInput('Leave Type').isInvalid('Required');
          cy.getOXDInput('From Date').isInvalid('Required');
          cy.getOXDInput('To Date').isInvalid('Required');
          cy.getOXDInput('Comments')
            .type(this.strings.chars400.text)
            .isInvalid('Should not exceed 250 characters');
        });
      });
      it('Apply leave-Date field validations', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('From Date').type('2022-07-25');
          cy.getOXDInput('To Date').clear().type('2022-07-23');
          cy.getOXD('button').contains('Apply').click();
          cy.getOXDInput('To Date').isInvalid(
            'To date should be after from date',
          );
        });
      });
    });

    describe('Apply leave as Admin User for a single day ', function () {
      it('Apply full day leave with a comment', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('From Date').type('2022-08-11');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('success', 'Successfully Saved');
      });
      it('Apply half day leave with a comment', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('From Date').type('2022-08-11');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXDInput('Duration').selectOption('Half Day - Afternoon');
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('success', 'Successfully Saved');
      });
      it('Apply leave for a specific time', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('From Date').type('2022-08-11');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXDInput('Duration').selectOption('Specify Time');
          cy.get(
            ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
          )
            .clear()
            .type('01:00 PM');
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('success', 'Successfully Saved');
      });
    });

    //Apply leave for multiple days
    describe('Apply leave for multiple days ', function () {
      it('Apply full day leave for multiple days with a comment', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('From Date').type('2022-08-10');
          cy.getOXDInput('To Date').clear().type('2022-08-12');
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('success', 'Successfully Saved');
      });
      it('Apply partial day leave when applying leave for multiple days with a comment', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('From Date').type('2022-08-10');
          cy.getOXDInput('To Date').clear().type('2022-08-12');
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('Partial Days').selectOption('All Days');
          cy.getOXDInput('Duration').selectOption('Half Day - Morning');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('success', 'Successfully Saved');
      });
      it('Apply leave for multiple days for a specific time of the start day', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('From Date').type('2022-08-10');
          cy.getOXDInput('To Date').clear().type('2022-08-12');
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('Partial Days').selectOption('Start Day Only');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXDInput('Start Day').selectOption('Specify Time');
          cy.get(
            ':nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
          )
            .clear()
            .type('01:00 PM');
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('success', 'Successfully Saved');
      });
    });

    //Applying leave on non-working days and holidays
    describe('Apply leave non-working days and holidays ', function () {
      it('Apply leave on a non-working day', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('From Date').type('2022-08-06');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('error', 'Failed to Submit: No Working Days Selected');
      });
      it('Apply leave on a holiday', function () {
        cy.task('db:restore', {name: 'holidayforleave'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('From Date').type('2022-08-03');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('error', 'Failed to Submit: No Working Days Selected');
      });
    });

    //Verifying overlapping leave requests
    describe('Verifying overlapping leave requests ', function () {
      it('Creating snapshot with a leave', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('From Date').type('2022-08-03');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.task('db:snapshot', {name: 'appliedleave'});
      });
      it('Verify overlapping leave requests', function () {
        cy.task('db:restore', {name: 'appliedleave'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('From Date').type('2022-08-03');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXD('button').contains('Apply').click();
        });
        cy.toast('warn', 'Failed to Submit');
        cy.getOXD('pageTitle').should(
          'include.text',
          'Overlapping Leave Request(s) Found',
        );
      });
    });

    //Leave balance calculation
    describe('Verifying Leave balance calculation ', function () {
      it('Verify ability to open and close leave balance modal', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.get('.orangehrm-leave-balance > .oxd-icon').click();
          cy.getOXD('pageTitle').contains('Leave Balance Details');
          cy.get(':nth-child(6) > .oxd-form-actions > .oxd-button').click();
        });
      });
      it('Creating snapshot with a leave to verify leave balance', function () {
        cy.task('db:restore', {name: 'leaveEntitlements'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.get('[data-v-2fe357a6=""] > .oxd-text').contains('5.00 Day(s)');
          cy.getOXDInput('From Date').type('2022-08-03');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.task('db:snapshot', {name: 'appliedleaveforLeavebal'});
      });
      it('Verify leave balance is getting calculated correctly', function () {
        cy.task('db:restore', {name: 'appliedleaveforLeavebal'});
        cy.loginTo(user.admin, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.get('[data-v-2fe357a6=""] > .oxd-text').contains('4.00 Day(s)');
        });
      });
    });

    //Apply leave as ESS user
    describe('Apply leave as ESS User', function () {
      it('Apply full day leave with a comment as ESS user', function () {
        cy.task('db:restore', {name: 'ESSleaveEntitlements'});
        cy.loginTo(user.john, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('From Date').type('2022-08-11');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('success', 'Successfully Saved');
      });
      it('Apply half day leave with a comment as ESS user', function () {
        cy.task('db:restore', {name: 'ESSleaveEntitlements'});
        cy.loginTo(user.john, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('From Date').type('2022-08-11');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXDInput('Duration').selectOption('Half Day - Afternoon');
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('success', 'Successfully Saved');
      });
      it('Apply leave for a specific time as ESS user', function () {
        cy.task('db:restore', {name: 'ESSleaveEntitlements'});
        cy.loginTo(user.john, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('From Date').type('2022-08-11');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXDInput('Duration').selectOption('Specify Time');
          cy.get(
            ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
          )
            .clear()
            .type('01:00 PM');
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('success', 'Successfully Saved');
      });
      it('Apply leave on multiple days with a comment as ESS user', function () {
        cy.task('db:restore', {name: 'ESSleaveEntitlements'});
        cy.loginTo(user.john, '/leave/applyLeave');
        cy.getOXD('form').within(() => {
          cy.getOXDInput('From Date').type('2022-08-10');
          cy.getOXDInput('To Date').clear().type('2022-08-12');
          cy.getOXDInput('Leave Type').selectOption('Casual Leave');
          cy.getOXDInput('Comments').type(this.strings.chars100.text);
          cy.getOXD('button').contains('Apply').click();
        });
        cy.wait('@postLeaveRequest');
        cy.toast('success', 'Successfully Saved');
      });
    });
  });
});

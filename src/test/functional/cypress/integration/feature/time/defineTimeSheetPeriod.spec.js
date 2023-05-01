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

describe('Time - Define Time Sheet Period', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('viewport').then(({HD}) => {
      cy.viewport(HD.width, HD.height);
    });
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/time/time-sheet-period').as(
      'getTimeSheetPeriod',
    );
    cy.intercept('GET', '**api/v2/time/employees/timesheets').as(
      'getEmployeeTimeSheet',
    );
    cy.intercept('PUT', '**/api/v2/time/time-sheet-period').as(
      'updateTimeSheetPeriod',
    );
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  describe('Before defining time sheet period', function () {
    it('Verify the define timesheet period screen appear first time when admin goes to the Time module', function () {
      cy.loginTo(this.user, '/time/viewEmployeeTimesheet');
      cy.wait('@getTimeSheetPeriod');
      cy.get('.orangehrm-main-title').contains('Define Timesheet Period');
    });
    it('Verify saving without selecting a day from the dropdown menu.', function () {
      cy.loginTo(this.user, '/time/viewEmployeeTimesheet');
      cy.wait('@getTimeSheetPeriod');
      cy.getOXDInput('First Day of the Week').selectOption('-- Select --');
      cy.get('.oxd-input-group > .oxd-text').contains('Required');
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
    });

    it('Verify getting navigated to employee timesheet screen after saving the timesheet period.', function () {
      cy.loginTo(this.user, '/time/viewEmployeeTimesheet');
      cy.wait('@getTimeSheetPeriod');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('First Day of the Week').selectOption('Tuesday');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateTimeSheetPeriod');
      cy.toast('success', 'Successfully Saved');
      cy.get('.orangehrm-card-container > .oxd-text--h6').contains(
        'Select Employee',
      );
    });
  });

  describe('Verifying Page Headers and Titles', function () {
    it('Verify Page Title', function () {
      cy.loginTo(this.user, '/time/viewEmployeeTimesheet');
      cy.wait('@getTimeSheetPeriod');
      cy.get('.orangehrm-main-title').contains('Define Timesheet Period');
    });
    it('Verify Page title', function () {
      cy.loginTo(this.user, '/time/viewEmployeeTimesheet');
      cy.wait('@getTimeSheetPeriod');
      cy.get('.oxd-topbar-header-breadcrumb-module').contains('Time');
    });
  });
});

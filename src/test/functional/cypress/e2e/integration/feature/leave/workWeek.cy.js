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

describe('Leave- Configure - Work Week', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('viewport').then(({HD}) => {
      cy.viewport(HD.width, HD.height);
    });
    cy.intercept('GET', '**/api/v2/leave/workweek*').as('getWorkWeek');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Read
  describe('View Work Week', function () {
    it('Verify work week is loaded', function () {
      cy.loginTo(this.user, '/leave/defineWorkWeek');
      cy.wait('@getWorkWeek');
      cy.getOXD('pageTitle').should('include.text', 'Work Week');
    });
  });

  //Update
  describe('Update Work Week', function () {
    it('Update Work week with different combinations', function () {
      cy.loginTo(this.user, '/leave/defineWorkWeek');
      cy.wait('@getWorkWeek');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Monday').selectOption('Half Day');
        cy.getOXDInput('Wednesday').selectOption('Non-working Day');
        cy.getOXDInput('Sunday').selectOption('Full Day');
        cy.getOXD('button').contains('Save').click();
      });
      cy.toast('success', 'Successfully Saved');
    });

    it('Update Work week to all Half Days', function () {
      cy.loginTo(this.user, '/leave/defineWorkWeek');
      cy.wait('@getWorkWeek');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Monday').selectOption('Half Day');
        cy.getOXDInput('Tuesday').selectOption('Half Day');
        cy.getOXDInput('Wednesday').selectOption('Half Day');
        cy.getOXDInput('Thursday').selectOption('Half Day');
        cy.getOXDInput('Friday').selectOption('Half Day');
        cy.getOXDInput('Saturday').selectOption('Half Day');
        cy.getOXDInput('Sunday').selectOption('Half Day');
        cy.getOXD('button').contains('Save').click();
      });
      cy.toast('success', 'Successfully Saved');
    });
  });

  //Validation
  describe('Work Week- Validations', function () {
    it('Required Validation', function () {
      cy.loginTo(this.user, '/leave/defineWorkWeek');
      cy.wait('@getWorkWeek');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Monday').selectOption('-- Select --');
        cy.getOXDInput('Monday').isInvalid('Required');
      });
    });

    it('Work week  with all Non Working Days Validation', function () {
      cy.loginTo(this.user, '/leave/defineWorkWeek');
      cy.wait('@getWorkWeek');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Monday').selectOption('Non-working Day');
        cy.getOXDInput('Tuesday').selectOption('Non-working Day');
        cy.getOXDInput('Wednesday').selectOption('Non-working Day');
        cy.getOXDInput('Thursday').selectOption('Non-working Day');
        cy.getOXDInput('Friday').selectOption('Non-working Day');
        cy.getOXDInput('Saturday').selectOption('Non-working Day');
        cy.getOXDInput('Sunday').selectOption('Non-working Day');
        cy.getOXD('button').contains('Save').click();
      });
      cy.toast('warn', 'At least one day should be a working day');
    });
  });
});

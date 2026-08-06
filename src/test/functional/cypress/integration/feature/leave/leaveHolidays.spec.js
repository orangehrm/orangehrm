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

describe('Leave - Holidays', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('viewport').then(({HD}) => {
      cy.viewport(HD.width, HD.height);
    });
    cy.fixture('chars').as('strings');
    cy.intercept('PUT', '**/api/v2/leave/leave-period').as('putLeavePeriod');
    cy.intercept('GET', '**/api/v2/leave/leave-period').as('getLeavePeriod');
    cy.intercept('GET', '**/api/v2/leave/holidays*').as('getHolidays');
    cy.intercept('POST', '**/api/v2/leave/holidays').as('postHolidays');
    cy.intercept('PUT', '**/api/v2/leave/holidays/*').as('putHolidays');
    cy.intercept('DELETE', '**/api/v2/leave/holidays').as('deleteHolidays');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  describe('create snapshot with leave period', function () {
    it('create snapshot with leave period', function () {
      cy.loginTo(this.user, '/leave/defineLeavePeriod');
      cy.getOXD('form').within(() => {
        cy.wait('@getLeavePeriod');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putLeavePeriod').then(function () {
        cy.task('db:snapshot', {name: 'leavePeriod'});
      });
    });
  });

  describe('Verify admin ability to add holidays', function () {
    it('Verify admin ability to add holidays and toast message', function () {
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars10.text);
        cy.get('.oxd-date-input > .oxd-input').type('2023-01-12');
        cy.getOXDInput('Full Day/ Half Day').selectOption('Full Day');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postHolidays');
      cy.toast('success', 'Successfully Saved');
    });

    it('Verify adding holiday without selecting all required fields', function () {
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('button').contains('Save').click();
      cy.get(':nth-child(1) > .oxd-input-group > .oxd-text')
        .should('be.visible')
        .and('have.text', 'Required');
      cy.get(':nth-child(2) > .oxd-input-group > .oxd-text')
        .should('be.visible')
        .and('have.text', 'Required');
    });

    it('Verify adding holiday with only selecting all required fields', function () {
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars10.text);
        cy.get('.oxd-date-input > .oxd-input').type('2021-10-03');
        cy.getOXDInput('Full Day/ Half Day').selectOption('Full Day');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postHolidays');
      cy.toast('success', 'Successfully Saved');
    });

    it('Verify admin ability to add half day holidays', function () {
      cy.task('db:restore', {name: 'leavePeriod'});
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.get('.oxd-date-input > .oxd-input').type('2023-10-10');
        cy.getOXDInput('Full Day/ Half Day').selectOption('Half Day');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postHolidays');
      cy.toast('success', 'Successfully Saved');
      cy.task('db:snapshot', {name: 'leaveHoliday'});
    });

    it('Verify whether list count increment when a new holiday is added', function () {
      cy.task('db:restore', {name: 'leaveHoliday'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.wait('@getHolidays');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(1) Record Found',
      );
    });

    it('Verify text field max length validation', function () {
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .type(this.strings.chars250.text)
          .isInvalid('Should not exceed 200 characters');
      });
    });

    it('Verify whether perviously added holidays are displaying in the date picker', function () {
      cy.task('db:restore', {name: 'leaveHoliday'});
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.get('.oxd-date-input > .oxd-input').type('2023-10-10');
        cy.getOXDInput('Full Day/ Half Day').selectOption('Half Day');
        cy.getOXD('button').contains('Save').click();
        cy.get('.oxd-input-group > .oxd-text').contains('Already exists');
      });
    });

    it('Verify admin ability to select Repeats Annually yes/ no', function () {
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.get('.oxd-date-input > .oxd-input').type('2021-10-10');
        cy.getOXDInput('Full Day/ Half Day').selectOption('Half Day');
        cy.get(
          ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
        ).click();
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postHolidays');
      cy.toast('success', 'Successfully Saved');
    });

    it('Verify adding multiple leaves on the same date', function () {
      cy.task('db:restore', {name: 'leaveHoliday'});
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.get('.oxd-date-input > .oxd-input').type('2023-10-10');
        cy.getOXD('button').contains('Save').click();
        cy.get('.oxd-input-group > .oxd-text').contains('Already exists');
      });
    });
  });

  describe('update holiday', function () {
    it('update holiday', function () {
      cy.task('db:restore', {name: 'leaveHoliday'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.wait('@getHolidays');
      cy.get('.oxd-table-cell-actions > :nth-child(2)').click();
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type(this.strings.chars30.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putHolidays');
      cy.toast('success', 'Successfully Updated');
    });

    it('Verify removing required fields in editing', function () {
      cy.task('db:restore', {name: 'leaveHoliday'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.wait('@getHolidays');
      cy.get('.oxd-table-cell-actions > :nth-child(2)').click();
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().setValue('').isInvalid('Required');
      });
    });

    it('Verify admin ability to edit Repeats Annually yes/ no', function () {
      cy.task('db:restore', {name: 'leaveHoliday'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.wait('@getHolidays');
      cy.get('.oxd-table-cell-actions > :nth-child(2)').click();
      cy.getOXD('form').within(() => {
        cy.get(
          ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
        ).click();
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putHolidays');
      cy.toast('success', 'Successfully Updated');
    });
  });

  describe('delete holiday', function () {
    it('delete holiday', function () {
      cy.task('db:restore', {name: 'leaveHoliday'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.get('.oxd-table-cell-actions > :nth-child(1)').click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@deleteHolidays');
      cy.toast('success', 'Successfully Deleted');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(4000);
    });

    it('Bulk Delete Holidays', function () {
      cy.task('db:restore', {name: 'leaveHoliday'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.get('.oxd-table-header .oxd-checkbox-input').click();
      cy.getOXD('button').contains('Delete Selected').click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@deleteHolidays');
      cy.toast('success', 'Successfully Deleted');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(4000);
    });

    it('cancel button behaviour on delete Holidays and Verify page header', function () {
      cy.task('db:restore', {name: 'leaveHoliday'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.get('.oxd-table-cell-actions > :nth-child(1) > .oxd-icon').click();
      cy.getOXD('button').contains('No, Cancel').click();
      cy.wait('@getHolidays');
      cy.scrollTo('top');
      cy.get('.oxd-table-filter-header-title > .oxd-text').contains('Holidays');
    });
  });

  describe('testing other scenarios', function () {
    it('Verify reset button', function () {
      cy.task('db:restore', {name: 'leavePeriod'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.wait('@getHolidays');
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .clear()
        .type('2023-11-10');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .clear()
        .type('2023-11-11');
      cy.getOXD('button').contains('Reset').click();
      cy.wait('@getHolidays');
    });

    it('Verify before date for "to" field', function () {
      cy.task('db:restore', {name: 'leavePeriod'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.wait('@getHolidays');
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .clear()
        .type('2023-11-10');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .clear()
        .type('2023-11-09');
      cy.getOXD('button').contains('Search').click();
      cy.get('.oxd-input-group > .oxd-text').contains(
        'To date should be after from date',
      );
    });
  });
});

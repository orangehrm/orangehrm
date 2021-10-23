import user from '../../../fixtures/user.json';
import {AddUsers} from '../../../fixtures/add-users.spec.js';

describe('Leave- Apply Leave test script', function () {
  context('Add Users', function () {
    AddUsers();
  });

  describe('Verify leave type drop down when no leave types are added', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.jane.userName, user.jane.password, '/leave/applyLeave');
    });
    it('Verify leave type drop down when no leave types are added.', function () {
      cy.get('.orangehrm-card-container > .oxd-text--p').should(
        'include.text',
        'No Leave Types with Leave Balance',
      );
    });
  });
  describe('Configuring the system', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.jane.userName, user.jane.password, '/leave/applyLeave');
    });
    it('Setting the work week', function () {
      cy.visit('/leave/defineWorkWeek');
      let i = 1;
      for (i = 1; i < 8; i++) {
        cy.get(
          '.oxd-form > :nth-child(' +
            i +
            ') > .oxd-grid-4 > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
        ).click();
        if (i < 6) {
          cy.get(
            '.oxd-form > :nth-child(' +
              i +
              ') > .oxd-grid-4 > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
          )
            .contains('Full Day')
            .click();
        } else {
          cy.get(
            '.oxd-form > :nth-child(' +
              i +
              ') > .oxd-grid-4 > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
          )
            .contains('Non-working Day')
            .click();
        }
      }
      cy.get('.oxd-button').click();
    });
    it('add new leave types', () => {
      cy.visit('/leave/leaveTypeList');
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('1---test');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
    });
    it('add entitlements', () => {
      cy.visit('/leave/addLeaveEntitlement');
      cy.get(
        ':nth-child(2) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get(
        ':nth-child(3) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        ':nth-child(3) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(3) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        ':nth-child(3) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('2021-01-01 - 2021-12-31')
        .click();
      cy.get(':nth-child(2) > .oxd-input').type('3');
      cy.get('.oxd-button--secondary').click();
      cy.get(
        ':nth-child(6) > .oxd-form-actions > .oxd-button--secondary',
      ).click();
    });
  });
  describe('Text field validations', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.jane.userName, user.jane.password, '/leave/applyLeave');
    });
    it('required field validation-Leave Type', () => {
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-icon',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(2) > div > div:nth-child(1) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(13)',
      ).click();
      cy.get('.oxd-button').click();
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });
    it('required field validation-Date', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get('.oxd-button').click();
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });
  });
  describe('Balance details modal', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.jane.userName, user.jane.password, '/leave/applyLeave');
    });
    it('Display Leave Balance Details Modal', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get('.orangehrm-leave-balance > .oxd-icon').click();
      cy.get('.orangehrm-header-container > .oxd-text--h6').should(
        'include.text',
        'Leave Balance Details',
      );
    });
  });
  describe('Applying Leave,Leave Balance Calculation,Leave Overlap & Insufficient Balance', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.jane.userName, user.jane.password, '/leave/applyLeave');
    });
    it('Apply Leave, check toast & Leave Balance calculation- Full Day', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').should(
        'include.text',
        '3.00 Day(s)',
      );
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-15');
      cy.get('.oxd-button').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.visit('/leave/applyLeave');
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').should(
        'include.text',
        '2.00 Day(s)',
      );
    });
    it('Overlapping Leave-Failed to submit toast', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-15');
      cy.get('.oxd-button').click();
      cy.get('.oxd-toast').should('include.text', 'Failed to Submit');
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Overlapping Leave Request(s) Found',
      );
    });
    it('Balance Not suffient vallidation', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-16');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-19');
      cy.get('.oxd-button').click();
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').should(
        'include.text',
        'Balance not sufficient',
      );
    });
    after(() => {
      cy.visit('/leave/viewMyLeaveList');
      cy.get('.oxd-multiselect-chips-area > :nth-child(1) > .oxd-icon').click();
      cy.get('.oxd-multiselect-chips-area > :nth-child(1) > .oxd-icon').click();
      cy.get('.oxd-multiselect-chips-area > :nth-child(2) > .oxd-icon').click();
      cy.get('.oxd-multiselect-chips-area > :nth-child(2) > .oxd-icon').click();
      cy.get('.oxd-form-actions > .oxd-button').click();
      cy.get('.oxd-table-cell-actions > .oxd-button').click();
    });
  });

  describe('Date Vallidation', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.jane.userName, user.jane.password, '/leave/applyLeave');
    });
    it('From date-vallidation', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-26');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-25');
      cy.get('.oxd-button').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'To date should be after from date',
      );
    });
    it('Verify selecting dates outside current leave period', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2022-01-10');
      cy.get('.oxd-button').click();
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').should(
        'include.text',
        'Balance not sufficient',
      );
      cy.get('.oxd-toast').should('include.text', 'Leave Balance Exceeded');
    });
    it('Verify selecting a Non-working day/holiday', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-23');
      cy.get('.oxd-button').click();
      cy.get('.oxd-text--toast-message').should(
        'include.text',
        'Failed to Submit: No Working Days Selected',
      );
    });
  });

  describe('Specific Time Vallidation', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.jane.userName, user.jane.password, '/leave/applyLeave');
    });
    it('From/ to time-vallidation', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-25');
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Specify Time')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear()
        .type('09:00 AM');
      cy.get(
        ':nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear()
        .type('08:00 AM');
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'To time should be after from time',
      );
    });
    it('Required-validation', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-25');
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Specify Time')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear();
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input-group > .oxd-text').should(
        'include.text',
        'Required',
      );
    });
    it('Duration Calculation', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-25');
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Specify Time')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear()
        .type('09:00 AM');
      cy.get(
        ':nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear()
        .type('09:00 PM');
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-text',
      ).should('include.text', '12.00');
    });
    it('Apply Leave More than Work shift', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-25');
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Specify Time')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear()
        .type('09:00 AM');
      cy.get(
        ':nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear()
        .type('07:00 PM');
      cy.get('.oxd-button').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Duration should be less than work shift length',
      );
    });
  });
  describe('Applying Leave for multiple days', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.jane.userName, user.jane.password, '/leave/applyLeave');
    });
    it('Display Partial Days Field', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-26');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-29');
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').click();
      cy.get(
        ':nth-child(3) > .oxd-grid-4 > [data-v-e99d4118-s=""] > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label',
      ).should('include.text', 'Partial Days');
    });
    it('Display Duration Field & Required validation', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-26');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-29');
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').click();
      cy.get(
        '.oxd-grid-4 > [data-v-e99d4118-s=""] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        '.oxd-grid-4 > [data-v-e99d4118-s=""] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('All Days')
        .click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label',
      ).should('include.text', 'Duration');
      cy.get('.oxd-button').click();
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });
    it('Display Start Day/End Day Field & Required validation', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-26');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-29');
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').click();
      cy.get(
        '.oxd-grid-4 > [data-v-e99d4118-s=""] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        '.oxd-grid-4 > [data-v-e99d4118-s=""] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Start Day Only')
        .click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label',
      ).should('include.text', 'Start Day');
      cy.get('.oxd-button').click();
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });
  });

  describe('Apply Leave as ESS', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.john.userName, user.john.password, '/leave/applyLeave');
    });
    it('Apply Leave as ESS & check toast', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-15');
      cy.get('.oxd-button').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
    it('Verify Overlap Leave & toast for ESS', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-15');
      cy.get('.oxd-button').click();
      cy.get('.oxd-toast').should('include.text', 'Failed to Submit');
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Overlapping Leave Request(s) Found',
      );
    });
    it('Verify Balance Not suffient vallidation for ESS', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-16');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-19');
      cy.get('.oxd-button').click();
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').should(
        'include.text',
        'Balance not sufficient',
      );
    });
    it('Verify applying Leave More than Work shift for ESS Users', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-25');
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Specify Time')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear()
        .type('09:00 AM');
      cy.get(
        ':nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear()
        .type('07:00 PM');
      cy.get('.oxd-button').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Duration should be less than work shift length',
      );
    });
    after(() => {
      cy.visit('/leave/viewMyLeaveList');
      cy.get('.oxd-multiselect-chips-area > :nth-child(1) > .oxd-icon').click();
      cy.get('.oxd-multiselect-chips-area > :nth-child(1) > .oxd-icon').click();
      cy.get('.oxd-multiselect-chips-area > :nth-child(2) > .oxd-icon').click();
      cy.get('.oxd-multiselect-chips-area > :nth-child(2) > .oxd-icon').click();
      cy.get('.oxd-form-actions > .oxd-button').click();
      cy.get('.oxd-table-cell-actions > .oxd-button').click();
    });
  });

  describe('Apply Leave as Superviser', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.mike.userName, user.mike.password, '/leave/applyLeave');
    });
    it('Apply Leave as Superviser & check toast', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-15');
      cy.get('.oxd-button').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
    it('Verify Overlap Leave & toast for Supervisor', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-15');
      cy.get('.oxd-button').click();
      cy.get('.oxd-toast').should('include.text', 'Failed to Submit');
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Overlapping Leave Request(s) Found',
      );
    });
    it('Verify Balance Not suffient vallidation for Supervisor', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-16');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-11-19');
      cy.get('.oxd-button').click();
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').should(
        'include.text',
        'Balance not sufficient',
      );
    });
    it('Verify applying Leave More than Work shift for Supervisers', () => {
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('1---test')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-input',
      )
        .click()
        .clear()
        .type('2021-10-25');
      cy.get('[data-v-4a1cbaad=""] > .oxd-text').click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        '[style="grid-column-start: 1;"] > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Specify Time')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear()
        .type('09:00 AM');
      cy.get(
        ':nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-time-wrapper > .oxd-time-input > .oxd-input',
      )
        .click()
        .clear()
        .type('07:00 PM');
      cy.get('.oxd-button').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Duration should be less than work shift length',
      );
    });
    after(() => {
      cy.visit('/leave/viewMyLeaveList');
      cy.get('.oxd-multiselect-chips-area > :nth-child(1) > .oxd-icon').click();
      cy.get('.oxd-multiselect-chips-area > :nth-child(1) > .oxd-icon').click();
      cy.get('.oxd-multiselect-chips-area > :nth-child(2) > .oxd-icon').click();
      cy.get('.oxd-multiselect-chips-area > :nth-child(2) > .oxd-icon').click();
      cy.get('.oxd-form-actions > .oxd-button').click();
      cy.get('.oxd-table-cell-actions > .oxd-button').click();
    });
  });

  describe('UI testing', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.jane.userName, user.jane.password, '/leave/applyLeave');
    });
    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('verify Main header', () => {
      cy.visit('/leave/applyLeave');
      cy.get('.oxd-topbar-header-title > .oxd-text').should(
        'include.text',
        'Leave',
      );
    });
    it('verify header', () => {
      cy.visit('/leave/applyLeave');
      cy.get('.orangehrm-card-container > .oxd-text--h6').should(
        'include.text',
        'Apply Leave',
      );
    });
  });
  after(() => {
    cy.visit('/leave/leaveTypeList');
    cy.get(
      ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1)',
    ).click();
    cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
  });
});

import user from '../../../fixtures/admin.json';
import charLength from '../../../fixtures/charLength.json';

describe('Leave -Configure-Holidays test script', () => {
  beforeEach(() => {
    cy.viewport(1024, 768);
    cy.loginTo(
      user.admin.userName,
      user.admin.password,
      '/leave/viewHolidayList',
    );
  });

  describe('Add holiday and Duplicate record validations testing', function () {
    it('add holiday, check toast message and duplicate holiday', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('---test');
      cy.get('.oxd-date-input > .oxd-icon').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(20) > div',
      ).click();

      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2)',
      )
        .contains('Full Day')
        .click();
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.visit('/leave/viewHolidayList');
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('---test');
      cy.get('.oxd-date-input > .oxd-icon').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(20) > div',
      ).click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Already exist',
      );
    });
    after(() => {
      cy.visit('/leave/viewHolidayList');
      cy.get(
        '.oxd-table-card > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });
  describe('Text field validation testing', function () {
    it('required field verification', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });
    it('maximum length validation', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type(charLength.chars200.text + 'a');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Should be less than 200 characters',
      );
    });
  });
  describe('List count increment testing', function () {
    it('list count increment', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('---test');
      cy.get('.oxd-date-input > .oxd-icon').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(20) > div',
      ).click();

      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2)',
      )
        .contains('Full Day')
        .click();
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/viewHolidayList');
      cy.get('.orangehrm-horizontal-padding > .oxd-text')
        .contains('Found')
        .invoke('text')
        .then((line) => {
          const num = line.match(/\((.*)\)/);
          const currentHolno = parseInt(num[1]);

          cy.get('.orangehrm-header-container > .oxd-button').click();
          cy.get(':nth-child(2) > .oxd-input').type('---test2');
          cy.get('.oxd-date-input > .oxd-icon').click();
          cy.get(
            '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-header > button:nth-child(3)',
          ).click();
          cy.get(
            '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(18) > div',
          ).click();

          cy.get(
            '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2) > div',
          ).click();
          cy.get(
            '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2)',
          )
            .contains('Half Day')
            .click();
          cy.get(
            ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
          ).click();
          cy.get('.oxd-button--secondary').click();

          cy.visit('/leave/viewHolidayList');
          cy.get('.orangehrm-horizontal-padding > .oxd-text')
            .contains('Found')
            .invoke('text')
            .then((line) => {
              const num = line.match(/\((.*)\)/);
              const newHolno = parseInt(num[1]);
              expect(newHolno).to.eq(currentHolno + 1);
            });
        });
    });
    after(() => {
      cy.visit('/leave/viewHolidayList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1) > .oxd-icon',
      ).click();
      cy.get('.oxd-button--label-danger').click();
      cy.visit('/leave/viewHolidayList');
      cy.get(
        '.oxd-table-card > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });
  describe('Search holiday, Display table and Reset Button testing', function () {
    it('Search a holiday in a specific time frame & click Reset', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('---test2');
      cy.get('.oxd-date-input > .oxd-icon').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-header > button:nth-child(3)',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-header > button:nth-child(3)',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-header > button:nth-child(3)',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(15) > div',
      ).click();

      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2)',
      )
        .contains('Half Day')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/viewHolidayList');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-icon',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div.oxd-table-filter > div.oxd-table-filter-area > form > div.oxd-form-row > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-header > button:nth-child(3) > i',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div.oxd-table-filter > div.oxd-table-filter-area > form > div.oxd-form-row > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-header > button:nth-child(3) > i',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div.oxd-table-filter > div.oxd-table-filter-area > form > div.oxd-form-row > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(28) > div',
      ).click();
      /*eslint-disable */
      Cypress.on('uncaught:exception', (err, runnable) => {
        return false;
      });
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.get('.orangehrm-horizontal-padding > .oxd-text').should(
        'include.text',
        '(1) Record Found',
      );
      cy.get('.oxd-table-card > .oxd-table-row').should('be.visible');
      cy.get('.oxd-button--ghost').click();
      cy.get('.orangehrm-horizontal-padding > .oxd-text').should(
        'include.text',
        'No Records Found',
      );
      cy.get('.oxd-table-card > .oxd-table-row').should('not.exist');
    });
    after(() => {
      cy.visit('/leave/viewHolidayList');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-date-wrapper > .oxd-date-input > .oxd-icon',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div.oxd-table-filter > div.oxd-table-filter-area > form > div.oxd-form-row > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-header > button:nth-child(3) > i',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div.oxd-table-filter > div.oxd-table-filter-area > form > div.oxd-form-row > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-header > button:nth-child(3) > i',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div.oxd-table-filter > div.oxd-table-filter-area > form > div.oxd-form-row > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(28) > div',
      ).click();
      /*eslint-disable */
      Cypress.on('uncaught:exception', (err, runnable) => {
        return false;
      });
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.get(
        '.oxd-table-card > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Update holiday testing', function () {
    it('update an existing holiday and check toast message', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('---test');
      cy.get('.oxd-date-input > .oxd-icon').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(08) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2)',
      )
        .contains('Full Day')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/viewHolidayList');
      cy.get('.oxd-table-cell-actions > :nth-child(2) > .oxd-icon').click();
      cy.get(':nth-child(2) > .oxd-input').clear().type('---edittest');
      cy.get('.oxd-date-input > .oxd-icon').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-header > button:nth-child(3)',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(30) > div',
      ).click();

      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2)',
      )
        .contains('Half Day')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast-container--bottom').should(
        'include.text',
        'Successfully Updated',
      );
    });
    after(() => {
      cy.visit('/leave/viewHolidayList');
      cy.get(
        '.oxd-table-card > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });
  describe('Cancel button testing', function () {
    it('visiting edit holiday and clicking cancel', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('---test');
      cy.get('.oxd-date-input > .oxd-icon').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(08) > div',
      ).click();

      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2)',
      )
        .contains('Full Day')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/viewHolidayList');
      cy.get('.oxd-table-cell-actions > :nth-child(2) > .oxd-icon').click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.oxd-table-filter-header-title > .oxd-text').should(
        'include.text',
        'Holidays',
      );
    });
    it('visiting add new holiday and clicking cancel', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.oxd-table-filter-header-title > .oxd-text').should(
        'include.text',
        'Holidays',
      );
    });
    after(() => {
      cy.visit('/leave/viewHolidayList');
      cy.get(
        '.oxd-table-card > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Deleting a holiday testing', function () {
    it('no records found message', () => {
      cy.get('.orangehrm-horizontal-padding > .oxd-text').should(
        'include.text',
        'No Records Found',
      );
    });
    it('deleting a holiday and check the toast', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('---test');
      cy.get('.oxd-date-input > .oxd-icon').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(18) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2)',
      )
        .contains('Full Day')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/viewHolidayList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1) > .oxd-icon',
      ).click();
      cy.get('.oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
    it('bulk delete holidays and check the toast', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('---test2');
      cy.get('.oxd-date-input > .oxd-icon').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-header > button:nth-child(3)',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(2) > div > div:nth-child(2) > div > div.oxd-date-input-calendar > div > div.oxd-calendar-dates-grid > div:nth-child(18) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2) > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div:nth-child(3) > div > div:nth-child(2)',
      )
        .contains('Half Day')
        .click();
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/viewHolidayList');
      cy.get(
        '.oxd-table-card > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
  });
  describe('UI testing', function () {
    it('Add holidays page', () => {
      cy.visit('/leave/viewHolidayList');
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get('.orangehrm-card-container > .oxd-text--h6').should(
        'include.text',
        'Add Holiday',
      );
    });
    it('verify header', () => {
      cy.visit('/leave/viewHolidayList');
      cy.get('.oxd-table-filter-header-title > .oxd-text').should(
        'include.text',
        'Holidays',
      );
    });
  });
});

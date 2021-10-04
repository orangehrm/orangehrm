import user from '../../../fixtures/admin.json';
import charLength from '../../../fixtures/charLength.json';

describe('Job - PayGrade test script', function () {
  beforeEach(() => {
    cy.login(user.admin.userName, user.admin.password);
    cy.viewport(1024, 768);
    cy.visit('/admin/viewPayGrades');
  });

  describe('Add paygrade and Duplicate record validations testing', function () {
    it('add new pay grade', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA');
      cy.get('.oxd-button--secondary').click();
    });
    it('add duplicate paygrade and check validation', () => {
      cy.visit('/admin/viewPayGrades');
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Already exist',
      );
    });
    after(() => {
      cy.visit('/admin/viewPayGrades');
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Text field validation testing-paygrade', function () {
    it('required field verification', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type(' ');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });
    it('maximum length validation', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type(charLength.chars51.text);
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Should be less than 50 characters',
      );
    });
  });

  describe('Update paygrade testing', function () {
    it('update an existing paygrade and check toast message', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewPayGrades');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon',
      ).click();
      cy.get(':nth-child(2) > .oxd-input').click().clear().type('AAAAexist');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast-container--bottom').should(
        'include.text',
        'Successfully Updated',
      );
    });
    after(() => {
      cy.visit('/admin/viewPayGrades');
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Cancel button testing', function () {
    it('add a paygrade, currency and clicking cancel', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA');
      cy.get('.oxd-button--secondary').click();
      cy.get('.orangehrm-action-header > .oxd-button').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('AED - Utd. Arab Emir. Dirham')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-form > .oxd-form-actions > .oxd-button--ghost',
      ).click();
      cy.get(
        ':nth-child(1) > .orangehrm-card-container > .oxd-text--h6',
      ).should('include.text', 'Edit Pay Grade');
    });
    it('Edit paygrade & clicking cancel', () => {
      cy.visit('/admin/viewPayGrades');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon',
      ).click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Pay Grades',
      );
    });
    it('visiting add new paygrade and clicking cancel', () => {
      cy.get('.oxd-button').click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Pay Grades',
      );
    });
    after(() => {
      cy.visit('/admin/viewPayGrades');
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Deleting paygrade testing', function () {
    it('deleting a paygrade,check confirmation message and toast', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AESL');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewPayGrades');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(1) > .oxd-icon',
      ).click();
      cy.get('.orangehrm-modal-header > .oxd-text').should(
        'include.text',
        'Are you Sure?',
      );
      cy.get('.oxd-button--label-danger').click();
      cy.get('.oxd-toast-container--bottom').should(
        'include.text',
        'Successfully Deleted',
      );
    });
    it('bulk delete paygrades,check confirmation message and toast', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('EESL');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewPayGrades');
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-header > .oxd-text').should(
        'include.text',
        'Are you Sure?',
      );
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
  });

  describe('Add a deleted paygrade', function () {
    it('add a deleted paygrade', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('EESL');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewPayGrades');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(1) > .oxd-icon',
      ).click();
      cy.get('.oxd-button--label-danger').click();
      cy.visit('/admin/viewPayGrades');
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('EESL');
      cy.get('.oxd-button--secondary').click();
    });
    after(() => {
      cy.visit('/admin/viewPayGrades');
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Add currency type, min/max salaray amount testing', function () {
    it('add a a paygrade and currency without salary', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA');
      cy.get('.oxd-button--secondary').click();
      cy.get('.orangehrm-action-header > .oxd-button').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('AED - Utd. Arab Emir. Dirham')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-form > .oxd-form-actions > .oxd-button--secondary',
      ).click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
    it('add a a paygrade,currency & one salary value', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAB');
      cy.get('.oxd-button--secondary').click();
      cy.get('.orangehrm-action-header > .oxd-button').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('AED - Utd. Arab Emir. Dirham')
        .click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('200');
      cy.get(
        ':nth-child(2) > .oxd-form > .oxd-form-actions > .oxd-button--secondary',
      ).click();
      cy.get('.oxd-table-card > .oxd-table-row > :nth-child(3)').should(
        'include.text',
        '200',
      );
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
    after(() => {
      cy.visit('/admin/viewPayGrades');
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Validation- currency type, min/max salary amount', function () {
    it('Validation-currency type', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA');
      cy.get('.oxd-button--secondary').click();
      cy.get('.orangehrm-action-header > .oxd-button').click();
      cy.get(
        ':nth-child(2) > .oxd-form > .oxd-form-actions > .oxd-button--secondary',
      ).click();
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });
    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('amount field length validation', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAC');
      cy.get('.oxd-button--secondary').click();
      cy.get('.orangehrm-action-header > .oxd-button').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('AED - Utd. Arab Emir. Dirham')
        .click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(charLength.chars10.int);
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Should be less than 1000,000,000',
      );
    });
    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('add currency with max value smaller than min value validation', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAD');
      cy.get('.oxd-button--secondary').click();
      cy.get('.orangehrm-action-header > .oxd-button').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('AED - Utd. Arab Emir. Dirham')
        .click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      )
        .clear()
        .type('600');
      cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .clear()
        .type('300');
      cy.get(':nth-child(2) > .oxd-input-group > .oxd-text').should(
        'include.text',
        'Should be higher than Minimum Salary',
      );
    });
    it('amount field character validation', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAB');
      cy.get('.oxd-button--secondary').click();
      cy.get('.orangehrm-action-header > .oxd-button').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('AED - Utd. Arab Emir. Dirham')
        .click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('asd@#');
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Should be a number',
      );
    });
    after(() => {
      cy.visit('/admin/viewPayGrades');
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Edit currency type, min/max salaray amount testing', function () {
    it('Edit currency details', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA');
      cy.get('.oxd-button--secondary').click();
      cy.get('.orangehrm-action-header > .oxd-button').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('AED - Utd. Arab Emir. Dirham')
        .click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('600');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('1000');
      cy.get(
        ':nth-child(2) > .oxd-form > .oxd-form-actions > .oxd-button--secondary',
      ).click();
      cy.visit('/admin/viewPayGrades');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get('.oxd-table-cell-actions > :nth-child(2)').click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      )
        .clear()
        .type('400');
      cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .clear()
        .type('900');
      cy.get(
        ':nth-child(2) > .oxd-form > .oxd-form-actions > .oxd-button--secondary',
      ).click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Updated');
    });
    it('Edit Currency Type', () => {
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get('.oxd-table-cell-actions > :nth-child(2)').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2) > input',
      ).should('be.disabled');
    });
    it('Edit Amount where max salary = min salary', () => {
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get('.oxd-table-cell-actions > :nth-child(2)').click();
      cy.get(
        '.oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      )
        .clear()
        .type('400');
      cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .clear()
        .type('400');
      cy.get(
        ':nth-child(2) > .oxd-form > .oxd-form-actions > .oxd-button--secondary',
      ).click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Updated');
    });
  });

  describe('Delete currency type', function () {
    it('Delete a single currency and confirmation message', () => {
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get('.oxd-table-cell-actions > :nth-child(1)').click();
      cy.get('.orangehrm-modal-header > .oxd-text').should(
        'include.text',
        'Are you Sure?',
      );
      cy.get('.oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
      cy.visit('/admin/viewPayGrades');
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
    it('Bulk delete currency', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA');
      cy.get('.oxd-button--secondary').click();
      cy.get('.orangehrm-action-header > .oxd-button').click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div.orangehrm-card-container > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('AED - Utd. Arab Emir. Dirham')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-form > .oxd-form-actions > .oxd-button--secondary',
      ).click();
      cy.visit('/admin/viewPayGrades');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get(
        '.oxd-table-card > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
    after(() => {
      cy.visit('/admin/viewPayGrades');
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });
});

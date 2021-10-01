import user from '../../../fixtures/admin.json';
import charLength from '../../../fixtures/charLength.json';

describe('Leave- Config- Leave Type test script', function () {
  beforeEach(() => {
    cy.viewport(1024, 768);
    cy.loginTo(
      user.admin.userName,
      user.admin.password,
      '/leave/leaveTypeList',
    );
  });

  describe('Add Leave type,Duplicate record & radio button functionalty validation testing', function () {
    it('add new leave type and check radio button functionality', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('1---test');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
    it('add duplicate leave type and check validation', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('1---test');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Already exist',
      );
    });
    after(() => {
      cy.visit('/leave/leaveTypeList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });
  describe('Text field validation testing', function () {
    it('required field verification', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type(' ');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });

    it('maximum length validation', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type(charLength.chars51.text);
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Should be less than 50 characters',
      );
    });
  });
  describe('Entitlement Pop-up testing', function () {
    it('Pop-up appearance and close button functionality', () => {
      cy.get('.oxd-button').click();
      cy.get('.label-is-entitlement-situational > .oxd-icon').click();
      cy.get('.oxd-sheet > .orangehrm-card-container > .oxd-text').should(
        'include.text',
        'Situational Leave',
      );
      cy.get(':nth-child(5) > .oxd-form-actions > .oxd-button').should(
        'be.visible',
      );
      cy.get('.oxd-dialog-close-button').should('be.visible');
      cy.get(':nth-child(5) > .oxd-form-actions > .oxd-button').click();
      cy.get('.oxd-sheet > .orangehrm-card-container').should('not.exist');
    });
  });
  describe('List count increment testing', function () {
    it('list count increment', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('1---test');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/leaveTypeList');
      cy.get('.orangehrm-horizontal-padding > .oxd-text')
        .contains('Found')
        .invoke('text')
        .then((line) => {
          const num = line.match(/\((.*)\)/);
          const currentLeaveno = parseInt(num[1]);

          cy.get('.oxd-button').click();
          cy.get(':nth-child(2) > .oxd-input').type('2---test');
          cy.get(
            ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
          ).click();
          cy.get('.oxd-button--secondary').click();
          cy.visit('/leave/leaveTypeList');
          cy.get('.orangehrm-horizontal-padding > .oxd-text')
            .contains('Found')
            .invoke('text')
            .then((line) => {
              const num = line.match(/\((.*)\)/);
              const newLeaveno = parseInt(num[1]);
              expect(newLeaveno).to.eq(currentLeaveno + 1);
            });
        });
    });
    after(() => {
      cy.visit('/leave/leaveTypeList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1) > .oxd-icon',
      ).click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Update leave type testing', function () {
    it('update an existing name,leave type & check toast message', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('1---test');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/leaveTypeList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon',
      ).click();
      cy.get(':nth-child(2) > .oxd-input').clear().type('1---edit');
      cy.get(
        ':nth-child(2) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast-container--bottom').should(
        'include.text',
        'Successfully Updated',
      );
    });
    after(() => {
      cy.visit('/leave/leaveTypeList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Cancel button testing', function () {
    it('visiting edit paytype and clicking cancel', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('1---test');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/leaveTypeList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon',
      ).click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Leave Types',
      );
    });
    it('visiting add new paytype and clicking cancel', () => {
      cy.get('.oxd-button').click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Leave Types',
      );
    });
    after(() => {
      cy.visit('/leave/leaveTypeList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Deleting paytypes testing', function () {
    it('deleting a paytype file and check the toast', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('1---test');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/leaveTypeList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      cy.get('.oxd-toast-container--bottom').should(
        'include.text',
        'Successfully Deleted',
      );
    });
    it('bulk delete paytypes and check the toast', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('1---test');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/leaveTypeList');
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
    it('add a deleted paytype and check the toast', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('1---test');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/leaveTypeList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      cy.get('.oxd-toast-container--bottom').should(
        'include.text',
        'Successfully Deleted',
      );
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('1---test');
      cy.get(
        ':nth-child(1) > :nth-child(2) > .oxd-radio-wrapper > label > .oxd-radio-input',
      ).click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
    after(() => {
      cy.visit('/leave/leaveTypeList');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('UI testing', function () {
    it('Verify header', () => {
      cy.visit('/leave/leaveTypeList');
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Leave Types',
      );
    });
  });
});

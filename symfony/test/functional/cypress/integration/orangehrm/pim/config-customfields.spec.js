import user from '../../../fixtures/admin.json';
import charLength from '../../../fixtures/charLength.json';

describe('Configuration - custom fields', function () {
  beforeEach(() => {
    cy.login(user.admin.userName, user.admin.password);
    cy.viewport(1024, 768);
    cy.visit('/pim/listCustomFields');
  });

  describe('Add a custom field and Duplicate record validations testing', function () {
    it('add a custom field ,check toast message and duplicate license', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA1');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Personal Details')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Text or Number')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.visit('/pim/listCustomFields');
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA1');
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Already exist',
      );
    });
    after(() => {
      cy.visit('/pim/listCustomFields');
      cy.get(
        '.oxd-table-card > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Text field validation testing', function () {
    it('required field verification', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type(' ');
      cy.get('.oxd-button--secondary').click();
      cy.get(
        ':nth-child(1) > .oxd-grid-2 > .organization-name-container > .oxd-input-group > .oxd-text',
      ).should('include.text', 'Required');
      cy.get(':nth-child(2) > .oxd-input-group > .oxd-text').should(
        'include.text',
        'Required',
      );
      cy.get(
        ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > .oxd-text',
      ).should('include.text', 'Required');
    });
    it('maximum length validation', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type(charLength.chars250.text);
      cy.get('.oxd-button--secondary').click();
      cy.get(
        ':nth-child(1) > .oxd-grid-2 > .organization-name-container > .oxd-input-group > .oxd-text',
      ).should('include.text', 'Should be less than 250 characters');
    });
  });

  describe('Customfield count decrement testing', function () {
    it('Remaining Customfield count decrement', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA1');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Personal Details')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Text or Number')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/pim/listCustomFields');
      cy.get('.orangehrm-custom-field-title > .oxd-text--p')
        .contains('Remaining number of custom fields:')
        .invoke('text')
        .then((text) => {
          const fullText = text;
          const pattern = /[0-9]+/g;
          const num = fullText.match(pattern);
          const currentRemno = parseInt(num);
          cy.get('.oxd-button').click();
          cy.get(':nth-child(2) > .oxd-input').type('AAAA2');
          cy.get(
            ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
          ).click();
          cy.get(
            ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
          )
            .contains('Personal Details')
            .click();
          cy.get(
            ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
          ).click();
          cy.get(
            ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
          )
            .contains('Text or Number')
            .click();
          cy.get('.oxd-button--secondary').click();

          cy.visit('/pim/listCustomFields');
          cy.get('.orangehrm-custom-field-title > .oxd-text--p')
            .contains('Remaining number of custom fields:')
            .invoke('text')
            .then((text) => {
              const fullText = text;
              const pattern = /[0-9]+/g;
              const num = fullText.match(pattern);
              const newRemno = parseInt(num);
              expect(newRemno).to.eq(currentRemno - 1);
            });
        });
    });
    it('Remaining Customfield count increment when a record is deleted', () => {
      cy.visit('/pim/listCustomFields');
      cy.get('.orangehrm-custom-field-title > .oxd-text--p')
        .contains('Remaining number of custom fields:')
        .invoke('text')
        .then((text) => {
          const fullText = text;
          const pattern = /[0-9]+/g;
          const num = fullText.match(pattern);
          const currentRemno = parseInt(num);
          cy.get(
            '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
          ).click();
          cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
          cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
          cy.visit('/pim/listCustomFields');
          cy.get('.orangehrm-custom-field-title > .oxd-text--p')
            .contains('Remaining number of custom fields:')
            .invoke('text')
            .then((text) => {
              const fullText = text;
              const pattern = /[0-9]+/g;
              const num = fullText.match(pattern);
              const delRemno = parseInt(num);
              expect(delRemno).to.eq(currentRemno + 1);
            });
        });
    });
    after(() => {
      cy.visit('/pim/listCustomFields');
      cy.get(
        '.oxd-table-card > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('List count increment testing', function () {
    it('List count increment', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('AAAA1');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Personal Details')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Text or Number')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/pim/listCustomFields');
      cy.get('.orangehrm-horizontal-padding > .oxd-text')
        .contains('Found')
        .invoke('text')
        .then((line) => {
          const num = line.match(/\((.*)\)/);
          const currentLicenseno = parseInt(num[1]);

          cy.get('.oxd-button').click();
          cy.get(':nth-child(2) > .oxd-input').type('AAAA2');
          cy.get(
            ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
          ).click();
          cy.get(
            ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
          )
            .contains('Personal Details')
            .click();
          cy.get(
            ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
          ).click();
          cy.get(
            ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
          )
            .contains('Text or Number')
            .click();
          cy.get('.oxd-button--secondary').click();

          cy.visit('/pim/listCustomFields');
          cy.get('.orangehrm-horizontal-padding > .oxd-text')
            .contains('Found')
            .invoke('text')
            .then((line) => {
              const num = line.match(/\((.*)\)/);
              const newLicenseno = parseInt(num[1]);
              expect(newLicenseno).to.eq(currentLicenseno + 1);
            });
        });
    });
  });
  describe('Updating an existing customfield', function () {
    it('Update Name of custom field and type', () => {
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon',
      ).click();
      cy.get(':nth-child(2) > .oxd-input').clear().type('AAAA1test');
      cy.get(
        ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Drop Down')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('1,2,3');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Updated');
    });
    it('Change screen of the custom field ', () => {
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Dependents')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Updated');
    });
  });

  describe('Deleting existing customfields and check toast', function () {
    it('Delete a single customfield, check confirmation message and toast', () => {
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1) > .oxd-icon',
      ).click();
      cy.get('.orangehrm-modal-header > .oxd-text').should(
        'include.text',
        'Are you Sure?',
      );
      cy.get('.oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
    it('Bulk delete customfields, check confirmation message and toast', () => {
      cy.get(
        '.oxd-table-card > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
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
  describe('Cancel button testing', function () {
    it('visiting add new customfield and clicking cancel', () => {
      cy.get('.oxd-button').click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.orangehrm-custom-field-title > .oxd-text--h6').should(
        'include.text',
        'Custom Fields',
      );
    });
  });

  describe('Verify Add button after adding 10 custom fields', function () {
    it('Add button is disabled', function () {
      let i = 0;
      for (i = 0; i < 10; i++) {
        cy.visit('/pim/listCustomFields');
        cy.get('.oxd-button').click();
        cy.get(':nth-child(2) > .oxd-input').type('AAAA' + i);
        cy.get(
          ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text--after > .oxd-icon',
        ).click();
        cy.get(
          ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
        )
          .contains('Personal Details')
          .click();
        cy.get(
          ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
        ).click();
        cy.get(
          ':nth-child(2) > .oxd-grid-2 > .oxd-grid-item > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
        )
          .contains('Text or Number')
          .click();
        cy.get('.oxd-button--secondary').click();
      }
      cy.get('.orangehrm-custom-field-title > .oxd-text--p').should(
        'include.text',
        'All custom fields are in use',
      );
    });
    after(() => {
      let i = 0;
      for (i = 0; i < 10; i++) {
        cy.visit('/pim/listCustomFields');
        cy.get(
          '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
        ).click();
        cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
        cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      }
    });
  });

  describe('UI testing', function () {
    it('Display customfield list', () => {
      cy.visit('/pim/listCustomFields');
      cy.get('.oxd-topbar-header-title > .oxd-text').should(
        'include.text',
        'Custom Field List',
      );
    });
    it('verify header', () => {
      cy.visit('/pim/listCustomFields');
      cy.get('.orangehrm-custom-field-title > .oxd-text--h6').should(
        'include.text',
        'Custom Field',
      );
    });
  });
});

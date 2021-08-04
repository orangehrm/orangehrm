import user from '../../../fixtures/admin.json';
import charLength from '../../../fixtures/charLength.json';
import size from '../../../fixtures/viewport.json';

describe('Qualifications - License test script', function () {
  beforeEach(() => {
    cy.login(user.admin.userName, user.admin.password);
    cy.viewport(size.viewport1.width, size.viewport1.height);
    cy.visit('/admin/viewLicenses');
  });

  describe('Add license and Duplicate record validations testing', function () {
    it('add license file, check toast message and duplicate license', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('ITIL');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast-container--bottom').should(
        'include.text',
        'Successfully Saved',
      );
      cy.visit('/admin/viewLicenses');
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('ITIL');
      cy.get('.oxd-input-group__message').should(
        'include.text',
        'Already exist',
      );
    });
    after(() => {
      cy.visit('/admin/viewLicenses');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
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
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });
    it('maximum length validation', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type(charLength.chars100.text);
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Should not exceed 100 characters',
      );
    });
  });

  describe('List count increment testing', function () {
    it('list count increment', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('ITIL');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewLicenses');
      cy.get('.orangehrm-horizontal-padding > .oxd-text')
        .contains('Found')
        .invoke('text')
        .then((line) => {
          const num = line.match(/\((.*)\)/);
          const currentLicenseno = parseInt(num[1]);

          cy.get('.oxd-button').click();
          cy.get(':nth-child(2) > .oxd-input').type('IESL');
          cy.get('.oxd-button--secondary').click();

          cy.visit('/admin/viewLicenses');
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
    after(() => {
      cy.visit('/admin/viewLicenses');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Update license testing', function () {
    it('update an existing license file and check toast message', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('IESL');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewLicenses');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get(':nth-child(2) > .oxd-input').click().clear().type('CIPM');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast-container--bottom').should(
        'include.text',
        'Successfully Updated',
      );
    });
    after(() => {
      cy.visit('/admin/viewLicenses');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Cancel button testing', function () {
    it('visiting edit license and clicking cancel', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('MCSC');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewLicenses');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.oxd-text').should('include.text', 'Licenses');
    });
    it('visiting add new license and clicking cancel', () => {
      cy.get('.oxd-button').click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.oxd-text').should('include.text', 'Licenses');
    });
    after(() => {
      cy.visit('/admin/viewLicenses');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Deleting license testing', function () {
    it('no records found message', () => {
      cy.get('.oxd-text').should('include.text', 'No Records Found');
    });
    it('deleting a license file and check the toast', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('IESL');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewLicenses');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.get('.oxd-button--label-danger').click();
      cy.get('.oxd-toast-container--bottom').should(
        'include.text',
        'Successfully Deleted',
      );
    });
    it('bulk delete license files and check the toast', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('MCSC');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewLicenses');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
  });

  describe('UI testing', function () {
    it('Add license page', () => {
      cy.visit('/admin/saveLicense');
      cy.get('.orangehrm-card-container > .oxd-text--h6').should(
        'include.text',
        'Add License',
      );
    });
    it('verify header', () => {
      cy.visit('/admin/viewLicenses');
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'License',
      );
    });
    it('field name', () => {
      cy.visit('/admin/saveLicense');
      cy.get('.oxd-label').should('include.text', 'Name');
    });
  });
});

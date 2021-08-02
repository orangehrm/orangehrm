import user from '../../../fixtures/admin.json';
import charLength from '../../../fixtures/charLength.json';
import size from '../../../fixtures/viewport.json';

describe('Qualifications - Education test script', function () {
  beforeEach(() => {
    cy.login(user.admin.userName, user.admin.password);
    cy.viewport(size.viewport1.width, size.viewport1.height);
    cy.visit('/admin/viewEducation');
  });

  describe('Add education and Duplicate record validations testing', function () {
    it('Add education, check toast message and duplicate education', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('Degree');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.visit('/admin/viewEducation');
      cy.get('.oxd-button--medium').should('include.text', 'Add').click();
      cy.get('.oxd-input-group').type('Degree');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group__message').should(
        'include.text',
        'Already exist',
      );
      cy.get('.oxd-button--secondary').click();
    });
    after(() => {
      cy.visit('/admin/viewEducation');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.oxd-button--label-danger')
        .should('include.text', 'Selected')
        .click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Text field validation testing', function () {
    it('Required field validation', () => {
      cy.get('.oxd-button--medium').should('include.text', 'Add').click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group__message').should('include.text', 'Required');
    });
    it('Maximum Length validation', () => {
      cy.get('.oxd-button--medium').should('include.text', 'Add').click();
      cy.get('.oxd-input-group').type(charLength.chars100.text);
      cy.get('.oxd-input-group__message').should(
        'include.text',
        'Should not exceed 100 characters',
      );
      cy.get('.oxd-button--secondary').click();
    });
  });

  describe('List count increment testing', function () {
    it('List count increment testing', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('Diploma');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewEducation');
      cy.get('.orangehrm-horizontal-padding > .oxd-text')
        .contains('Found')
        .invoke('text')
        .then((line) => {
          const num = line.match(/\((.*)\)/);
          const currentEducationno = parseInt(num[1]);

          cy.get('.oxd-button').click();
          cy.get(':nth-child(2) > .oxd-input').type('HND-L1');
          cy.get('.oxd-button--secondary').click();

          cy.visit('/admin/viewEducation');
          cy.get('.orangehrm-horizontal-padding > .oxd-text')
            .contains('Found')
            .invoke('text')
            .then((line) => {
              const num = line.match(/\((.*)\)/);
              const newEducationno = parseInt(num[1]);
              expect(newEducationno).to.eq(currentEducationno + 1);
            });
        });
    });
    after(() => {
      cy.visit('/admin/viewEducation');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.oxd-button--label-danger')
        .should('include.text', 'Selected')
        .click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Update education testing', function () {
    it('update education and toast message', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('Diploma');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewEducation');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get(':nth-child(2) > .oxd-input')
        .click()
        .clear()
        .type('Advanced Level');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Updated');
    });
    after(() => {
      cy.visit('/admin/viewEducation');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.oxd-button--label-danger')
        .should('include.text', 'Selected')
        .click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Cancel button testing', function () {
    it('Visiting edit education and clicking cancel', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('HND-L2');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewEducation');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.oxd-text').should('include.text', 'Education');
    });
    it('Visiting add new education and clicking cancel', () => {
      cy.get('.oxd-button').click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.oxd-text').should('include.text', 'Education');
    });
    after(() => {
      cy.visit('/admin/viewEducation');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.oxd-button--label-danger')
        .should('include.text', 'Selected')
        .click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Delete education testing', function () {
    it('No records found text validation', () => {
      cy.get('.oxd-text').should('include.text', 'No Records Found');
    });
    it('Delete education, check toast message, and list count reduction', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('HND-L3');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewEducation');
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('HND-L4');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewEducation');
      cy.get('.orangehrm-horizontal-padding > .oxd-text')
        .contains('Found')
        .invoke('text')
        .then((line) => {
          const num = line.match(/\((.*)\)/);
          const currentEducationno = parseInt(num[1]);

          cy.get(
            ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(1)',
          ).click();
          cy.get('.oxd-button--label-danger').click();
          cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
          cy.visit('/admin/viewEducation');
          cy.get('.orangehrm-horizontal-padding > .oxd-text')
            .contains('Found')
            .invoke('text')
            .then((line) => {
              const num = line.match(/\((.*)\)/);
              const newEducationno = parseInt(num[1]);
              expect(newEducationno).to.eq(currentEducationno - 1);
            });
        });
    });
    it('Bulk Delete education', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('HND-L4');
      cy.get('.oxd-button--secondary').click();
      cy.visit('/admin/viewEducation');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.oxd-button--label-danger')
        .should('include.text', 'Selected')
        .click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
  });

  describe('UI testing', function () {
    it('Add education page', () => {
      cy.visit('/admin/saveEducation');
      cy.get('.orangehrm-card-container > .oxd-text--h6').should(
        'include.text',
        'Add Education',
      );
    });
    it('Verify header', () => {
      cy.visit('/admin/viewEducation');
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Education',
      );
    });
    it('Verify field name', () => {
      cy.visit('/admin/saveEducation');
      cy.get('.oxd-label').should('include.text', 'Level');
    });
  });
});

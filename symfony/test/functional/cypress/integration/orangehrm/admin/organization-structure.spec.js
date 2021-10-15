import user from '../../../fixtures/admin.json';
import charLength from '../../../fixtures/charLength.json';

describe('Organization - Structure test script', function () {
  beforeEach(() => {
    cy.login(user.admin.userName, user.admin.password);
    cy.viewport(1024, 768);
    cy.visit('/admin/viewCompanyStructure');
  });

  describe('UI testing- when toggle disabled', function () {
    it('Bin icon-invisible', () => {
      cy.get(
        ':nth-child(1) > .--parent > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(1) > .oxd-icon',
      ).should('not.exist');
    });
    it('+ icon-invisible', () => {
      cy.get(
        ':nth-child(1) > .--parent > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(3) > .oxd-icon',
      ).should('not.exist');
    });
    it('Edit icon-invisible', () => {
      cy.get(
        ':nth-child(1) > .--parent > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(2) > .oxd-icon',
      ).should('not.exist');
    });
  });

  describe('UI testing- when toggle enabled', function () {
    it('Display add Button', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').should('include.text', 'Add');
    });
  });

  describe('Add Unit/Department testing', function () {
    it('Add Department', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('test1');
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.visit('/admin/viewCompanyStructure');
      cy.get('.oxd-switch-input').click();
      cy.get(
        '.--last > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(1)',
      ).click();
      cy.get('.oxd-button--label-danger').click();
    });
    it('Add Sub Department', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('test1');
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.visit('/admin/viewCompanyStructure');
      cy.get('.oxd-switch-input').click();
      cy.get(
        '.--last > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(3)',
      ).click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('SUBtest1');
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
    after(() => {
      cy.visit('/admin/viewCompanyStructure');
      cy.get('.oxd-switch-input').click();
      cy.get(
        '.--last > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(1)',
      ).click();
      cy.get('.oxd-button--label-danger').click();
    });
  });

  describe('Delete Unit/Department testing', function () {
    it('Delete Department', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('test1');
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.visit('/admin/viewCompanyStructure');
      cy.get('.oxd-switch-input').click();
      cy.get(
        '.--last > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(1)',
      ).click();
      cy.get('.oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
    it('Delete Department having a Sub Department', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('test1');
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.visit('/admin/viewCompanyStructure');
      cy.get('.oxd-switch-input').click();
      cy.get(
        '.--last > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(3)',
      ).click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('SUBtest1');
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.visit('/admin/viewCompanyStructure');
      cy.get('.oxd-switch-input').click();
      cy.get(
        '.--last > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(1)',
      ).click();
      cy.get('.oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
  });

  describe('Appearance/Dissappearance of the Modal testing', function () {
    it('Save Button- Modal dissappears &  display toast', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('test1');
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.get('.oxd-sheet').should('not.exist');
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.visit('/admin/viewCompanyStructure');
      cy.get('.oxd-switch-input').click();
      cy.get(
        '.--last > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(1)',
      ).click();
      cy.get('.oxd-button--label-danger').click();
    });
    it('Cancel Button- Modal dissappears', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.oxd-sheet').should('not.exist');
      cy.get('.orangehrm-header-container').should(
        'include.text',
        'Organization Structure',
      );
    });
  });

  describe('Text field validation testing-Name', function () {
    it('required field verification', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });
    it('maximum length validation', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(charLength.chars100.text);
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Should be less than 100 characters',
      );
    });
  });

  describe('Text field validation testing-Id', function () {
    it('Input combination characters validation', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('asd123!@#$');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('test1');
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.visit('/admin/viewCompanyStructure');
      cy.get('.oxd-switch-input').click();
      cy.get(
        '.--last > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(1)',
      ).click();
      cy.get('.oxd-button--label-danger').click();
    });
    it('maximum length validation', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(charLength.chars100.text);
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Should be less than 100 characters',
      );
    });
  });

  describe('Text field validation testing-Description', function () {
    it('Input combination characters validation', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(' ');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('test1');
      cy.get('.oxd-textarea').type('asd123!@#$');
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.visit('/admin/viewCompanyStructure');
      cy.get('.oxd-switch-input').click();
      cy.get(
        '.--last > .oxd-tree-node-content > .oxd-sheet > .org-action > :nth-child(1)',
      ).click();
      cy.get('.oxd-button--label-danger').click();
    });
    it('maximum length validation', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.oxd-button').click();
      cy.get('.oxd-textarea').type(charLength.chars400.text);
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'Should be less than 400 characters',
      );
    });
  });
});

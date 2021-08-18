import user from '../../../fixtures/admin.json';
import charLength from '../../../fixtures/charLength.json';

describe('Qualifications - Skills test script', function () {
  beforeEach(() => {
    cy.login(user.admin.userName, user.admin.password);
    cy.viewport(1024, 768);
    cy.visit('/admin/viewSkills');
  });

  describe('Add Skill and Duplicate record validation testing', function () {
    it('Add Skill, check toask message and duplicate skills', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('html');
      cy.get('.oxd-textarea').type('This is the description for html skill');
      cy.get('form').submit();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.visit('/admin/saveSkills');
      cy.get(':nth-child(2) > .oxd-input').type('html');
      cy.get('.oxd-input-group__message').should(
        'include.text',
        'Already exist',
      );
    });
    after(() => {
      cy.visit('/admin/viewSkills');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('List count increment testing', function () {
    it('List count increment testing', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('node.js');
      cy.get('form').submit();
      cy.visit('/admin/viewSkills');
      cy.get('.orangehrm-horizontal-padding > .oxd-text')
        .contains('Found')
        .invoke('text')
        .then((line) => {
          const num = line.match(/\((.*)\)/);
          const currentSkillno = parseInt(num[1]);

          cy.get('.oxd-button').click();
          cy.get(':nth-child(2) > .oxd-input').type('Springboot');
          cy.get('.oxd-textarea').type(
            'This is the description for Springboot skill',
          );
          cy.get('form').submit();

          cy.visit('/admin/viewSkills');
          cy.get('.orangehrm-horizontal-padding > .oxd-text')
            .contains('Found')
            .invoke('text')
            .then((line) => {
              const num = line.match(/\((.*)\)/);
              const newSkillno = parseInt(num[1]);
              expect(newSkillno).to.eq(currentSkillno + 1);
            });
        });
    });
    after(() => {
      cy.visit('/admin/viewSkills');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Text field validations', function () {
    it('required field validation in Skill', () => {
      cy.visit('/admin/saveSkills');
      cy.get(':nth-child(2) > .oxd-input').click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-input-group__message').should('include.text', 'Required');
    });
    it('maximum allowed charachters validation in Skill', () => {
      cy.visit('/admin/saveSkills');
      cy.get(':nth-child(2) > .oxd-input').type(charLength.chars120.text);
      cy.get('.oxd-textarea').type(charLength.chars400.text);
      cy.get('.oxd-input-group__message').should(
        'include.text',
        'Should not exceed 120 characters',
      );
      cy.get('.oxd-input-group__message').should(
        'include.text',
        'Should not exceed 400 characters',
      );
    });
  });

  describe('Update skill testing', function () {
    it('update a skill and check toast message', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('Bootstrap');
      cy.get('form').submit();
      cy.visit('/admin/viewSkills');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get(':nth-child(2) > .oxd-input').click().clear().type('java');
      cy.get('form').submit();
      cy.get('.oxd-toast').should('include.text', 'Successfully Updated');
    });
    after(() => {
      cy.visit('/admin/viewSkills');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Cancel button testing', function () {
    it('visiting edit skill and clicking cancel', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('php');
      cy.get('form').submit();
      cy.visit('/admin/viewSkills');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(2)',
      ).click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.oxd-text--h6').should('include.text', 'Skills');
    });
    it('visiting add skill and clicking cancel', () => {
      cy.visit('/admin/saveSkills');
      cy.get('.oxd-button--ghost').click();
      cy.get('.oxd-text--h6').should('include.text', 'Skills');
    });
    after(() => {
      cy.visit('/admin/viewSkills');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    });
  });

  describe('Delete skill testing', function () {
    it('deleting a skill and checking toast message', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('C++');
      cy.get('form').submit();
      cy.visit('/admin/viewSkills');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex: 1 1 0%;"] > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.get('.oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
    it('bulk delete skills and check toast message', () => {
      cy.get('.oxd-button').click();
      cy.get(':nth-child(2) > .oxd-input').type('Python');
      cy.get('form').submit();
      cy.visit('/admin/viewSkills');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
      cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    });
    it('No records found', () => {
      cy.get('.oxd-text--span').should('include.text', 'No Records Found');
    });
  });

  describe('UI testing', function () {
    it('verify header', () => {
      cy.visit('admin/viewSkills');
      cy.get('.orangehrm-header-container > .oxd-text').should(
        'include.text',
        'Skills',
      );
    });
    it('verify field name', () => {
      cy.visit('admin/viewSkills');
      cy.get('.oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label',
      ).should('include.text', 'Name');
      cy.get(
        ':nth-child(2) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label',
      ).should('include.text', 'Description');
    });
    it(' add skills page', () => {
      cy.visit('/admin/saveSkills');
      cy.get('.orangehrm-card-container > .oxd-text--h6').should(
        'include.text',
        'Add Skill',
      );
    });
  });
});

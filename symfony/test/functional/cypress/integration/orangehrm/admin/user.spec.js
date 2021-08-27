import user from '../../../fixtures/admin.json';
import size from '../../../fixtures/viewport.json';
import charLength from '../../../fixtures/charLength.json';

describe('User Management - User test script', function () {
  beforeEach(() => {
    cy.login(user.admin.userName, user.admin.password);
    cy.viewport(size.viewport1.width, size.viewport1.height);
    cy.visit('/admin/viewSystemUsers');
  });

  describe('Default admin testing', function () {
    it('Default admin availability', () => {
      cy.get(
        '.oxd-table-body > :nth-child(1) > .oxd-table-row > :nth-child(3)',
      ).should('include.text', 'Admin');
    });
    it('Delete default admin', () => {
      cy.get(
        ':nth-child(1) > .oxd-table-row > :nth-child(6) > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.get('.oxd-text--toast-message').should(
        'include.text',
        'Cannot be deleted',
      );
    });
  });

  describe('Add new user testing', function () {
    it('Add user', () => {
      cy.visit('/pim/viewEmployeeList');
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(
        '.--name-grouped-field > :nth-child(1) > :nth-child(2) > .oxd-input',
      ).type('Jane');
      cy.get(':nth-child(3) > :nth-child(2) > .oxd-input').type('Peterson');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.visit('/admin/viewSystemUsers');
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text-input',
      ).click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Admin')
        .click();
      cy.get('.oxd-autocomplete-text-input > input').click().type('j');
      cy.get('.oxd-autocomplete-wrapper > .oxd-autocomplete-dropdown ')
        .contains('Jane Peterson')
        .click();
      cy.get(
        ':nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text-input',
      ).click();
      cy.get(
        ':nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('Enabled')
        .click();
      cy.get(
        ':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('JPeterson');
      cy.get(
        '.user-password-cell > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('Jane@123');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('Jane@123');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
  });

  describe('Field validation testing', function () {
    it('Required field validation', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get('.oxd-button--secondary').click();
      cy.get(
        ':nth-child(1) > .oxd-grid-2 > :nth-child(1) > .oxd-input-group > .oxd-text',
      ).should('include.text', 'Required');
      cy.get(
        ':nth-child(1) > .oxd-grid-2 > :nth-child(2) > .oxd-input-group > .oxd-text',
      ).should('include.text', 'Required');
      cy.get(':nth-child(3) > .oxd-input-group > .oxd-text').should(
        'include.text',
        'Required',
      );
      cy.get(':nth-child(4) > .oxd-input-group > .oxd-text').should(
        'include.text',
        'Required',
      );
      cy.get('.user-password-cell > .oxd-input-group > .oxd-text').should(
        'include.text',
        'Required',
      );
      cy.get(
        '.user-password-row > .oxd-grid-2 > :nth-child(2) > .oxd-input-group > .oxd-text',
      ).should('include.text', 'Required');
    });
    it('Invalid employee search', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get('.oxd-autocomplete-text-input > input').type('z');
      cy.get('.oxd-autocomplete-wrapper > .oxd-autocomplete-dropdown ').should(
        'include.text',
        'No results found',
      );
    });

    describe('Username validation testing', function () {
      it('Adding already used username', () => {
        cy.get('.orangehrm-header-container > .oxd-button').click();
        cy.get(
          ':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-input',
        ).type('JPeterson');
        cy.get('.oxd-input-group > .oxd-text').should(
          'include.text',
          'Username already exists',
        );
      });
      it('Minimum character length validation in username', () => {
        cy.get('.orangehrm-header-container > .oxd-button').click();
        cy.get(
          ':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-input',
        ).type('abcd');
        cy.get('.oxd-input-group > .oxd-text').should(
          'include.text',
          'Should have at least 5 characters',
        );
      });
      it('Maximum character length validation in username', () => {
        cy.get('.orangehrm-header-container > .oxd-button').click();
        cy.get(
          ':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-input',
        ).type(charLength.chars50.text);
        cy.get('.oxd-input-group > .oxd-text').should(
          'include.text',
          'Should not exceed 40 characters',
        );
      });
    });

    describe('Password validation testing', function () {
      it('Minimum character length validation in password', () => {
        cy.get('.orangehrm-header-container > .oxd-button').click();
        cy.get(
          '.user-password-cell > .oxd-input-group > :nth-child(2) > .oxd-input',
        ).type('test123');
        cy.get('.oxd-input-group > .oxd-text').should(
          'include.text',
          'Your password should have at least 8 characters',
        );
      });
      it('Maximum character length validation in password', () => {
        cy.get('.orangehrm-header-container > .oxd-button').click();
        cy.get(
          '.user-password-cell > .oxd-input-group > :nth-child(2) > .oxd-input',
        ).type(charLength.chars100.text);
        cy.get('.oxd-input-group > .oxd-text').should(
          'include.text',
          'Should not exceed 64 characters',
        );
      });
      it('Non matching passwords', () => {
        cy.get('.orangehrm-header-container > .oxd-button').click();
        cy.get(
          '.user-password-cell > .oxd-input-group > :nth-child(2) > .oxd-input',
        ).type('Test@123');
        cy.get(
          ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
        ).type('Test@1');
        cy.get('.oxd-input-group > .oxd-text').should(
          'include.text',
          'Passwords do not match',
        );
      });
      it('Character validation in password', () => {
        cy.get('.orangehrm-header-container > .oxd-button').click();
        cy.get(
          '.user-password-cell > .oxd-input-group > :nth-child(2) > .oxd-input',
        ).type('123456789');
        cy.get('.oxd-input-group > .oxd-text').should(
          'include.text',
          'Your password must contain a lower-case letter',
        );
        cy.get(
          '.user-password-cell > .oxd-input-group > :nth-child(2) > .oxd-input',
        )
          .click()
          .clear()
          .type('abcdefghij');
        cy.get('.oxd-input-group > .oxd-text').should(
          'include.text',
          'Your password must contain a upper-case letter',
        );
        cy.get(
          '.user-password-cell > .oxd-input-group > :nth-child(2) > .oxd-input',
        )
          .click()
          .clear()
          .type('Abcd12345');
        cy.get('.oxd-input-group > .oxd-text').should(
          'include.text',
          'Your password must contain a special character',
        );
      });
    });
  });

  describe('Cancel button testing', function () {
    it('Add user details and click cancel', () => {
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get('.oxd-button--ghost').click();
      cy.get('.oxd-table-filter-header-title > .oxd-text').should(
        'include.text',
        'System Users',
      );
    });
  });
});

import user from './admin-user.json';

export function AddUsers() {
  describe('Add Users', function () {
    beforeEach(() => {
      cy.viewport(1024, 768);
      cy.loginTo(user.admin.userName, user.admin.password, '/pim/addEmployee');
    });
    it('Add Admin user', () => {
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
    it('Add ESS user', () => {
      cy.visit('/pim/viewEmployeeList');
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(
        '.--name-grouped-field > :nth-child(1) > :nth-child(2) > .oxd-input',
      ).type('John');
      cy.get(':nth-child(3) > :nth-child(2) > .oxd-input').type('Perera');
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
        .contains('ESS')
        .click();
      cy.get('.oxd-autocomplete-text-input > input').click().type('j');
      cy.get('.oxd-autocomplete-wrapper > .oxd-autocomplete-dropdown ')
        .contains('John Perera')
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
      ).type('JPerera');
      cy.get(
        '.user-password-cell > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('John@123');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('John@123');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
    it('Add Superviser user', () => {
      cy.visit('/pim/viewEmployeeList');
      cy.get('.orangehrm-header-container > .oxd-button').click();
      cy.get(
        '.--name-grouped-field > :nth-child(1) > :nth-child(2) > .oxd-input',
      ).type('Mike');
      cy.get(':nth-child(3) > :nth-child(2) > .oxd-input').type('Combas');
      cy.get('.oxd-switch-input').click();
      cy.get(
        ':nth-child(4) > .oxd-grid-2 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('MCombas');
      cy.get(
        '.user-password-cell > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('Mike@123');
      cy.get(
        '.oxd-grid-2 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('Mike@123');
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
      cy.get(':nth-child(8) > .orangehrm-tabs-item').click();
      cy.get(
        ':nth-child(3) > :nth-child(1) > .orangehrm-action-header > .oxd-button',
      ).click();
      cy.get('.oxd-autocomplete-text-input > input').click().type('j');
      cy.get('.oxd-autocomplete-wrapper > .oxd-autocomplete-dropdown ')
        .contains('John Perera')
        .click();
      cy.get('.oxd-select-text--after > .oxd-icon').click();
      cy.get('.oxd-select-wrapper > .oxd-select-dropdown')
        .contains('Direct')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
  });
}

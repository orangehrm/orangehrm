import user from '../../../fixtures/admin-user.json';

describe('Login page', () => {
  it('Visits the login page', () => {
    cy.login(user.admin.userName, user.admin.password);
    cy.get('.oxd-userdropdown').should('include.text', user.admin.fullName);
  });

  it('Visits the login page and check validations', () => {
    cy.login(' ', ' ');
    cy.get('.oxd-text').should('include.text', 'Required');
  });
});

import user from '../../../fixtures/admin-user.json';

describe('Add Employee', function () {
  it('check adding a employee', () => {
    cy.login(user.admin.userName, user.admin.password);

    cy.visit('/pim/addEmployee');

    cy.get('input[name="First Name"]').type('John');

    cy.get('input[name="Last Name"]').type('mike');

    cy.get('form').submit();
  });
});

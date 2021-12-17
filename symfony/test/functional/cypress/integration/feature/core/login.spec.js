describe('Core - Login Page', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.intercept('POST', '**/auth/validate').as('postLogin');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  it('should login as admin', function () {
    cy.visit('/auth/login');
    cy.getOXD('form').within(() => {
      cy.getOXDInput('Username').type(this.user.username);
      cy.getOXDInput('Password').type(this.user.password);
      cy.getOXD('button').contains('Login').click();
    });
    cy.wait('@postLogin')
      .its('response.headers')
      .should('have.property', 'location')
      .and('match', /pim\/viewPimModule/);
  });

  it('login form validations should work', function () {
    cy.visit('/auth/login');
    cy.getOXD('button').contains('Login').click();
    cy.getOXDInput('Username').isInvalid('Required');
    cy.getOXDInput('Password').isInvalid('Required');
  });
});

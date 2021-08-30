import user from '../../../fixtures/admin.json';
import charLength from '../../../fixtures/charLength.json';




describe('Contact Details Page test script', function () {
  beforeEach(() => {
    cy.login(user.admin.userName, user.admin.password);
    cy.viewport(1024, 768);
    cy.visit('/pim/viewEmployeeList');
    cy.get(':nth-child(1) > .oxd-table-row > :nth-child(9) > .oxd-table-cell-actions > :nth-child(2)').click();
    cy.visit('/pim/viewPersonalDetails/empNumber/4');
    cy.get(':nth-child(2) > .orangehrm-tabs-item').click();
    cy.visit('/pim/contactDetails/empNumber/4');
  });

  describe('Contact Details Page Add Contact Details', function () {
    it('Add Contact Details, check toast message', () => {

      cy.get(':nth-child(3) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').type('Street 1');
      cy.get(':nth-child(3) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').type('Street 2');
      cy.get(':nth-child(3) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').type('City 1');
      cy.get(':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-input').type('Province 1');
      cy.get(':nth-child(5) > .oxd-input-group > :nth-child(2) > .oxd-input').type('1234');
      cy.get('.oxd-dropdown-input > .oxd-input').click()
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').type('-011234567()4');
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').type('+94772345674');
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').type('0782345674/');
      cy.get(':nth-child(9) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('work@gmail.com');
      cy.get(':nth-child(9) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('other@gmail.com');
      cy.get('.oxd-form-actions > .oxd-button').click();
      cy.get('.oxd-text--toast-message').should('include.text', 'Successfully Updated');
      cy.visit('/pim/contactDetails/empNumber/4');

    });
  });

  describe('Text field validations', function () {
    it('maximum allowed charachters validation in Contact Details', () => {
      cy.get(':nth-child(3) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars70.text);
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should be less than 70 characters');
      cy.get(':nth-child(3) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars70.text);
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should be less than 70 characters');
      cy.get(':nth-child(3) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars70.text);
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should be less than 70 characters');
      cy.get(':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars70.text);
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should be less than 70 characters');
      cy.get(':nth-child(5) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars10.text);
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should be less than 10 characters');
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars25.text);
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should be less than 25 characters');
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars25.text);
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should be less than 25 characters');
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars25.text);
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should be less than 25 characters');
      cy.get(':nth-child(9) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars50.text);
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should be less than 50 characters');
      cy.get(':nth-child(9) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars50.text);
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should be less than 50 characters');

    });

    it('character type validation in Contact Details', () => {
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('55ttt');
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Allows numbers and only + - / ( )');
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('@#$%');
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Allows numbers and only + - / ( )');
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('æÅ£ð');
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Allows numbers and only + - / ( )');

    });

    it('expected format validation in Contact Details', () => {
      cy.get(':nth-child(9) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('tggttgttg');
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Expected format: admin@example.com');
      cy.get(':nth-child(9) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('23456');
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Expected format: admin@example.com');


    });
  });
});

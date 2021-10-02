import user from '../../../fixtures/admin-user.json';
import charLength from '../../../fixtures/charLength.json';

describe('Organization-General Info Page', function () {

  beforeEach(() => {
    cy.loginTo(
      user.admin.userName,
      user.admin.password,
      '/admin/viewOrganizationGeneralInformation',
    );
  });

  
  describe('Verify Organization-General Info page properties', function () {
    
  it('Verify the title in Organization-General Info page', () => {
    cy.visit('admin/viewOrganizationGeneralInformation');
    cy.get('.orangehrm-main-title').should('include.text', 'General Information');

  });

  it('Verify the fields in Organization-General Info page', () => {
    cy.get('.organization-name-container > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Organization Name');
    cy.get(':nth-child(1) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Number of Employees');
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Registration Number');
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Tax ID');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Phone');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Fax');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Email');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Address Street 1');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Address Street 2');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'City');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'State/Province');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Zip/Postal Code');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Country');
    cy.get('.oxd-grid-2 > .oxd-grid-item > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', 'Note');
    
  });
  });


  describe('Verify adding data in Organization-General Info page', function () {
  
    it('Verify adding data into Organization-General Info page', () => {
    
    cy.get('.oxd-switch-input').click();
    cy.get('.organization-name-container > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('OrangeHRM Inc');
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Reg-12345');
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Tax-123');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('0112345678');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('0112777666');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('orangehrm@orangehrm.com');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Strt 1');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Strt 2');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Colombo');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Colombo/Western');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Zip-123');
   
    // ==== selecting a country from the dropdown should be handled here ====
    // cy.get('.oxd-select-text--after > .oxd-icon').click();
    // cy.get('[class="oxd-select-wrapper"]').click();
    // ====
   

    cy.get('.oxd-textarea').clear().type('Test Note');

    cy.get('.oxd-button').click();
    cy.get('.oxd-toast').should('include.text', 'Successfully Updated');

    cy.get('.organization-name-container > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'OrangeHRM Inc');
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Reg-12345');
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Tax-123');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', '0112345678');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', '0112777666');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'orangehrm@orangehrm.com');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Strt 1');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Strt 2');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Colombo');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Colombo/Western');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Zip-123');

   // cy.get('.oxd-select-text').should('have.value', 'Sri Lanka');
    cy.get('.oxd-textarea').should('have.value', 'Test Note');
  });



  it('Verify updating data in Organization-General Info page', () => {
    
    cy.get('.oxd-switch-input').click();
    cy.get('.organization-name-container > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('OrangeHRM Inc updated');
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Reg-12345 updated');
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Tax-123 updated');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('0112345679');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('0112777888');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('orangehrm@orangehrmupdated.com');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Strt 1 updated');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Strt 2 updated');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Colombo updated');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Colombo/Western updated');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type('Zip-123 updated');
   
    // ==== selecting a country from the dropdown should be handled here ====
    // cy.get('.oxd-select-text--after > .oxd-icon').click();
    // cy.get('[class="oxd-select-wrapper"]').click();
    // ====
   

    cy.get('.oxd-textarea').clear().type('Test Note updated');

    cy.get('.oxd-button').click();
    cy.get('.oxd-toast').should('include.text', 'Successfully Updated');

    cy.get('.organization-name-container > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'OrangeHRM Inc updated');
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Reg-12345 updated');
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Tax-123 updated');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', '0112345679');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', '0112777888');
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'orangehrm@orangehrmupdated.com');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Strt 1 updated');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Strt 2 updated');
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Colombo updated');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Colombo/Western updated');
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').should('have.value', 'Zip-123 updated');

   // cy.get('.oxd-select-text').should('have.value', 'Sri Lanka');
    cy.get('.oxd-textarea').should('have.value', 'Test Note updated');



  });
  


    
  });


  

describe('Verify validations in Organization-General Info page', function () {
 
  it('Verify required fields validations in Organization-General Info page', () => {
    cy.get('.oxd-switch-input').click();
    cy.get('.organization-name-container > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
    cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
    cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
    cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
    cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear();
   
    // ==== unselecting the country from the dropdown should be handled here ====
    // cy.get('.oxd-select-text--after > .oxd-icon').click();
    // cy.get('[class="oxd-select-wrapper"]').click();
    // ====

    cy.get('.oxd-textarea').clear();
    cy.get('.oxd-button').click();
    cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
  });



    
    it('Verify character length validations in Organization-General Info page', () => {
      cy.get('.oxd-switch-input').click();
      cy.get('.organization-name-container > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
      cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
      cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
      cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
      cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
      cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
      cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
      cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input').clear().type(charLength.chars100.text);
     
      // ==== selecting a country from the dropdown should be handled here ====
      // cy.get('.oxd-select-text--after > .oxd-icon').click();
      // cy.get('[class="oxd-select-wrapper"]').click();
      // ====
     
      cy.get('.oxd-textarea').clear().type(charLength.chars400.text);
  
      cy.get('.oxd-button').click();

      cy.get('.organization-name-container > .oxd-input-group > .oxd-text').should('include.text','Should be less than 100 characters');
      cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > .oxd-text').should('include.text','Should be less than 30 characters');
      cy.get(':nth-child(2) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > .oxd-text').should('include.text','Should be less than 30 characters');
      cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > .oxd-text').should('include.text','Should be less than 30 characters');
      cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > .oxd-text').should('include.text','Should be less than 30 characters');
      cy.get(':nth-child(4) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > .oxd-text').should('include.text','Should be less than 30 characters');
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > .oxd-text').should('include.text','Should be less than 100 characters');
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > .oxd-text').should('include.text','Should be less than 100 characters');
      cy.get(':nth-child(6) > .oxd-grid-3 > :nth-child(3) > .oxd-input-group > .oxd-text').should('include.text','Should be less than 30 characters');
      cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(1) > .oxd-input-group > .oxd-text').should('include.text','Should be less than 30 characters');
      cy.get(':nth-child(7) > .oxd-grid-3 > :nth-child(2) > .oxd-input-group > .oxd-text').should('include.text','Should be less than 30 characters');
      cy.get('.oxd-grid-2 > .oxd-grid-item > .oxd-input-group > .oxd-text').should('include.text','Should be less than 255 characters');
    });

  });

});


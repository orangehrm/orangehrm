import user from '../../../fixtures/admin.json';

describe('Leave-Configure - Leave Period test script', function () {
  beforeEach(() => {
    cy.login(user.admin.userName, user.admin.password);
    cy.viewport(1024, 768);
    cy.visit('/leave/defineLeavePeriod');
  });
  describe('config leave period testing', function () {
    it('config leave period for the first time ', () => {
      cy.get('.oxd-topbar-header-title').should(
        'include.text',
        'Define Leave Period',
      );
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper >  .oxd-select-dropdown',
      )
        .contains('January')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('01')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });

    it('Change leave period & check toast message', () => {
      cy.get('.oxd-topbar-header-title').should(
        'include.text',
        'Define Leave Period',
      );
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper >  .oxd-select-dropdown',
      )
        .contains('March')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('01')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
    it('Verify end date & current leave period updating accordingly ', () => {
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-text',
      ).should('include.text', 'February 28 (Following Year)');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-text',
      ).should(
        'include.text',
        '\n            2021-01-01\n            to\n            2022-02-28\n',
      );
    });
    it('Select any month, save & toast message', () => {
      cy.get('.oxd-topbar-header-title').should(
        'include.text',
        'Define Leave Period',
      );
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper >  .oxd-select-dropdown',
      )
        .contains('August')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('08')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
  });
  describe('Reset Button testing', function () {
    it('Reset values captured for Start Month and Start date back to previosuly saved values', () => {
      const month = 'September';
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper >  .oxd-select-dropdown',
      )
        .contains(month)
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('25')
        .click();
      cy.get('.oxd-button--secondary').click();
      cy.visit('/leave/defineLeavePeriod');
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper >  .oxd-select-dropdown',
      )
        .contains('May')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('07')
        .click();
      cy.get('.oxd-button--ghost').click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text-input',
      ).contains(month);
    });
  });

  describe('Textfield Validation testing', function () {
    it('required field verification', () => {
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper >  .oxd-select-dropdown',
      )
        .contains('-- Select --')
        .click();
      cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required');
    });
    it('Date get set to the 1st when date is selected before month', () => {
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-dropdown',
      )
        .contains('15')
        .click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text',
      ).click();
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper >  .oxd-select-dropdown',
      )
        .contains('May')
        .click();
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text > .oxd-select-text-input',
      )
        .invoke('text')
        .then((text) => {
          expect(text).to.eq('01');
        });
    });
  });

  describe('UI testing', function () {
    it('Navigation', () => {
      cy.visit('/leave/defineLeavePeriod');
      cy.url().should('include', '/leave/defineLeavePeriod');
    });
    it('verify header and Page Title', () => {
      cy.visit('/leave/defineLeavePeriod');
      cy.get('.oxd-topbar-header-title').should(
        'include.text',
        'Define Leave Period',
      );
      cy.get('.orangehrm-main-title').should('include.text', 'Leave Period');
    });
  });
});

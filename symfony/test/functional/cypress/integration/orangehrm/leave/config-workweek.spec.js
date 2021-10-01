import user from '../../../fixtures/admin.json';

describe('Leave-Configure - Work Week test script', function () {
  beforeEach(() => {
    cy.viewport(1024, 768);
    cy.loginTo(
      user.admin.userName,
      user.admin.password,
      '/leave/defineWorkWeek',
    );
  });
  describe('Change work week type testing', function () {
    it('change type to halfday,fullday, nonworking day & check toast', () => {
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('Full Day')
        .click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(3) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(3) > div > div > div > div:nth-child(2)',
      )
        .contains('Non-working Day')
        .click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(6) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(6) > div > div > div > div:nth-child(2)',
      )
        .contains('Half Day')
        .click();
      cy.get('.oxd-button').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
  });
  describe('work week validation testing', function () {
    it('check with different combinations', () => {
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('Non-working Day')
        .click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(3) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(3) > div > div > div > div:nth-child(2)',
      )
        .contains('Full Day')
        .click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(6) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(6) > div > div > div > div:nth-child(2)',
      )
        .contains('Half Day')
        .click();
      cy.get('.oxd-button').click();
      cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
    });
    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('change all days to non working day', () => {
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(1) > div > div > div > div:nth-child(2)',
      )
        .contains('Non-working Day')
        .click();

      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(3) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(3) > div > div > div > div:nth-child(2)',
      )
        .contains('Non-working Day')
        .click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(2) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(2) > div > div > div > div:nth-child(2)',
      )
        .contains('Non-working Day')
        .click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(4) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(4) > div > div > div > div:nth-child(2)',
      )
        .contains('Non-working Day')
        .click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(6) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(6) > div > div > div > div:nth-child(2)',
      )
        .contains('Non-working Day')
        .click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(5) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(5) > div > div > div > div:nth-child(2)',
      )
        .contains('Non-working Day')
        .click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(7) > div > div > div > div:nth-child(2) > div > div',
      ).click();
      cy.get(
        '#app > div.oxd-layout > div > div.oxd-layout-context > div > div > form > div:nth-child(7) > div > div > div > div:nth-child(2)',
      )
        .contains('Non-working Day')
        .click();

      cy.get('.oxd-button').click();
      cy.get('.oxd-input-group > .oxd-text').should(
        'include.text',
        'At Least One Day Should Be a Working Day',
      );
    });
  });

  describe('UI testing', function () {
    it('View Work week', () => {
      cy.get('.orangehrm-card-container').should('be.visible');
    });
    it('Verify Page Header', () => {
      cy.get('.orangehrm-main-title').should('include.text', 'Work Week');
    });
    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('Verify Main Page Header', () => {
      cy.get('.oxd-topbar-header-title > .oxd-text').should(
        'include.text',
        'Leave',
      );
    });
  });
});

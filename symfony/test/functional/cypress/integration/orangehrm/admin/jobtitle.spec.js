import user from '../../../fixtures/admin-user.json';

describe('Job title page', function () {
  it('Check job title view page', () => {
    cy.login(user.admin.userName, user.admin.password);

    cy.visit('/admin/viewJobTitleList');

    cy.get('.orangehrm-main-title').should('include.text', 'Job Title');
  });
});

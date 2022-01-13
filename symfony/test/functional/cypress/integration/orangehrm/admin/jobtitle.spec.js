import user from '../../../fixtures/admin-user.json';

describe('Job title page', function () {
  beforeEach(() => {
    cy.truncateTable(['JobTitle']);
  });

  it('Check job title view page', () => {
    cy.loginTo(
      user.admin.userName,
      user.admin.password,
      '/admin/viewJobTitleList',
    );
    cy.get('.orangehrm-main-title').should('include.text', 'Job Title');
  });
});

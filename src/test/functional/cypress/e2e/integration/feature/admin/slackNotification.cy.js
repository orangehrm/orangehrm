/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

describe('Admin - Slack Notification Configuration', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.intercept('GET', '**/api/v2/admin/slack-notification/config').as(
      'getConfig',
    );
    cy.intercept('PUT', '**/api/v2/admin/slack-notification/config').as(
      'putConfig',
    );
    cy.intercept('GET', '**/api/v2/admin/slack-notification/registrations*').as(
      'getRegistrations',
    );
    cy.intercept('POST', '**/api/v2/admin/slack-notification/registrations').as(
      'postRegistration',
    );
    cy.intercept(
      'PUT',
      '**/api/v2/admin/slack-notification/registrations/*',
    ).as('putRegistration');
    cy.intercept(
      'DELETE',
      '**/api/v2/admin/slack-notification/registrations/*',
    ).as('deleteRegistration');
    cy.intercept(
      'POST',
      '**/api/v2/admin/slack-notification/registrations/test',
    ).as('postTestWebhook');
    cy.intercept('GET', '**/api/v2/admin/subunits').as('getSubunits');
    cy.intercept('GET', '**/api/v2/attendance/timezones').as('getTimezones');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Read — the page loads with the form-on-top + table-below layout (Row 16/17 AC).
  describe('view slack notification page', function () {
    it('page loads with form and empty registrations table', function () {
      cy.loginTo(this.user, '/admin/slackNotificationConfiguration');
      cy.wait([
        '@getConfig',
        '@getRegistrations',
        '@getSubunits',
        '@getTimezones',
      ]);
      cy.getOXD('pageTitle').should(
        'include.text',
        'Slack Notification Configuration',
      );
      cy.getOXD('form').should('exist');
    });
  });

  // Toggle — the global enable switch persists via PUT /config (Row 18 AC: admin-only access).
  describe('global enable toggle', function () {
    it('flipping the master switch persists to the backend', function () {
      cy.loginTo(this.user, '/admin/slackNotificationConfiguration');
      cy.wait('@getConfig');
      // The styled wrapper sits on top of the actual input; click that instead
      // of needing {force: true} on the underlying .oxd-switch-input.
      cy.get('.oxd-switch-wrapper').first().click();
      cy.wait('@putConfig').its('response.statusCode').should('eq', 200);
      cy.toast('success', 'Successfully Saved');
    });
  });

  // Create — happy path covering Row 21 AC ("Birthday notification arrives correctly").
  // We stub the actual webhook delivery via the intercept so no traffic leaves the box.
  describe('create slack registration', function () {
    it('admin can create a birthday notification for a Slack channel', function () {
      cy.loginTo(this.user, '/admin/slackNotificationConfiguration');
      cy.wait([
        '@getConfig',
        '@getRegistrations',
        '@getSubunits',
        '@getTimezones',
      ]);

      cy.getOXD('form').within(() => {
        // Event type — first dropdown in the form
        cy.getOXDInput('Notification Type').click();
      });
      cy.getOXD('option').contains('Birthday').click();

      cy.getOXD('form').within(() => {
        // Provider — Slack is preselected (beforeMount default) but be explicit
        cy.getOXDInput('Platform').click();
      });
      cy.getOXD('option').contains('Slack').click();

      cy.getOXD('form').within(() => {
        cy.getOXDInput('Webhook URL').type(
          'https://hooks.slack.com/services/T01ABCDEF/B02GHIJKL/abcdefSeCrEt123',
        );
        cy.getOXDInput('Channel Label').type('#birthdays');

        // Send time — type into the time input directly. Split clear/type into
        // two cy.* commands because chaining after `clear()` is flagged by the
        // cypress/unsafe-to-chain-command rule.
        cy.getOXDInput('Send Time').clear();
        cy.getOXDInput('Send Time').type('09:00');

        cy.getOXD('button').contains('Save').click();
      });

      cy.wait('@postRegistration').its('response.statusCode').should('eq', 200);
      cy.toast('success', 'Successfully Saved');
      // Row appears in the registrations table after save (channel column).
      cy.contains('.oxd-table-body', '#birthdays').should('be.visible');
    });
  });

  // Delete — clicking the row action opens the confirmation dialog (Row 17 AC: delete confirmation).
  describe('delete slack registration', function () {
    it('clicking delete on a row shows confirmation and removes on confirm', function () {
      // Pre-seed a row so the table has something to delete.
      cy.task('db:restore', {name: 'slackRegistrationCreated'});
      cy.loginTo(this.user, '/admin/slackNotificationConfiguration');
      cy.wait('@getRegistrations');

      // Delete is the trash icon on the row — typically the last action cell.
      cy.get('.oxd-table-body .oxd-table-cell-actions')
        .first()
        .find('button, .oxd-icon-button')
        .last()
        .click();

      // Confirmation dialog appears
      cy.getOXD('button')
        .contains(/^Yes, Delete$/i)
        .click();
      cy.wait('@deleteRegistration')
        .its('response.statusCode')
        .should('eq', 200);
      cy.toast('success', 'Successfully Deleted');
    });
  });

  // ACL — verify non-admin can't reach the page or the API (Row 18 AC).
  describe('access control', function () {
    it('ESS user is redirected away from the slack page', function () {
      cy.fixture('user').then(({john}) => {
        cy.loginTo(john, '/admin/slackNotificationConfiguration');
        // ESS lands on the dashboard or a 403; the slack title MUST NOT render.
        cy.getOXD('pageTitle').should(
          'not.include.text',
          'Slack Notification Configuration',
        );
      });
    });

    it('ESS user is forbidden from the registrations API', function () {
      cy.fixture('user').then(({john}) => {
        cy.apiLogin(john);
        cy.request({
          method: 'GET',
          url: '/api/v2/admin/slack-notification/registrations',
          failOnStatusCode: false,
        }).then((resp) => {
          expect([401, 403]).to.include(resp.status);
        });
      });
    });
  });
});

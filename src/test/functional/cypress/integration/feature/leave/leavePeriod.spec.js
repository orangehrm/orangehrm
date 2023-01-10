/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

import user from '../../../fixtures/user.json';

describe('Leave - Leave Period', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('viewport').then(({HD}) => {
      cy.viewport(HD.width, HD.height);
    });
    cy.intercept('GET', '**/api/v2/leave/leave-period').as(
      'retriveLeavePeriod',
    );
    cy.intercept('PUT', '**/api/v2/leave/leave-period').as('updateLeavePeriod');
    cy.fixture('user').then((data) => {
      this.adminUser = data.admin;
      this.essUser = data.john;
    });
  });

  describe('save leave period', function () {
    it('update the leave period to any day of the year', function () {
      cy.loginTo(user.admin, '/leave/defineLeavePeriod');
      cy.wait('@retriveLeavePeriod');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Start Month').selectOption('May');
        cy.getOXDInput('Start Date').selectOption('15');
        cy.getOXD('button').contains('Save').click();
      });
      cy.toast('success', 'Successfully Saved');
      cy.wait('@updateLeavePeriod');
    });
    // eslint-disable-next-line jest/no-disabled-tests
    it.skip('Verify the Current Leave Period and End date is getting updated', function () {
      cy.loginTo(user.admin, '/leave/defineLeavePeriod');
      cy.wait('@retriveLeavePeriod');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Start Month').selectOption('June');
        cy.getOXDInput('Start Date').selectOption('28');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateLeavePeriod');
      cy.toast('success', 'Successfully Saved');
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-text',
      ).should('include.text', 'June 27 (Following Year)');
    });
    it('Verify the Date is set to 1st', function () {
      cy.loginTo(user.admin, '/leave/defineLeavePeriod');
      cy.wait('@retriveLeavePeriod');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Start Month').selectOption('July');
        cy.getOXDInput('Start Date').should('include.text', '1');
      });
    });
  });

  //Validation
  describe('Leave Period- Validations', function () {
    it('Required Validation', function () {
      cy.loginTo(user.admin, '/leave/defineLeavePeriod');
      cy.wait('@retriveLeavePeriod');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Start Month').selectOption('-- Select --');
        cy.getOXDInput('Start Month').isInvalid('Required');
        cy.getOXDInput('Start Date').isInvalid('Required');
      });
    });
  });

  //Reset Leave Period
  describe('Reset Leave Period', function () {
    it('Create db snapshot for Reset Leave Period', function () {
      cy.loginTo(user.admin, '/leave/defineLeavePeriod');
      cy.wait('@retriveLeavePeriod');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Start Month').selectOption('May');
        cy.getOXDInput('Start Date').selectOption('15');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateLeavePeriod');
      cy.toast('success', 'Successfully Saved');
      cy.task('db:snapshot', {name: 'leavePeriodSaved2'});
    });
    it('Verify Reset button Function ', function () {
      cy.task('db:restore', {name: 'leavePeriodSaved2'});
      cy.loginTo(user.admin, '/leave/defineLeavePeriod');
      cy.wait('@retriveLeavePeriod');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Start Month').selectOption('June');
        cy.getOXDInput('Start Date').selectOption('28');
        cy.getOXD('button').contains('Reset').click();
      });
      cy.getOXDInput('Start Month').should('include.text', 'May');
      cy.getOXDInput('Start Date').should('include.text', '15');
    });
  });
});

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

const empStatusAPI = '/api/v2/admin/employment-statuses';

describe('Admin - Employment Status API ', function () {
  before(function () {
    cy.fixture('user').then(({admin}) => {
      cy.apiLogin(admin);
    });
  });

  beforeEach(function () {
    cy.fixture('chars').as('strings');
    Cypress.Cookies.preserveOnce('orangehrm', '_orangehrm');
  });

  describe('POST /emp-status', function () {
    it('create a new employment status', function () {
      cy.request('POST', empStatusAPI, {
        name: this.strings.chars50.text,
      }).then((response) => {
        expect(response.status).to.eq(200);
        expect(response.body.data.name).to.eq(this.strings.chars50.text);
      });
    });
  });

  describe('With `emp-statuses` snapshot', function () {
    before(function () {
      cy.task('db:reset');
      cy.request('POST', empStatusAPI, {
        name: this.strings.chars50.text,
      }).then(function () {
        cy.task('db:snapshot', {name: 'emp-statuses'});
      });
    });

    describe('GET /emp-status ', function () {
      it('get all employment status:empty', function () {
        cy.task('db:reset');
        cy.request('GET', empStatusAPI).then((response) => {
          expect(response.status).to.eq(200);
          expect(response.body.data.length).to.eq(0);
          expect(response.body.meta.total).to.eq(0);
        });
      });

      it('get all employment status', function () {
        cy.task('db:restore', {name: 'emp-statuses'});
        cy.request('GET', empStatusAPI).then((response) => {
          expect(response.status).to.eq(200);
          expect(response.body.data.length).to.eq(1);
          expect(response.body.data[0].name).to.eq(this.strings.chars50.text);
          expect(response.body.meta.total).to.eq(1);
        });
      });

      it('get one employment status', function () {
        cy.task('db:restore', {name: 'emp-statuses'});
        cy.request('GET', empStatusAPI + '/1').then((response) => {
          expect(response.status).to.eq(200);
          expect(response.body.data.name).to.eq(this.strings.chars50.text);
        });
      });
    });

    describe('DELETE /emp-status', function () {
      it('delete all employment status', function () {
        cy.task('db:restore', {name: 'emp-statuses'});
        cy.request('GET', empStatusAPI)
          .then((response) => {
            expect(response.status).to.eq(200);
            return cy.request('DELETE', empStatusAPI, {
              ids: response.body.data.map((item) => item.id),
            });
          })
          .then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.data).to.deep.eq([1]);
          });
      });

      it('delete one employment status', function () {
        cy.task('db:restore', {name: 'emp-statuses'});
        cy.request('DELETE', empStatusAPI, {ids: [1]}).then((response) => {
          expect(response.status).to.eq(200);
          expect(response.body.data).to.deep.eq([1]);
        });
      });
    });

    describe('PUT /emp-status', function () {
      it('update an employment status', function () {
        cy.task('db:restore', {name: 'emp-statuses'});
        cy.request('PUT', empStatusAPI + '/1', {name: 'test'}).then(
          (response) => {
            expect(response.status).to.eq(200);
            expect(response.body.data.name).to.eq('test');
          },
        );
      });
    });
  });
});

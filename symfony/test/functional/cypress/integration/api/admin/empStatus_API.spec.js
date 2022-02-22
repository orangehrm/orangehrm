const empStatusAPI = '/api/v2/admin/employment-statuses';

describe('Admin - Employment Status API ', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.fixture('user').then(({admin}) => {
      cy.apiLogin(admin);
    });
  });

  describe('POST /emp-Status', function () {
    it('create a new employment status', function () {
      cy.request('POST', empStatusAPI, {
        name: this.strings.chars50.text,
      }).then((response) => {
        console.log(response);
        expect(response.status).to.eq(200);
        expect(response.body.data.name).to.eq(this.strings.chars50.text);
        cy.task('db:snapshot', {name: 'emp-Status-POST1'});
      });
    });
  });

  describe('GET /emp-Status ', function () {
    it('get all employment status', function () {
      cy.request('GET', empStatusAPI).then((response) => {
        expect(response.status).to.eq(200);
        expect(response.body.data.length).to.eq(0);
      });
    });
    it('get one employment status', function () {
      cy.task('db:restore', {name: 'emp-Status-POST1'});
      cy.request('GET', empStatusAPI)
        .then((response) => {
          const statuslist = response.body.data.map((item) => item.id);
          return cy.request('GET', empStatusAPI + '/' + statuslist[0]);
        })
        .then((response) => {
          expect(response.status).to.eq(200);
          expect(response.body.data.name).to.eq(this.strings.chars50.text);
        });
    });
  });

  describe('DELETE /emp-Status', function () {
    it('delete all employment status', function () {
      cy.task('db:restore', {name: 'emp-Status-POST1'});
      cy.request('GET', empStatusAPI)
        .then((response) => {
          expect(response.status).to.eq(200);
          return cy.request('DELETE', empStatusAPI, {
            ids: response.body.data.map((item) => item.id),
          });
        })
        .then((response) => {
          expect(response.status).to.eq(200);
        });
    });
    it('delete one employment status', function () {
      cy.task('db:restore', {name: 'emp-Status-POST1'});
      cy.request('GET', empStatusAPI)
        .then((response) => {
          const statuslist = response.body.data.map((item) => item.id);
          expect(response.status).to.eq(200);
          return cy.request('DELETE', empStatusAPI, {
            ids: [statuslist[0]],
          });
        })
        .then((response) => {
          expect(response.status).to.eq(200);
        });
    });
  });

  describe('PUT /emp-Status', function () {
    it('update an employment status', function () {
      cy.task('db:restore', {name: 'emp-Status-POST1'});
      cy.request('GET', empStatusAPI).then((response) => {
        const statuslist = response.body.data.map((item) => item.id);
        console.log(response);
        expect(response.status).to.eq(200);
        return cy
          .request('PUT', empStatusAPI + '/' + statuslist[0], {
            name: 'test',
          })
          .then((response) => {
            expect(response.status).to.eq(200);
            expect(response.body.data.name).to.eq('test');
          });
      });
    });
  });
});

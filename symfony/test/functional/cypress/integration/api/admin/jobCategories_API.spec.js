const jobCatApi = `/api/v2/admin/job-categories`;

describe('Admin - Job Categories API', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.fixture('user').then(({admin}) => {
      cy.apiLogin(admin);
    });
  });

  describe('GET /job-categories', function () {
    it('gets the list of job categories', function () {
      cy.request('GET', jobCatApi).then((response) => {
        expect(response.status).to.eq(200);
        expect(response.body.data.length).to.eq(9);
      });
    });
    it('Get a single job category ', function () {
      cy.request('GET', jobCatApi + '/7', {}).then((response) => {
        expect(response.status).to.eq(200);
        expect(response.body.data.name).to.eq('Craft Workers');
      });
    });
  });

  describe('POST /job-categories', function () {
    it('creates a new job category', function () {
      cy.request('POST', jobCatApi, {
        name: this.strings.chars30.text,
      }).then((response) => {
        expect(response.status).to.eq(200);
      });
    });
  });

  describe('UPDATE /job-categories', function () {
    it('update a job category ', function () {
      cy.request('PUT', jobCatApi + '/7', {
        name: 'test2',
      }).then((response) => {
        expect(response.status).to.eq(200);
        expect(response.body.data.name).to.eq('test2');
      });
    });
  });

  describe('DELETE /job-category', function () {
    it('Deletes all job categories', function () {
      cy.request('GET', jobCatApi)
        .then((response) => {
          expect(response.status).to.eq(200);
          return cy.request('DELETE', jobCatApi, {
            ids: response.body.data.map((item) => item.id),
          });
        })
        .then((response) => {
          expect(response.status).to.eq(200);
        });
    });
    it('Deletes a single job category', function () {
      cy.request('GET', jobCatApi)
        .then((response) => {
          expect(response.status).to.eq(200);
          return cy.request('DELETE', jobCatApi, {
            ids: [response.body.data.map((item) => item.id)[0]],
          });
        })
        .then((response) => {
          expect(response.status).to.eq(200);
        });
    });
  });
});

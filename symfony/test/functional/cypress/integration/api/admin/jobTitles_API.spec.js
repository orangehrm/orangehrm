import faker from 'faker';

const jobTitlesApi = `/api/v2/admin/job-titles`;

describe('Admin - Job Title API', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.apiLogin(user.admin.userName, user.admin.password);
  });

  const dummyText = faker.lorem.paragraph(20);
  const jobTitleModel = {
    title: faker.name.title(),
    description: dummyText.substring(0, 150),
    note: dummyText.substring(0, 150),
  };

  describe('GET /job-titles', function () {
    it('gets a list of job titles', function () {
      cy.request('GET', jobTitlesApi).then((response) => {
        expect(response.status).to.eq(200);
        expect(response.body.data.length).to.eq(0);
      });
    });
  });

  describe('POST /job-titles', function () {
    it('creates a new job title', function () {
      cy.request('POST', jobTitlesApi, {
        title: jobTitleModel.title,
        description: jobTitleModel.description,
        specification: null,
        note: jobTitleModel.note,
      }).then((response) => {
        expect(response.status).to.eq(200);
      });
    });
  });
});

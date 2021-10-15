import adminUser from '../../../fixtures/admin.json';
import viewport from '../../../fixtures/viewport.json';
import UserListPageClass from '../../../support/page_Objects/admin/user-listPageWithoutComponenet';
describe('User List Page',() =>{
    const usersListPage = new UserListPageClass()
    beforeEach(()=>{
        cy.login(adminUser.admin.userName, adminUser.admin.password);
        cy.viewport(viewport.viewport1.width, viewport.viewport1.height);
        cy.visit('/admin/viewSystemUsers');
    })
    it('Search Employee with all values',function() {
        usersListPage.fillUserName('Admin')
        usersListPage.selectUserRoles('Admin')
        usersListPage.selectElementfromMultiSelect('Shenali')
        usersListPage.selectEmployeeStatus('Enabled')
        usersListPage.clickSearchButton()
        usersListPage.returnTableRowCount().then(rows =>{
            expect(rows.length).to.equal(1)
        })
    })
    it('Search Employee with User role value',function() {
        usersListPage.selectUserRoles('ESS')
        usersListPage.selectEmployeeStatus('Enabled')
        usersListPage.clickSearchButton()
        usersListPage.returnTableRowCount().then(rows =>{
            expect(rows.length).to.equal(5)
        })
    })
})



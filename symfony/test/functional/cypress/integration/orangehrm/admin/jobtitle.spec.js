import user from '../../../fixtures/user.json'
import string from '../../../fixtures/string.json'

//verify Job Title List 
describe('Job title page', function () {
	
	context('desktop view', () => {
		
		beforeEach(() => {
			// login with admin user
			cy.login(user.ann.userName, user.ann.password)
			
			// run these tests as if in a desktop
			cy.viewport(1024, 768)
		})

		it('Verify job title list UI', () => {

			cy.visit('/admin/viewJobTitleList')
			
			cy.get('.oxd-text--h6').should('include.text',"Job Title List")
			
			cy.get('button[class="oxd-button oxd-button--medium oxd-button--secondary"]').should('include.text',"Add")
			
			cy.get('span[class="oxd-text oxd-text--span"]').should('includes.text',"No Records Found")
			
			cy.get('div[role="columnheader"]').eq(1).should('include.text',"Job Title")
			
			cy.get('div[role="columnheader"]').eq(2).should('include.text',"Description")
			
			cy.get('div[role="columnheader"]').eq(3).should('include.text',"Actions")

			
		})
		
		it('Add job title', () => {

			cy.visit('/admin/viewJobTitleList')
			
			cy.contains('Add').click()
			
			cy.get('.oxd-text--h6').should('include.text',"Add Job Title")
			
			cy.get('input[class="oxd-input oxd-input--active"]').eq(1).type('QA Engineer')
			
			cy.get('textarea[class="oxd-textarea oxd-textarea--active oxd-textarea--resize-vertical"]').eq(0).type('Quality Assurance Engineer')
			
			cy.get('.oxd-file-button').attachFile('QA_Engineer.jpg')
			
			cy.get('form').submit()
			
			cy.get('.oxd-toast').should('include.text','Success')
			
		})
		
		it('Required validation', () => {

			cy.visit('/admin/viewJobTitleList')
			
			cy.contains('Add').click()
			
			cy.get('.orangehrm-left-space').click()
			
			cy.get('.oxd-input-group__message').should('include.text','Required')
		})
		
		it('Duplicate validation', () => {

			cy.visit('/admin/viewJobTitleList')
			
			cy.contains('Add').click()
			
			cy.get('.oxd-text--h6').should('include.text',"Add Job Title")
			
			cy.get('input[class="oxd-input oxd-input--active"]').eq(1).type('QA Engineer')
			
			cy.get('.orangehrm-left-space').click()
			
			cy.get('.oxd-input-group__message').should('include.text','Job title should be unique')
		})
		
		it('Length validation', () => {

			cy.visit('/admin/viewJobTitleList')
			
			cy.contains('Add').click()
			
			cy.get('.oxd-text--h6').should('include.text',"Add Job Title")
			
			cy.get('input[class="oxd-input oxd-input--active"]').eq(1).type(string.txt100chars.txt)
			
			cy.get('textarea[class="oxd-textarea oxd-textarea--active oxd-textarea--resize-vertical"]').eq(0).type(string.txt400chars.txt)
			
			cy.get('.orangehrm-left-space').click()
			
			cy.get('.oxd-input-group__message').eq(0).should('include.text','Should not exceed 100 characters')
			
			cy.get('.oxd-input-group__message').eq(1).should('include.text','Should not exceed 400 characters')
			
		})
		
		it('Edit job title', () => {

			cy.visit('/admin/viewJobTitleList')
			
			cy.get('button[class="oxd-icon-button oxd-table-cell-action-space"]').eq(1).click()
			
			cy.get('.oxd-text--h6').should('include.text',"Edit Job Title")
			
			cy.get('input[class="oxd-input oxd-input--active"]').eq(1).clear().type('QA')
			
			cy.get('textarea[class="oxd-textarea oxd-textarea--active oxd-textarea--resize-vertical"]').eq(0).clear().type('Quality Assurance Engineer')
						
			cy.get('form').submit()
			
			cy.get('.oxd-toast').should('include.text','Success')
			
		})
		
	})
	
	context('mobile view', () => {
		
		beforeEach(() => {
			// login with admin user
			cy.login(user.ann.userName, user.ann.password)
			
			// run these tests as if in a mobile
			cy.viewport(375, 812)
		})

		it('Verify job title list UI', () => {

			cy.visit('/admin/viewJobTitleList')
			
			cy.get('.oxd-text--h6').should('include.text',"Job Title List")
			
			cy.get('button[class="oxd-button oxd-button--medium oxd-button--secondary"]').should('include.text',"Add")
			
			cy.get('span[class="oxd-text oxd-text--span"]').should('includes.text',"1 Job Title Found")
			
			cy.get('div[role="columnheader"]').should('not.exist')
			
			
		})
	})
})





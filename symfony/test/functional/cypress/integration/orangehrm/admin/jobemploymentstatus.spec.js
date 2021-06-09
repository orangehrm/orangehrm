
import user from '../../../fixtures/user.json'

//check employment status page
describe ('employment status page', function(){
    it ( 'check employemnt status list page', () => {
         cy.login( user.admin.userName, user.admin.password)   
         cy.visit('/admin/employmentStatus')
         cy.get('.oxd-text--h6').should('include.text', "Employment Status List")
    })
})

//Add a new employment status   
 describe ('add new employment status', function(){
       it ('check add new employment status', () => {
            cy.login (user.admin.userName, user.admin.password) 
            cy.visit ('/admin/saveEmploymentStatus')
            cy.get(':nth-child(2) > .oxd-input').type('Contract')
            cy.get ('form').submit()
        })   
    })

//Validate add employment status field by exceeding the character no
describe ('validate add employment status field by exceeding the character no', function(){
        it('validate add employment status by exceeding the character no', () => {
            cy.login(user.admin.userName, user.admin.password)
            cy.visit( '/admin/saveEmploymentStatus')
            cy.get(':nth-child(2) > .oxd-input').type('validateaddingemploymentstatusfieldbyexceedingthemaximumcharacterlimit')
            cy.get ('form').submit()
            cy.get('.oxd-input-group__message').should('include.text', 'Should not exceed 50 characters')      
        })
    })
    
//Validate add employment status required field
describe ('validate add employment status required field', function(){
        it('validate add employment status required field', () => {
            cy.login(user.admin.userName, user.admin.password) 
            cy.visit( '/admin/saveEmploymentStatus')  
            cy.get(':nth-child(2) > .oxd-input').type(' ') 
            cy.get ('form').submit()
            cy.get('.oxd-input-group__message').should('include.text', 'Required')        
        })
    })


//Updating an employment status and the toast message 
describe ('updating an employment status and the toast message', function (){
    it('updating an employment status and the toast message', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon').click()
        cy.get(':nth-child(2) > .oxd-input').click().clear().type('Sample status')
        cy.get ('form').submit()       
    })
})

//Adding duplicate employment status
describe ('adding a duplicate employment status', function(){
    it('adding a duplicate employment status', () => {
           cy.login(user.admin.userName, user.admin.password)
           cy.visit('/admin/saveEmploymentStatus')
           cy.get(':nth-child(2) > .oxd-input').type('Contract')   
           cy.get ('form').submit()
           cy.get('.oxd-input-group__message').should('include.text', 'Employment Status should be unique')
    })
})

//Delete employment status
describe ('delete an employment status', function(){
    it('delete an employment status', () => {       
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(1)').click()
        cy.get('.oxd-button--label-danger').click()
        cy.get ('.oxd-toast').should('include.text', 'Employment Status deleted successfully!')
    })
})


//Add a new employment status and check the success toast.
describe ('add new employment status and check the success toast', function(){
    it ('check add new employment status and check the success toast', () => {
        cy.login (user.admin.userName, user.admin.password) 
        cy.visit ('/admin/saveEmploymentStatus')
        cy.get(':nth-child(2) > .oxd-input').type('Probation')
        cy.get ('form').submit()
        cy.wait (2000)
        cy.get ('.oxd-toast').should('include.text', 'Employment Status added successfully!')
    })   
})

//Visiting update an employment status and click cancel 
describe ('visiting update an employment status and click cancel', function (){
    it('visiting update an employment status and click cancel', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2)').click()
        cy.get('.oxd-button--ghost').click()
        
    })
})

//Visiting add a new employment status and click cancel 
describe ('visiting add a new employment status and click cancel', function (){
    it('visiting add a new employment status and click cancel', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get('.oxd-button').click()
        cy.get('.oxd-button--ghost').click()      
    })
})

//Bulk delete and check the success toast.
describe ('check the success toast', function(){
    it('check the success toast', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.viewport(1024, 768)
        cy.get('.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon').click()
        cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click()
        cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click()
        cy.get ('.oxd-toast').should('include.text', 'Success')
    })
})

//Employment status list no results found
describe ('Employment status list no results found', function(){
    it ('check employment status list page', () => {
         cy.login(user.admin.userName, user.admin.password) 
         cy.visit ('/admin/employmentStatus')
         cy.get ('.oxd-text--span').should('include.text', "No Records Found" )    
       })
   })
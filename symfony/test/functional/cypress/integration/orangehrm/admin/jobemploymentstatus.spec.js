import user from '/home/administrator/Desktop/OS /orangehrm/symfony/test/functional/cypress/fixtures/user.json'

//check employment status page
describe ('employment status page', function(){

    it ( 'check employemnt status list page', () => {
         cy.login( user.admin.userName, user.admin.password)   
         cy.visit('/admin/employmentStatus')
         cy.get('.oxd-text--h6').should('include.text', "Employment Status List")
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

//Add a new employment status   
 describe ('add new employment status', function(){

        it ('check add new employment status', () => {
    
            cy.login (user.admin.userName, user.admin.password) 
            cy.visit ('/admin/saveEmploymentStatus')
            cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').type('Full time')
            cy.get ('form').submit()
        })   
    })

//Validate add employment status field by exceeding the character no
describe ('validate add employment status field by exceeding the character no', function(){

        it('validate add employment status by exceeding the character no', () => {
    
            cy.login(user.admin.userName, user.admin.password)
            cy.visit( '/admin/saveEmploymentStatus')
            cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').type('validate add employment status field by exceeding the character no')
            cy.get ('form').submit()
            cy.get('.oxd-input-group__message').should('include.text', 'Should not exceed 50 characters')
    
            
        })
    })
    
//Validate add employment status required field
describe ('validate add employment status required field', function(){
    
        it('validate add employment status required field', () => {
    
            cy.login(user.admin.userName, user.admin.password) 
            cy.visit( '/admin/saveEmploymentStatus')  
            cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').type(' ')   
            cy.get ('form').submit()
            cy.get('.oxd-input-group__message').should('include.text', 'Required')
    
           
        })
    })


//Updating an employment status

describe ('updating an employment status', function (){

    it('updating an employment status', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.visit ('/admin/saveEmploymentStatus/6')
        cy.xpath ('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').click().clear().type('Sample Status')
        cy.get ('form').submit()
        
    })
})


//Adding duplicate employment status

describe ('adding a duplicate employment status', function(){

    it('adding a duplicate employment status', () => {
      
           cy.login(user.admin.userName, user.admin.password)
           cy.visit('/admin/saveEmploymentStatus')
           cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').type('Full time')   
           cy.get ('form').submit()
           cy.get('.oxd-input-group__message').should('include.text', 'Employment Status should be unique')

    })

})




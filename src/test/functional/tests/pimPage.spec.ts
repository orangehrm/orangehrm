import { test, expect } from '@playwright/test';
import { PimPage } from '../pages/PimPage';
import { LoginPage } from '../pages/LoginPage';
import { adminUserTestData } from '../data';
import { SubPage } from '../pages/BasePage';
import { generateRandomString } from '../utils/helper';
import { newEmployeeTestData } from '../data';


test.describe.only('Admin user should be able to manage on pim page', () => {
    let loginPage: LoginPage;
    let pimPage: PimPage;
    const randomNewEmployeeName = generateRandomString(3);
    const randomNewEmployeeNameForEditing = generateRandomString(3);
    const randomEditedEmployeeName = generateRandomString(3) + 'Edited';
  
    test.beforeEach(async ({ page }) => {
      loginPage = new LoginPage(page);
      pimPage = new PimPage(page);
      await loginPage.initialize();
      await loginPage.navigateToMainPage();
      await loginPage.loginUser(
        adminUserTestData.userName,
        adminUserTestData.password
      );
      await pimPage.navigateToSubPage(SubPage.PIM);
    });

    test.only('Admin user ahould add employee', async ({ page }) => {
      await pimPage.addEmployee(newEmployeeTestData.firstName + randomNewEmployeeName);
      await pimPage.navigateToSubPage(SubPage.PIM);
      const element = await pimPage.getLocatorByRandomNewEmplyeeName(randomNewEmployeeName)
      await page.waitForLoadState()
      await expect(page.locator(element)).toBeVisible();
    });
 
    test.only('Admin user ahould edit previousely created employee with random name', async ({ page }) => {
      await pimPage.addEmployee(newEmployeeTestData.firstName + randomNewEmployeeNameForEditing);
      await pimPage.navigateToSubPage(SubPage.PIM);
      const element = await pimPage.getEditIconByRandomEmployeeName(randomNewEmployeeNameForEditing)
      await page.locator(element).click();
      await pimPage.editEmployee(randomEditedEmployeeName)
      await pimPage.navigateToSubPage(SubPage.PIM);
      const employeeEditedName = await pimPage.getLocatorByRandomNewEmplyeeName(randomEditedEmployeeName)
      await page.waitForLoadState()
      await expect(page.locator(employeeEditedName)).toBeVisible();
    });

    test.only('Admin user should delete employee//Clean all data after tests..', async ({ page }) => {
      const pimPage = new PimPage(page);
      const trashBinGotByName = await pimPage.getTrashBinByRandomEmployeeName(randomNewEmployeeName);
      const trashBinGotByEditedName = await pimPage.getTrashBinByRandomEmployeeName(randomEditedEmployeeName);
      await page.locator(trashBinGotByName).click();
      await pimPage.confirmDeleteButton.click();
      await page.locator(trashBinGotByEditedName).click();
      await pimPage.confirmDeleteButton.click();
      await expect(page.locator(trashBinGotByEditedName)).not.toBeAttached()
      await expect(page.locator(trashBinGotByName)).not.toBeAttached()
    });
});







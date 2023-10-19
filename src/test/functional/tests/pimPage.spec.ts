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

    test('Admin user ahould add employee', async ({ page }) => {
      await pimPage.addEmployee(newEmployeeTestData.firstName + randomNewEmployeeName);
      await pimPage.navigateToSubPage(SubPage.PIM);
      const element = await pimPage.randomNewEmplyeeNameText(randomNewEmployeeName)
      await page.waitForLoadState()
      await expect(page.locator(element)).toBeVisible();
    });
 
    test('Admin user ahould edit previousely created employee with random name', async ({ page }) => {
      await pimPage.addEmployee(newEmployeeTestData.firstName + randomNewEmployeeNameForEditing);
      await pimPage.navigateToSubPage(SubPage.PIM);
      const element = await pimPage.locateEditIconByRandomEmployeeName(randomNewEmployeeNameForEditing)
      await page.locator(element).click();
      await pimPage.editEmployee(randomEditedEmployeeName)
      await pimPage.navigateToSubPage(SubPage.PIM);
      const editedElement = await pimPage.randomNewEmplyeeNameText(randomEditedEmployeeName)
      await page.waitForLoadState()
      await expect(page.locator(editedElement)).toBeVisible();
    });

    test('Admin user should delete employees', async ({ page }) => {
      const pimPage = new PimPage(page);
      const element = await pimPage.locateTrashBinByRandomEmployeeName(randomNewEmployeeName);
      const elementEdited = await pimPage.locateTrashBinByRandomEmployeeName(randomEditedEmployeeName);
      await page.locator(element).click();
      await pimPage.confirmDeleteButton.click();
      await page.locator(elementEdited).click();
      await pimPage.confirmDeleteButton.click();
    });
});







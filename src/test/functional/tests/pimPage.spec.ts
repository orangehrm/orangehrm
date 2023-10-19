import { test, expect } from '@playwright/test';
import { PimPage } from '../pages/PimPage';
import { LoginPage } from '../pages/LoginPage';
import { adminUserTestData } from '../data';
import { SubPage } from '../pages/BasePage';
import { generateRandomString } from '../utils/helper';
import { newEmployeeTestData } from '../data';


test.describe('Admin user should be able to manage on pim page', () => {
    let loginPage: LoginPage;
    let pimPage: PimPage;
    const randomNewEmpoyeeName = generateRandomString(3);
  
    test.beforeEach(async ({ page }) => {
      loginPage = new LoginPage(page);
      pimPage = new PimPage(page);
      await loginPage.initialize();
      await loginPage.navigateToMainPage();
      await loginPage.loginUser(
        adminUserTestData.userName,
        adminUserTestData.password
      );
      await pimPage.navigateToSubPage(SubPage.BUZZ);
    });

    test.afterEach(async ({ page }) => {
      pimPage = new PimPage(page);
      const element = await pimPage.locateTrashBinByRandomEmployeeName(randomNewEmpoyeeName)
      await page.locator(element).click()
      await pimPage.confirmDeleteButton.click()
    })

  
    test('Admin User Should Add Employee', async ({ page }) => {
      await pimPage.navigateToSubPage(SubPage.PIM);
      await pimPage.addEmployee(newEmployeeTestData.firstName + randomNewEmpoyeeName);
      await pimPage.navigateToSubPage(SubPage.PIM);
      const element = await pimPage.randomNewEmplyeeNameText(randomNewEmpoyeeName)
      await expect(page.locator(element)).toBeVisible();
    });
  });
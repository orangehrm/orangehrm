import { expect, test } from '@playwright/test';

import { userTestData } from '../data';
import { LoginPage } from '../pages/LoginPage';
import { PimPage } from '../pages/PimPage';

let loginPage: LoginPage, pimPage: PimPage;

test.describe('Adding an employee', () => {
  test.beforeAll(async ({ page }) => {
    loginPage = new LoginPage(page);
    await loginPage.initialize();
    await loginPage.navigateToPIMPage();
    await loginPage.loginUser(userTestData.name, userTestData.password);
    await expect(page).toHaveURL(userTestData.pimPage);
  });

  test('Add new employee user & create employee account', async ({ page }) => {
    pimPage = new PimPage(page);
    await pimPage.addEmployeeWithLoginCredentails();
  });

  test('Add new employee user & fill new employee personal details', async ({ page }) => {
    await pimPage.fillNewEmployeePersonalDetails();
    await page.waitForTimeout(5000); //for debugging, to remove later
  });

  test.afterAll(async () => {
    await loginPage.close();
  });
});

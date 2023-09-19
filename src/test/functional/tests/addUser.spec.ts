import { test } from '@playwright/test';

import { userTestData } from '../data';
import { LoginPage } from '../pages/LoginPage';
import { PimPage } from '../pages/PimPage';

test.describe('Adding an employee', () => {
  test.beforeEach(async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.initialize();
    await loginPage.navigateToPIMPage();
    await loginPage.loginUser(userTestData.name, userTestData.password);
  });

  test.afterEach(async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.close();
  });

  test('Add new employee user - full flow', async ({ page }) => {
    const pimPage = new PimPage(page);
    await pimPage.addEmployee();
    await page.waitForTimeout(5000);
  });
});

import { expect, test } from '@playwright/test';

import { adminUserTestData } from '../data';
import { LoginPage } from '../pages/LoginPage';
import { PimPage } from '../pages/PimPage';

let loginPage: LoginPage, pimPage: PimPage;

test.describe('Adding a new employee', () => {
  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    pimPage = new PimPage(page);
    await loginPage.initialize();
    await loginPage.loginUser(adminUserTestData.userName, adminUserTestData.password);
    await loginPage.navigateToSubPage('PIM');
    await expect(page.getByRole('heading', { name: 'Employee Information' })).toBeVisible();
  });

  test.afterEach(async () => {
    await loginPage.close();
  });

  test('Add new employee user with account  & fill new employee personal details', async () => {
    await pimPage.addEmployeeWithLoginCredentials();
    await pimPage.fillNewEmployeePersonalDetails();
  });
});

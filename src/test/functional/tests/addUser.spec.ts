import { expect, test } from '@playwright/test';

import { adminUserTestData, newEmployeeData, userData } from '../data';
import { SubPage } from '../pages/BasePage';
import { LoginPage } from '../pages/LoginPage';
import { PersonalDetailsPage } from '../pages/PersonalDetailsPage';
import { PimPage } from '../pages/PimPage';

let loginPage: LoginPage, pimPage: PimPage, personalDetailsPage: PersonalDetailsPage;
test.describe('Adding a new employee', () => {
  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    pimPage = new PimPage(page);
    personalDetailsPage = new PersonalDetailsPage(page);
    await page.goto('https://opensource-demo.orangehrmlive.com/');
    await loginPage.loginUser('Admin', 'admin123');
    // await loginPage.loginUser(adminUserTestData.userName, adminUserTestData.password);
    await loginPage.navigateToSubPage(SubPage.PIM);
  });

  test('Should create a new user account with personal details', async ({ page }) => {
    await pimPage.addEmployeeWithLoginCredentials(newEmployeeData);
    // await personalDetailsPage.assertIfPersonalDetailsPageIsOpened();
    await personalDetailsPage.fillNewEmployeePersonalDetails(userData);
    await personalDetailsPage.savePersonalDetails();
    await expect(page.locator('#oxd-toaster_1').filter({ hasText: 'Success' })).toBeVisible();
  });
});

import { expect, test } from '@playwright/test';

import { adminUserTestData } from '../data';
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
    await loginPage.loginUser(adminUserTestData.userName, adminUserTestData.password);
    await loginPage.navigateToSubPage(SubPage.PIM);
  });

  test('Should create a new user account with personal details', async ({ page }) => {
    await pimPage.addEmployeeWithLoginCredentials();
    await personalDetailsPage.assertIfPersonalDetailsPageIsOpened();
    await personalDetailsPage.fillNewEmployeePersonalDetails();
    await personalDetailsPage.savePersonalDetails();
    await expect(page.locator('#oxd-toaster_1').filter({ hasText: 'Success' })).toBeVisible();
  });
});

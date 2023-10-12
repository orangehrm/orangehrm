import { LoginPage } from '../pages/LoginPage';
import { test, expect } from '@playwright/test';
import { normalUserTestData, adminUserTestData } from '../data';


const adminName = 'Admin User'
const userName = 'Normal User'


test.describe('Login Page', () => {
  let loginPage: LoginPage;
  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    await loginPage.initialize();
  });

  test('Admin User Should Be Logged In', async ({ page }) => {
    await loginPage.navigateToMainPage()
    await loginPage.loginUser(adminUserTestData.userName, adminUserTestData.password)
    await expect(loginPage.chooseDropdownOptionByText('Admin User')).toHaveText(adminName);
  });

  test('Regular User Should Be Logged In', async ({ page }) => {
    await loginPage.navigateToMainPage()
    await loginPage.loginUser(normalUserTestData.userName, normalUserTestData.password)
    await expect(loginPage.chooseDropdownOptionByText('Normal User')).toHaveText(userName);
  });
});

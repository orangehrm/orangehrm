import { LoginPage } from "../pages/LoginPage";
import { test, expect } from "@playwright/test";
import { normalUserTestData, adminUserTestData } from "../data";
import { loginPageAssertion } from "../assertions";

test.describe("Login Page", () => {
  let loginPage: LoginPage;
  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    await loginPage.initialize();
  });

  test("Test 1: Admin User Should Be Logged In", async ({ page }) => {
    await loginPage.navigateToMainPage()
    await loginPage.loginUser(adminUserTestData.userName, adminUserTestData.password)
    await expect(loginPage.userNameAfterLogin).toHaveText(loginPageAssertion.adminName);
  });

  test("Test 2: Regular User Should Be Logged In", async ({ page }) => {
    await loginPage.navigateToMainPage()
    await loginPage.loginUser(normalUserTestData.userName, normalUserTestData.password)
    await expect(loginPage.userNameAfterLogin).toHaveText(loginPageAssertion.userName);
  });
});

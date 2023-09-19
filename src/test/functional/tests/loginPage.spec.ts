import { LoginPage } from "../pages/LoginPage";
import { test, expect } from "@playwright/test";
import { userTestData } from "../data";
import { loginPageAssertion } from "../assertions";

test.describe("Login Page", () => {
  test.beforeEach(async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.initialize();
  });

  test.afterEach(async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.close();
  });

  test("Test Case 1: Login As An Admin", async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.navigateToMainPage()
    await loginPage.loginUser(userTestData.adminName, userTestData.password)
    await expect(loginPage.userNameAfterLogin).toHaveText(loginPageAssertion.adminName);
  });

  test("Test Case 2: Login As A User", async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.navigateToMainPage()
    await loginPage.loginUser(userTestData.userName, userTestData.password)
    await expect(loginPage.userNameAfterLogin).toHaveText(loginPageAssertion.userName);
  });
});

import { LoginPage } from "../pages/LoginPage";
import { test, expect } from "@playwright/test";
import { userTestData } from "../data";
import { loginPageLocator } from "../locators";
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

  test("Test Case 1: Login User", async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.navigateToMainPage()
    await loginPage.loginUser(userTestData.name, userTestData.password)
    await expect(page.locator(loginPageLocator.userNameAfterLogin)).toHaveText(loginPageAssertion.userName)
    // await page.waitForTimeout(5000); dodane na ta chwile w celach debugowych
  });










});

// buzzPage.spec.ts
import { LoginPage } from "../pages/LoginPage";
import { BuzzPage } from "../pages/BuzzPage";
import { test, expect } from "@playwright/test";
import { adminUserTestData } from "../data";
import { buzzPageAssertions } from "../assertions";

test.describe("Login Page", () => {
  let loginPage: LoginPage;
  let buzzPage: BuzzPage;

  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    buzzPage = new BuzzPage(page);
    await loginPage.initialize();
    await loginPage.navigateToMainPage();
    await loginPage.loginUser(adminUserTestData.userName, adminUserTestData.password);
  });

  test("Test Case 1: Share file and Delete file", async ({ page }) => {
    await buzzPage.navigateToSubPage('span:has-text("Buzz")');
    await buzzPage.sharePhotos();
    await page.reload();
    await expect(buzzPage.photoImg.first()).toBeAttached();
    await buzzPage.deletePhotos();
    await expect(buzzPage.noPostParagraph.first()).toHaveText(buzzPageAssertions.noPostAvailable);
  });
});

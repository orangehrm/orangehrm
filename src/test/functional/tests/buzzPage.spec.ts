import { LoginPage } from "../pages/LoginPage";
import { BuzzPage } from "../pages/BuzzPage";
import { test, expect } from "@playwright/test";
import { adminUserTestData } from "../data";
import { buzzPageAssertions } from "../assertions";


test.describe("Buzz Page", () => {
  let loginPage: LoginPage;
  let buzzPage: BuzzPage;

  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    buzzPage = new BuzzPage(page);
    await loginPage.initialize();
    await loginPage.navigateToMainPage();
    await loginPage.loginUser(adminUserTestData.userName, adminUserTestData.password);
  });

  test("Test Case 1: Share Post and Delete Post", async ({ page }) => {
    const filePath = '../test/functional/files/file1.png';
    await buzzPage.navigateToSubPage(buzzPage.buzzPageButton);
    await buzzPage.sharePhotos(filePath);
    await page.reload();
    await expect(buzzPage.photoImg.first()).toBeAttached();
    await buzzPage.deletePhotos();
    await expect(buzzPage.noPostParagraph.first()).toHaveText(buzzPageAssertions.noPostAvailable);
  });

  test.only("Test Case 1: Edit Post", async ({ page }) => {
    const filePath = '../test/functional/files/file1.png';
    await buzzPage.navigateToSubPage(buzzPage.buzzPageButton);
    await buzzPage.sharePhotos(filePath);
    await page.reload();
    await expect(buzzPage.photoImg.first()).toBeAttached();
    await buzzPage.editPost()
    await expect(page.locator('p:has-text("post edited")')).toBeAttached();
  });
});

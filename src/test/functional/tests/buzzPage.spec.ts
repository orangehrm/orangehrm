import { LoginPage } from "../pages/LoginPage";
import { BuzzPage } from "../pages/BuzzPage";
import { test, expect } from "@playwright/test";
import { adminUserTestData } from "../data";
import { buzzPageAssertions } from "../assertions";

const postEdited = "post edited"
const filePath = '../test/functional/files/file1.png';

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

  test("Test 1: Post Should Be Shared and Deleted", async ({ page }) => {
    await buzzPage.navigateToSubPage(buzzPage.buzzPageButton);
    await buzzPage.sharePhotos(filePath);
    await page.reload();
    await expect(buzzPage.photoImg.first()).toBeAttached();
    await buzzPage.deletePhotos();
    await expect(buzzPage.noPostParagraph.first()).toHaveText(buzzPageAssertions.noPostAvailable);
  });

  test("Test 2: Post Should Be Edited", async ({ page }) => {
    await buzzPage.navigateToSubPage(buzzPage.buzzPageButton);
    await buzzPage.sharePhotos(filePath);
    await page.reload();
    await buzzPage.editPost(postEdited)
    await expect(page.getByText(postEdited)).toBeAttached();
    await buzzPage.deletePhotos();
  });
});

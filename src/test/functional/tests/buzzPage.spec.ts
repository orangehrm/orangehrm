import { LoginPage } from "../pages/LoginPage";
import { BuzzPage } from "../pages/BuzzPage";
import { test, expect } from "@playwright/test";
import { adminUserTestData } from "../data";

const expectedPostTextAfterEdition = "post edited"
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

  test.afterEach(async ({ page }) => {
    buzzPage = new BuzzPage(page);
    await buzzPage.deleteTheNewestPost()
  });

  test("Post should be shared", async ({ page }) => {
    await buzzPage.navigateToSubPage(buzzPage.buzzPageButton);
    await buzzPage.sharePhotos(filePath);
    await page.reload();
    await expect(buzzPage.photoImg.first()).toBeAttached();
  });

  test("Post should be edited", async ({ page }) => {
    await buzzPage.navigateToSubPage(buzzPage.buzzPageButton);
    await buzzPage.sharePhotos(filePath);
    await page.reload();
    await buzzPage.editTheNewestPost(expectedPostTextAfterEdition)
    await expect(page.getByText(expectedPostTextAfterEdition)).toBeVisible();
  });
});

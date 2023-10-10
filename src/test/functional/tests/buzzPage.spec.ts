import { LoginPage } from "../pages/LoginPage";
import { BuzzPage } from "../pages/BuzzPage";
import { test, expect } from "@playwright/test";
import { adminUserTestData } from "../data";

const expectedPostTextAfterEdition = "post edited"
const filePath = '../test/functional/files/file1.png';
const videoTitle = 'Video is shared'
const mostLiked = 'Most Liked'
const mostCommented = 'Most commented'
const newComment = 'New comment'

const videoUrl = "https://www.youtube.com/watch?v=7jMlFXouPk8"
const videoUrl2 = "https://www.youtube.com/watch?v=HhtpBDmBY9w"
const videoUrl3 = "https://www.youtube.com/watch?v=zvrJbxWrMBQ"


test.describe("Share, edit and delete post", () => {
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
    await buzzPage.deleteTheNewestPost(false)
  });

  test("Post should be shared", async ({ page }) => {
    await buzzPage.navigateToSubPage(buzzPage.buzzPageButton);
    await buzzPage.sharePost(filePath);
    await page.reload();
    await expect(buzzPage.photoImg.first()).toBeVisible();
  });

  test("Post should be edited", async ({ page }) => {
    await buzzPage.navigateToSubPage(buzzPage.buzzPageButton);
    await buzzPage.sharePost(filePath);
    await page.reload();
    await buzzPage.editTheNewestPost(expectedPostTextAfterEdition)
    await expect(page.getByText(expectedPostTextAfterEdition)).toBeVisible();
  });

  test("Video should be shared", async ({ page }) => {
    await buzzPage.navigateToSubPage(buzzPage.buzzPageButton);
    await buzzPage.shareVideo(videoTitle, videoUrl);
    await expect(page.getByText(videoTitle)).toBeVisible();
  });
});

test.describe("Most liked and most commented post", () => {
  let loginPage: LoginPage;
  let buzzPage: BuzzPage;

  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    buzzPage = new BuzzPage(page);
    await loginPage.initialize();
    await loginPage.navigateToMainPage();
    await loginPage.loginUser(adminUserTestData.userName, adminUserTestData.password);
    await buzzPage.navigateToSubPage(buzzPage.buzzPageButton);
    await buzzPage.shareVideo(videoTitle, videoUrl);
    await buzzPage.shareVideo(mostLiked, videoUrl2);
    await buzzPage.shareVideo(mostCommented, videoUrl3);
    
  });

  test.afterEach(async ({ page }) => {
    buzzPage = new BuzzPage(page);
    await buzzPage.deleteTheNewestPost(true)
    await buzzPage.deleteTheNewestPost(true)
    await buzzPage.deleteTheNewestPost(true)
  });

  test("Most Liked", async ({ page }) => {
    await buzzPage.heart.nth(1).click()
    await buzzPage.mostLikedTab.click()
    await expect(buzzPage.bodyTextPost.first()).toHaveText(mostLiked)
  });

  test("Most Commented", async ({ page }) => {
    await buzzPage.commentPost.nth(0).click()
    await buzzPage.commentInput.nth(0).type(newComment)
    await page.keyboard.press('Enter');
    await buzzPage.mostCommentedTab.click()
    await expect(buzzPage.bodyTextPost.first()).toHaveText(mostCommented)
  });
});

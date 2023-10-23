import { LoginPage } from '../pages/LoginPage';
import { BuzzPage } from '../pages/BuzzPage';
import { test, expect } from '@playwright/test';
import { adminUserTestData } from '../data';
import { generateRandomString } from '../utils/helper';
import { SubPage } from '../pages/BasePage';

const expectedPostTextAfterEdition = 'post edited';
const filePath = '../test/functional/files/file1.png';
const videoTitle = 'Video is shared';
const mostLiked = 'Most Liked';
const mostCommented = 'Most commented';
const newComment = 'New comment';
const simplePostMessage = 'This is new post message'

const videoUrl = 'https://www.youtube.com/watch?v=7jMlFXouPk8';
const videoUrl2 = 'https://www.youtube.com/watch?v=HhtpBDmBY9w';
const videoUrl3 = 'https://www.youtube.com/watch?v=zvrJbxWrMBQ';

test.describe('Share, edit and delete post', () => {
  let loginPage: LoginPage;
  let buzzPage: BuzzPage;

  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    buzzPage = new BuzzPage(page);
    await loginPage.initialize();
    await loginPage.navigateToMainPage();
    await loginPage.loginUser(
      adminUserTestData.userName,
      adminUserTestData.password
    );
  });

  test.afterEach(async ({ page }) => {
    buzzPage = new BuzzPage(page);
    const lpicture = await buzzPage.photoBody.count()
    for (let i = 0; i < lpicture; i++) {
      await buzzPage.deleteTheNewestPost(false);
    }
  });

  test('Post should be shared', async ({ page }) => {
    const randomTitle = generateRandomString(8);
    await buzzPage.navigateToSubPage(SubPage.BUZZ);
    await buzzPage.sharePost(filePath, randomTitle);
    await page.reload();
    const element = await buzzPage.locateElementWithDynamicText(randomTitle)
    await expect(element).toBeVisible();
  });

  test('Post should be edited', async ({ page }) => {
    const randomTitle = generateRandomString(8);
    await buzzPage.navigateToSubPage(SubPage.BUZZ);
    await buzzPage.sharePost(filePath, randomTitle);
    await page.reload();
    await buzzPage.editTheNewestPost(expectedPostTextAfterEdition);
    const element = await buzzPage.locateElementWithDynamicText(expectedPostTextAfterEdition)
    await expect(element).toBeVisible();
  });
});

test.describe('Most liked and most commented post', () => {
  let loginPage: LoginPage;
  let buzzPage: BuzzPage;

  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    buzzPage = new BuzzPage(page);
    await loginPage.initialize();
    await loginPage.navigateToMainPage();
    await loginPage.loginUser(
      adminUserTestData.userName,
      adminUserTestData.password
    );
    await buzzPage.navigateToSubPage(SubPage.BUZZ);
    await buzzPage.shareVideo(videoTitle, videoUrl);
    await buzzPage.shareVideo(mostLiked, videoUrl2);
    await buzzPage.shareVideo(mostCommented, videoUrl3);
  });

  test.afterEach(async ({ page }) => {
    buzzPage = new BuzzPage(page);
    const lvideo = await buzzPage.videoBody.count()
    for (let i = 0; i < lvideo; i++) {
      await buzzPage.deleteTheNewestPost(true);
    }
  });

  test('User should upload 3 videos and verify if most liked is on 1st position in most liked tab', async ({ page }) => {
    await buzzPage.heartButton.nth(1).click();
    await buzzPage.mostLikedTab.click();
    await expect(buzzPage.textPostBody.first()).toHaveText(mostLiked);
    await expect(buzzPage.videoBody).toHaveCount(3);
  });

  test('User should upload 3 videos and verify if most commented is on 1st position in most commented tab', async ({ page }) => {
    await buzzPage.commentPostCloudButtom.nth(0).click();
    await buzzPage.commentInput.nth(0).type(newComment);
    await page.keyboard.press('Enter');
    await buzzPage.mostCommentedTab.click();
    await expect(buzzPage.textPostBody.first()).toHaveText(mostCommented);
    await expect(buzzPage.videoBody).toHaveCount(3);
  });
});

test.describe('User should be able to write and share post', () => {
  let loginPage: LoginPage;
  let buzzPage: BuzzPage;

  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    buzzPage = new BuzzPage(page);
    await loginPage.initialize();
    await loginPage.navigateToMainPage();
    await loginPage.loginUser(
      adminUserTestData.userName,
      adminUserTestData.password
    );
    await buzzPage.navigateToSubPage(SubPage.BUZZ);
  }); 

  test.afterEach(async ({ page }) => {
    buzzPage = new BuzzPage(page);
    const lvideo = await buzzPage.threeDotsIcon.count()
    for (let i = 0; i < lvideo; i++) {
      await buzzPage.deleteTheNewestPost(false);
    }
  })

  test('User shoud be able to write simple post', async ({ page }) => {
    await buzzPage.sendSimplePost(simplePostMessage)
    console.log(simplePostMessage)
    await page.reload();
    const element = await buzzPage.locateElementWithDynamicText(simplePostMessage)
    await expect(element).toBeVisible();
  });

  test('User should be able to share post of other', async ({ page }) => {
    const randomTitle = generateRandomString(8);
    const randomTitleForSharedPost = generateRandomString(8)
    await buzzPage.sharePost(filePath, randomTitle);
    await page.reload();
    await buzzPage.resharePostOfOther(randomTitleForSharedPost)
    const element = await buzzPage.locateElementWithDynamicText(randomTitleForSharedPost)
    await expect(element).toBeVisible();
    await expect(buzzPage.resharedTitleText).toHaveText(randomTitle)
  });
});

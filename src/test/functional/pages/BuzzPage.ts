import { BasePage } from "./BasePage";
import {  expect, Locator, Page } from "@playwright/test";
export class BuzzPage extends BasePage {
    readonly sharePhotosButton: Locator;
    readonly fileInput: Locator;
    readonly submitPhoto: Locator
    readonly threeDotsIcon: Locator
    readonly deletePostParagraph: Locator
    readonly confirmDeleteButton: Locator
    readonly photoImg: Locator
    readonly noPostParagraph: Locator
    readonly buzzPageButton: Locator
  
    constructor(page: Page) {
      super(page)
      this.sharePhotosButton = page.locator('button:has-text("Share Photos")');
      this.fileInput = page.locator('input[type="file"]');
      this.submitPhoto = page.locator('button.oxd-button--main:has-text("Share")');
      this.threeDotsIcon = page.locator('i.oxd-icon.bi-three-dots');
      this.deletePostParagraph = page.locator('p:has-text("Delete Post")');
      this.confirmDeleteButton = page.locator('button:has-text("Yes, Delete")');
      this.photoImg = page.locator('.orangehrm-buzz-photos-item img')
      this.noPostParagraph = page.locator('p:has-text("No Posts Available")')
      this.buzzPageButton = page.locator('span:has-text("Buzz")')
    }

    async sharePhotos(): Promise<void> {
    const filePath = '../test/functional/files/file1.png';
        await this.sharePhotosButton.click();
        const fileInput =  this.fileInput;
        if (!fileInput) {
          console.error("File input element not found.");
          return;
        }
        await fileInput.setInputFiles(filePath);
        await this.submitPhoto.click();
    }

    async deletePhotos(): Promise<void> {
        await this.threeDotsIcon.first().click()
        await this.deletePostParagraph.click();
        await this.confirmDeleteButton.click();
    }
}

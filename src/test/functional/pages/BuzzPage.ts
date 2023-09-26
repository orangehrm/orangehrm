import { BasePage } from "./BasePage";
import { Locator, Page } from "@playwright/test";
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
    readonly editPostParagraph: Locator
  
    constructor(page: Page) {
      super(page)
      this.sharePhotosButton = page.locator('button:has-text("Share Photos")');
      this.fileInput = page.locator('input[type="file"]');
      this.submitPhoto = page.locator('button.oxd-button--main:has-text("Share")');
      this.threeDotsIcon = page.locator('i.oxd-icon.bi-three-dots');
      this.deletePostParagraph = page.locator('p:has-text("Delete Post")');
      this.editPostParagraph = page.locator('p:has-text("Edit Post")');
      this.confirmDeleteButton = page.locator('button:has-text("Yes, Delete")');
      this.photoImg = page.locator('.orangehrm-buzz-photos-item img')
      this.noPostParagraph = page.locator('p:has-text("No Posts Available")')
      this.buzzPageButton = page.locator('span:has-text("Buzz")')
    }
    
    async sharePhotos(filePath:string): Promise<void> {
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

    async editPost(): Promise<void> {
      await this.threeDotsIcon.first().click()
      await this.editPostParagraph.click();
      await this.page.locator('.orangehrm-buzz-post-modal-header-text .oxd-buzz-post .oxd-buzz-post-input').type('post edited')
      await this.page.locator('.oxd-icon-button.orangehrm-photo-input-remove i.bi-x').click()
      await this.page.locator('.orangehrm-buzz-post-modal-actions button').click()
      
  }
}

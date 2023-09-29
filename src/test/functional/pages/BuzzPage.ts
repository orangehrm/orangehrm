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
    readonly textFieldInPostEdit: Locator
    readonly removePictureButton: Locator
    readonly confirmEditedPost: Locator
  
    constructor(page: Page) {
      super(page)
      this.sharePhotosButton = page.getByText("Share Photos");
      this.fileInput = page.locator('input[type="file"]');
      this.submitPhoto = page.locator('button.oxd-button--main:has-text("Share")');
      this.threeDotsIcon = page.locator('i.oxd-icon.bi-three-dots');
      this.deletePostParagraph = page.getByText("Delete Post");
      this.editPostParagraph = page.getByText("Edit Post");
      this.confirmDeleteButton = page.getByText("Yes, Delete");
      this.photoImg = page.locator('.orangehrm-buzz-photos-item img')
      this.noPostParagraph = page.getByText("No Posts Available")
      this.buzzPageButton = page.locator('span:has-text("Buzz")')
      this.textFieldInPostEdit = page.locator('.orangehrm-buzz-post-modal-header-text .oxd-buzz-post .oxd-buzz-post-input')
      this.removePictureButton = page.locator('.oxd-icon-button.orangehrm-photo-input-remove i.bi-x')
      this.confirmEditedPost = page.locator('.orangehrm-buzz-post-modal-actions button')
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

    async editPost(postEdited:string): Promise<void> {
      await this.threeDotsIcon.first().click()
      await this.editPostParagraph.click();
      await this.textFieldInPostEdit.type(postEdited)
      await this.removePictureButton.click()
      await this.confirmEditedPost.click()  
  }
}

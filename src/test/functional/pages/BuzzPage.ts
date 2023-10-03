import { BasePage } from "./BasePage";
export class BuzzPage extends BasePage {
    public readonly sharePhotoButton = this.page.getByRole("button", { name: "Share Photos" })
    public readonly fileInput = this.page.locator('input[type="file"]');
    public readonly submitPhoto = this.page.locator('button.oxd-button--main:has-text("Share")');
    public readonly threeDotsIcon = this.page.locator('i.oxd-icon.bi-three-dots');
    public readonly deletePostParagraph = this.page.getByText("Delete Post"); 
    public readonly editPostParagraph = this.page.getByText("Edit Post");
    public readonly confirmDeleteButton = this.page.getByRole("button", { name: "Yes, Delete" })
    public readonly photoImg = this.page.locator('.orangehrm-buzz-photos-item img')
    public readonly noPostParagraph = this.page.locator('p:has-text("No Posts Available")')
    public readonly buzzPageButton = this.page.locator('span:has-text("Buzz")')
    public readonly textFieldInPostEdit = this.page.locator('.orangehrm-buzz-post-modal-header-text .oxd-buzz-post .oxd-buzz-post-input')
    public readonly removePictureButton = this.page.locator('.oxd-icon-button.orangehrm-photo-input-remove i.bi-x')
    public readonly confirmEditedPost = this.page.locator('.orangehrm-buzz-post-modal-actions button')
    
    async sharePhotos(filePath:string): Promise<void> {
        await this.sharePhotoButton.click();
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

    public async editNewestPost(finalPostText: string): Promise<void> {
      await this.threeDotsIcon.first().click()
      await this.editPostParagraph.click();
      await this.textFieldInPostEdit.type(finalPostText)
      await this.confirmEditedPost.click()  
  }
}

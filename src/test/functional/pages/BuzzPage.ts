import { BasePage } from "./BasePage";
export class BuzzPage extends BasePage {
    protected readonly sharePhotosButton = this.page.getByRole("button", { name: "Share Photos" })
    protected readonly fileInput = this.page.locator('input[type="file"]');
    protected readonly submitPhoto = this.page.getByRole("button", { name: 'Share', exact: true })
    protected readonly threeDotsIcon = this.page.locator('i.oxd-icon.bi-three-dots');
    protected readonly deletePostParagraph = this.page.getByText("Delete Post"); 
    protected readonly editPostParagraph = this.page.getByText("Edit Post");
    protected readonly confirmDeleteButton = this.page.getByRole("button", { name: "Yes, Delete" })
    public readonly photoImg = this.page.locator('.orangehrm-buzz-photos-item img')
    public readonly buzzPageButton = this.page.locator('span:has-text("Buzz")')
    protected readonly textFieldInPostEdit = this.page.locator('.orangehrm-buzz-post-modal-header-text .oxd-buzz-post .oxd-buzz-post-input')
    protected readonly removePictureButton = this.page.locator('.oxd-icon-button.orangehrm-photo-input-remove i.bi-x')
    protected readonly confirmEditedPost = this.page.locator('.orangehrm-buzz-post-modal-actions button')
    
    async sharePost(filePath:string): Promise<void> {
        await this.sharePhotosButton.click();
        const fileInput =  this.fileInput;
        if (!fileInput) {
          console.error("File input element not found.");
          return;
        }
        await fileInput.setInputFiles(filePath);
        await this.submitPhoto.click();
    }

   async deleteTheNewestPost(): Promise<void> {
        await this.threeDotsIcon.first().click()
        await this.deletePostParagraph.click();
        await this.confirmDeleteButton.click();
    }

   async editTheNewestPost(finalPostText: string): Promise<void> {
      await this.threeDotsIcon.first().click()
      await this.editPostParagraph.click();
      await this.textFieldInPostEdit.type(finalPostText)
      await this.confirmEditedPost.click()  
  }
}

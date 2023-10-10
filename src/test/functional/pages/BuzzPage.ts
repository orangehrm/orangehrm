import { BasePage } from "./BasePage";

export class BuzzPage extends BasePage {
    protected readonly sharePhotosButton = this.page.getByRole("button", { name: "Share Photos" })
    protected readonly fileInput = this.page.locator('input[type="file"]');
    protected readonly submitPhoto = this.page.getByRole("button", { name: 'Share', exact: true })
    protected readonly threeDotsIcon = this.page.getByRole('button', { name: '' });
    protected readonly deletePostParagraph = this.page.getByText("Delete Post"); 
    protected readonly editPostParagraph = this.page.getByText("Edit Post");
    protected readonly confirmDeleteButton = this.page.getByRole("button", { name: "Yes, Delete" })
    public readonly buzzPageButton = this.page.getByRole('link', { name: 'Buzz' })
    protected readonly textFieldInPostEdit = this.page.getByRole('dialog').locator('textarea')
    protected readonly confirmEditedPost = this.page.getByRole('dialog').getByRole('button', { name: 'Post' })
    protected readonly shareVideoButton = this.page.getByRole('button', { name: 'Share Video' })
    protected readonly pasteUrlTextarea = this.page.getByPlaceholder("Paste Video URL")
    protected readonly shareVideoParagraph = this.page.getByRole('dialog').getByPlaceholder('What\'s on your mind?')
    public readonly heart = this.page.locator('#heart-svg')
    public readonly mostLikedTab = this.page.getByRole("button", { name: " Most Liked Posts " })
    public readonly mostCommentedTab = this.page.getByRole("button", { name: " Most Commented Posts " })
    public readonly bodyTextPost = this.page.locator('.orangehrm-buzz-post-body-text')
    public readonly commentPost = this.page.getByRole('button', { name: '' })
    public readonly commentInput = this.page.getByPlaceholder('Write your comment...')


    async sharePost(filePath:string, title:string): Promise<void> {
        await this.sharePhotosButton.click();
        await this.shareVideoParagraph.type(title)
        const fileInput =  this.fileInput;
        if (!fileInput) {
          console.error("File input element not found.");
          return;
        }
        await fileInput.setInputFiles(filePath);
        await this.submitPhoto.click();
    }

    async shareVideo(title:string, videoUrl:string): Promise<void> {
      const generateIt = (request: any) => {
        return request.url().startsWith('https://jnn-pa.googleapis.com') && request.url().endsWith('/GenerateIT');
      };
      await this.page.waitForLoadState('networkidle', { timeout: 7000 })
      await this.shareVideoButton.click();
      await this.shareVideoParagraph.type(title)
      await this.pasteUrlTextarea.type(videoUrl)
      await this.page.waitForRequest(generateIt)
      await this.submitPhoto.click()
      await this.page.waitForRequest(generateIt)
  }

   async deleteTheNewestPost(video: boolean | null): Promise<void> {
        if (video === true) {
          const generateIt = (request: any) => {
            return request.url().startsWith('https://jnn-pa.googleapis.com') && request.url().endsWith('/GenerateIT');
          };
          await this.page.waitForRequest(generateIt, { timeout: 7000 });
        }
        await this.threeDotsIcon.first().click()
        await this.deletePostParagraph.click();
        await this.confirmDeleteButton.click();
    }

   async editTheNewestPost(finalPostText: string): Promise<void> {
      await this.threeDotsIcon.first().click()
      await this.editPostParagraph.click();
      await this.textFieldInPostEdit.clear()
      await this.textFieldInPostEdit.fill(finalPostText)
      await this.confirmEditedPost.click()  
  }
}

import { BasePage } from './BasePage';

export class BuzzPage extends BasePage {
    protected readonly sharePhotosButton = this.page.getByRole('button', { name: 'Share Photos' })
    protected readonly fileInputButton = this.page.locator('input[type="file"]');
    public readonly sharingSubmitButton = this.page.getByRole('button', { name: 'Share', exact: true })
    public readonly threeDotsIcon = this.page.getByRole('button', { name: '' });
    protected readonly deletePostParagraphButton = this.page.getByText('Delete Post'); 
    protected readonly editPostParagraphButton = this.page.getByText('Edit Post');
    protected readonly confirmDeleteButton = this.page.getByRole('button', { name: 'Yes, Delete' })
    protected readonly inpostEditTextField = this.page.getByRole('dialog').locator('textarea')
    protected readonly confirmEditedPostButton = this.page.getByRole('dialog').getByRole('button', { name: 'Post' })
    protected readonly shareVideoButton = this.page.getByRole('button', { name: 'Share Video' })
    protected readonly pasteUrlTextarea = this.page.getByPlaceholder('Paste Video URL')
    protected readonly shareVideoParagraph = this.page.getByRole('dialog').getByPlaceholder('What\'s on your mind?')
    public readonly heartButton = this.page.locator('#heart-svg')
    public readonly mostLikedTab = this.page.getByRole('button', { name: ' Most Liked Posts ' })
    public readonly mostCommentedTab = this.page.getByRole('button', { name: ' Most Commented Posts ' })
    public readonly textPostBody = this.page.locator('.orangehrm-buzz-post-body-text')
    public readonly commentPostCloudButtom = this.page.getByRole('button', { name: '' })
    public readonly commentInput = this.page.getByPlaceholder('Write your comment...')
    public readonly photoBody = this.page.locator('.orangehrm-buzz-photos')
    public readonly videoBody = this.page.locator('.orangehrm-buzz-video')
    protected readonly simplePostMessageInput = this.page.getByPlaceholder('What\'s on your mind?')
    protected readonly submitSimplePostButton = this.page.getByRole('button', { name: 'Post', exact: true })
    public readonly postBody = this.page.locator('.orangehrm-buzz-post-body')
    public readonly resharePostButton = this.page.locator('.bi-share-fill')
    public readonly resharedTitleText = this.page.locator('.orangehrm-buzz-post-body-original-text')


    public async locateElementWithDynamicTextAndPhoto(randomTitle: string) {
      const element = this.postBody.locator(`:has(:text('${randomTitle}')):has(.orangehrm-buzz-photos) .oxd-text.oxd-text--p.orangehrm-buzz-post-body-text`)
      return element;
    }

    public async locateElementWithDynamicText(randomTitle: string) {
      const element = this.postBody.locator(`:has(:text('${randomTitle}'))`)
      return element;
    }


    async sharePost(filePath:string, title:string): Promise<void> {
        await this.sharePhotosButton.click();
        await this.shareVideoParagraph.type(title)
        const fileInput =  this.fileInputButton;
        if (!fileInput) {
          console.error('File input element not found.');
          return;
        }
        await fileInput.setInputFiles(filePath);
        await this.sharingSubmitButton.click({force:true});
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
      await this.sharingSubmitButton.click()
      await this.page.waitForRequest(generateIt)
  }

   async deleteTheNewestPost(containsVideo?: boolean | null): Promise<void> {
        if (containsVideo === true) {
          const generateIt = (request: any) => {
            return request.url().startsWith('https://jnn-pa.googleapis.com') && request.url().endsWith('/GenerateIT');
          };
          await this.page.waitForRequest(generateIt, { timeout: 7000 });
        }
        else {
          await this.page.waitForTimeout(1500)
        }
        await this.threeDotsIcon.first().click({force:true})
        await this.deletePostParagraphButton.click();
        await this.confirmDeleteButton.click();
    }

   async editTheNewestPost(finalPostText: string): Promise<void> {
      await this.threeDotsIcon.first().click()
      await this.editPostParagraphButton.click();
      await this.inpostEditTextField.clear()
      await this.inpostEditTextField.fill(finalPostText)
      await this.confirmEditedPostButton.click()  
  }

  async sendSimplePost(simplePostMessage:string): Promise<void> {
    await this.simplePostMessageInput.type(simplePostMessage)
    await this.submitSimplePostButton.click()
 }

  async resharePostOfOther(title:string): Promise<void> {
    await this.resharePostButton.last().click()
    await this.shareVideoParagraph.type(title)
    await this.sharingSubmitButton.click()
  }
}

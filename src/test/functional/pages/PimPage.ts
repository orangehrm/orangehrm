import { BasePage } from './BasePage';
import { newEmployeeTestData } from '../data';

export class PimPage extends BasePage {
    protected readonly sharePhotosButton = this.page.getByRole('button', { name: 'Share Photos' })
    protected readonly addEmployeeButton = this.page.getByRole('button', { name: 'Add' })
    protected readonly firstNameInput = this.page.getByPlaceholder('First Name')
    protected readonly middleNameInput = this.page.getByPlaceholder('Middle Name')
    protected readonly lastNameInput = this.page.getByPlaceholder('Last Name')
    protected readonly saveUserButton = this.page.getByRole('button', { name: 'Save' })
    public readonly confirmDeleteButton = this.page.getByRole('button', { name: 'Yes, Delete' })
    public readonly multiplyDelete = this.page.getByRole('button', { name: 'Delete Selected' })
    protected readonly employeeNameInput = this.page.getByPlaceholder('Type for hints...').first()
    protected readonly employeeIdInput = this.page.getByRole('textbox').nth(2)
    protected readonly searchEmployeeButton = this.page.getByRole('button', { name: 'Search' })
    public readonly resetButton = this.page.getByRole('button', { name: 'Reset' })

    public async getLocatorByRandomNewEmplyeeName(randomNewEmpoyeeName: string) {
      const element = `.oxd-table-cell:has(:text("${randomNewEmpoyeeName}"))`;
      return element
    }

    public async getTrashBinByRandomEmployeeName(randomNewEmpoyeeName: string) {
      const element = `.oxd-table-card:has(:text("${randomNewEmpoyeeName}")) .oxd-icon.bi-trash`;
      return element
    }

    public async getEditIconByRandomEmployeeName(randomNewEmpoyeeName: string) {
      const element = `.oxd-table-card:has(:text("${randomNewEmpoyeeName}")) .oxd-icon.bi-pencil-fill`;
      return element
    }

    public async getCheckIconByRandomEmployeeName(randomNewEmpoyeeName: string) {
      const element = `.oxd-table-card:has(:text("${randomNewEmpoyeeName}")) .oxd-checkbox-input-icon`;
      return element
    }

    public async addEmployee(firstRandomName:string) {
        await this.addEmployeeButton.click()
        await this.firstNameInput.type(firstRandomName)
        await this.middleNameInput.type(newEmployeeTestData.middleName)
        await this.lastNameInput.type(newEmployeeTestData.lastName)
        await this.saveUserButton.click()
      }

    public async editEmployee(randomEditedEmployeeName:string) {
      await this.page.waitForLoadState('load')
      await this.firstNameInput.fill(randomEditedEmployeeName)
      await this.saveUserButton.click()
      }
  
    public async searchEmployeeByName(employeeName:string) {
      await this.employeeNameInput.type(employeeName)
      await this.searchEmployeeButton.click()
    }

    public async searchEmployeeById(employeeId:string) {
      await this.employeeIdInput.type(employeeId)
      await this.searchEmployeeButton.click()
    }
}

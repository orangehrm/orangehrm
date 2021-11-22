<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
 -->
<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">Edit Vacancy</oxd-text>
      <oxd-divider />

      <oxd-form novalidate="true" :loading="isLoading" @submitValid="onSave">
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Vacancy Name"
              v-model="vacancy.name"
              required
              :rules="rules.name"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <jobtitle-dropdown
              v-model="vacancy.jobTitle"
              :rules="rules.jobTitle"
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangehrm-grid-item-span-2">
            <oxd-input-field
              type="textarea"
              label="Description"
              placeholder="Type description here"
              v-model="vacancy.description"
              :rules="rules.description"
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-autocomplete
              :params="{
                includeEmployees: 'onlyCurrent',
              }"
              required
              v-model="vacancy.hiringManager"
              :rules="rules.hiringManager"
              label="Hiring Manager"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-input-field
                label="Number Of Positions"
                v-model.number="vacancy.numOfPositions"
                :rules="rules.numOfPositions"
              />
            </oxd-grid>
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangerhrm-switch-wrapper">
            <oxd-text class="orangehrm-text" tag="label">Active</oxd-text>
            <oxd-switch-input v-model="vacancy.status" />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangerhrm-switch-wrapper">
            <oxd-text class="orangehrm-text" tag="label">
              Publish in RSS feed and web page
            </oxd-text>
            <oxd-switch-input v-model="vacancy.isPublished" />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-grid :cols="1" class="orangehrm-full-width-grid">
          <div class="orangehrm-container orangehrm-container--border">
            <vacancy-link-card
              label="RSS Feed URL"
              url="http://php74/orangehrm/symfony/web/index.php/recruitmentApply/jobs.rss"
            />
            <vacancy-link-card
              label="Web Page URL"
              url="http://php74/orangehrm/symfony/web/index.php/recruitmentApply/jobs.html"
            />
          </div>
        </oxd-grid>
        <br />
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button displayType="ghost" label="Cancel" @click="onCancel" />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <br />
    <div class="orangehrm-card-container" v-if="isAddClicked && !editClicked">
      <oxd-text
        tag="h6"
        class="orangehrm-main-title orangehrm-attachment-header__title"
        >Add Attachment</oxd-text
      >
      <oxd-divider />
      <oxd-form :loading="isLoadingAttachment" @submitValid="onSaveAttachment">
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <file-upload-input
              label="Select File"
              buttonLabel="Browse"
              v-model:newFile="vacancyAttachment.newAttachment"
              v-model:method="vacancyAttachment.method"
              :file="vacancyAttachment.oldAttachment"
              :rules="rules.addAttachment"
              :url="`recruitment/vacancyAttachment/attachId`"
              :hint="$t('general.file_upload_notice')"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              type="textarea"
              label="Comment"
              placeholder="Type comment here"
              v-model="vacancyAttachment.comment"
            />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            displayType="ghost"
            label="Cancel"
            @click="updateVisibility"
          />
          <submit-button label="Upload" />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <div class="orangehrm-card-container" v-if="isEditClicked && !isAddClicked">
      <oxd-text
        tag="h6"
        class="orangehrm-main-title orangehrm-attachment-header__title"
        >Edit Attachment</oxd-text
      >
      <oxd-divider />
      <oxd-form
        :loading="isLoadingAttachment"
        @submitValid="onUpdateAttachment"
      >
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <file-upload-input
              label="Select File"
              buttonLabel="Browse"
              v-model:newFile="vacancyAttachment.newAttachment"
              v-model:method="vacancyAttachment.method"
              :file="vacancyAttachment.oldAttachment"
              :rules="rules.updateAttachment"
              :url="`recruitment/viewVacancyAttachment/attachId`"
              :hint="$t('general.file_upload_notice')"
              :deletable="false"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              type="textarea"
              label="Comment"
              placeholder="Type comment here"
              v-model="vacancyAttachment.comment"
            />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            displayType="ghost"
            label="Cancel"
            @click="updateVisibility"
          />
          <submit-button label="Upload" />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <br />
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container orangehrm-attachment-header">
        <oxd-text
          tag="h6"
          class="orangehrm-main-title orangehrm-attachment-header__title"
          >Attachments</oxd-text
        >
        <oxd-button
          label="Add"
          iconName="plus"
          displayType="text"
          @click="onClickAdd"
          v-if="!isAddClicked && !isEditClicked"
        />
      </div>
      <table-header
        :selected="checkedItems.length"
        :loading="isLoadingTable"
        :total="attachments.length"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="attachments"
          :selectable="true"
          :clickable="false"
          v-model:selected="checkedItems"
          :loading="isLoadingTable"
          rowDecorator="oxd-table-decorator-card"
        />
      </div>
      <delete-confirmation ref="deleteDialog"></delete-confirmation>
      <br />
      <br />
    </div>
  </div>
</template>
<script>
import {ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import DeleteConfirmationDialog from '@orangehrm/components/dialogs/DeleteConfirmationDialog';
import SwitchInput from '@orangehrm/oxd/core/components/Input/SwitchInput';
import FileUploadInput from '@/core/components/inputs/FileUploadInput';

import {
  required,
  shouldNotExceedCharLength,
  digitsOnly,
  max,
  validFileTypes,
  maxFileSize,
} from '@orangehrm/core/util/validation/rules';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import VacancyLinkCard from '../components/VacancyLinkCard.vue';

const vacancyModel = {
  jobTitle: null,
  name: '',
  hiringManager: null,
  numOfPositions: '',
  description: '',
  status: false,
  isPublished: false,
};
const VacancyAttachmentModel = {
  id: null,
  comment: '',
  oldAttachment: '',
  newAttachment: null,
  method: 'keepCurrent',
};

const attachmentNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      vacancyId: item.vacancyId,
      fileName: item.attachment.fileName,
      fileSize: +(item.attachment.fileSize / 1024).toFixed(2) + ' kb',
      fileType: item.attachment.fileType,
      comment: item.comment,
      attachmentType: item.attachmentType,
    };
  });
};

export default {
  props: {
    vacancyId: {
      type: Number,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
  },
  name: 'edit-job-vacancy',
  components: {
    'oxd-switch-input': SwitchInput,
    'employee-autocomplete': EmployeeAutocomplete,
    'jobtitle-dropdown': JobtitleDropdown,
    'vacancy-link-card': VacancyLinkCard,
    'delete-confirmation': DeleteConfirmationDialog,
    'file-upload-input': FileUploadInput,
  },

  data() {
    return {
      isLoading: false,
      isLoadingAttachment: false,
      isLoadingTable: false,
      isAddClicked: false,
      isEditClicked: false,
      rssFeedUrl: '',
      webFeedUrl: '',
      fileUrl: 'recruitment/viewVacancyAttachment/attachId',
      currentName: '',
      vacancy: {...vacancyModel},
      vacancyAttachment: {...VacancyAttachmentModel},
      rules: {
        jobTitle: [required],
        name: [required, shouldNotExceedCharLength(50)],
        hiringManager: [required],
        numOfPositions: [max(99), digitsOnly],
        description: [],
        status: [required],
        isPublished: [required],
        addAttachment: [
          required,
          maxFileSize(this.maxFileSize),
          validFileTypes(this.allowedFileTypes),
        ],
        updateAttachment: [
          v => {
            if (this.vacancyAttachment.method == 'replaceCurrent') {
              return required(v);
            } else {
              return true;
            }
          },
          validFileTypes(this.allowedFileTypes),
          maxFileSize(this.maxFileSize),
        ],
      },
      headers: [
        {
          name: 'fileName',
          slot: 'title',
          title: 'File Name',
          style: {flex: 3},
        },
        {
          name: 'fileSize',
          title: 'Size',
          style: {flex: 2},
        },
        {
          name: 'fileType',
          title: 'Type',
          style: {flex: 2},
        },
        {
          name: 'comment',
          title: 'Comment',
          style: {flex: 4},
        },
        {
          name: 'actions',
          slot: 'action',
          title: 'Actions',
          style: {flex: 2},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            delete: {
              onClick: this.onClickDelete,
              component: 'oxd-icon-button',
              props: {
                name: 'trash',
              },
            },
            download: {
              onClick: this.downloadFile,
              props: {
                name: 'download',
              },
            },
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: 'pencil-fill',
              },
            },
          },
        },
      ],
      attachments: [],
      checkedItems: [],
    };
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/recruitment/vacancies',
    );
    const httpAttachments = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/recruitment/vacancy/attachments',
    );
    return {
      http,
      httpAttachments,
    };
  },

  methods: {
    onCancel() {
      navigate('/recruitment/viewJobVacancy');
    },
    onSave() {
      this.isLoading = true;
      this.vacancy = {
        name: this.vacancy.name,
        jobTitleId: this.vacancy.jobTitle.id,
        employeeId: this.vacancy.hiringManager.id,
        numOfPositions: this.vacancy.numOfPositions,
        description: this.vacancy.description,
        status: this.vacancy.status ? 1 : 2,
        isPublished: this.vacancy.isPublished,
      };
      this.http
        .update(this.vacancyId, {...this.vacancy})
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          navigate(`/recruitment/addJobVacancy/${this.vacancyId}`);
        });
    },
    onSaveAttachment() {
      this.isLoadingAttachment = true;
      this.httpAttachments
        .create({
          vacancyId: parseInt(this.vacancyId),
          attachment: this.vacancyAttachment.newAttachment
            ? this.vacancyAttachment.newAttachment
            : undefined,
          comment: this.vacancyAttachment.comment,
          attachmentType: 1,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.isLoadingAttachment = false;
          this.updateVisibility();
          this.resetDataTable();
        });
    },
    async getAttachments() {
      this.isLoadingTable = true;
      const result = await this.httpAttachments.get(this.vacancyId);
      const {data} = result.data;
      this.attachments = attachmentNormalizer(data);
      this.isLoadingTable = false;
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteData([item.id]);
        }
      });
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map(index => {
        return this.attachments[index]?.id;
      });
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteData(ids);
        }
      });
    },

    async deleteData(items) {
      if (items instanceof Array) {
        this.isLoadingTable = true;
        this.httpAttachments
          .deleteAll({
            ids: items,
          })
          .then(() => {
            return this.$toast.deleteSuccess();
          })
          .then(() => {
            this.resetDataTable();
            this.isLoadingTable = false;
          });
      }
    },
    async resetDataTable() {
      this.checkedItems = [];
      this.getAttachments();
    },
    onClickAdd() {
      this.isEditClicked = false;
      this.isAddClicked = true;
    },
    onClickEdit(item) {
      this.vacancyAttachment.id = item.id;
      this.vacancyAttachment.comment = item.comment;
      this.vacancyAttachment.oldAttachment = {
        id: item.id,
        filename: item.fileName,
        fileType: item.fileType,
        fileSize: item.filefileSize,
      };
      this.vacancyAttachment.newAttachment = null;
      this.vacancyAttachment.method = 'keepCurrent';
      this.isAddClicked = false;
      this.isEditClicked = true;
    },
    onUpdateAttachment() {
      this.isLoadingAttachment = true;
      this.httpAttachments
        .update(this.vacancyId, {
          id: this.vacancyAttachment.id,
          vacancyId: parseInt(this.vacancyId),
          currentAttachment: this.vacancyAttachment.oldAttachment
            ? this.vacancyAttachment.method
            : undefined,
          attachment: this.vacancyAttachment.newAttachment
            ? this.vacancyAttachment.newAttachment
            : undefined,
          comment: this.vacancyAttachment.comment,
          attachmentType: 1,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.isLoadingAttachment = false;
          this.updateVisibility();
          this.resetDataTable();
        });
    },
    updateVisibility() {
      this.isAddClicked = false;
      this.isEditClicked = false;
      this.vacancyAttachment = {...VacancyAttachmentModel};
    },
    downloadFile(item) {
      if (!item?.id) return;
      const downUrl = `${window.appGlobal.baseUrl}/${this.fileUrl}/${item.id}`;
      window.open(downUrl, '_blank');
    },
  },
  created() {
    this.isLoading = true;
    this.isLoadingTable = true;
    this.getAttachments();
    this.http
      .get(this.vacancyId)
      .then(response => {
        const {data} = response.data;
        this.currentName = data.name;
        this.vacancy.name = data.name;
        this.vacancy.description = data.description;
        this.vacancy.numOfPositions = data.numOfPositions;
        this.vacancy.status = data.status === 1 ? true : false;
        this.vacancy.isPublished = data.isPublished;
        this.vacancy.hiringManager = {
          id: data.hiringManager.id,
          label: `${data.hiringManager.firstName} ${data.hiringManager.middleName} ${data.hiringManager.lastName}`,
          isPastEmployee: data.hiringManager.terminationId ? true : false,
        };
        this.vacancy.jobTitle = {
          id: data.jobTitle.id,
          label: data.jobTitle.title,
        };
        return this.http.getAll({limit: 0});
      })
      .then(response => {
        const {data} = response.data;
        this.rules.name.push(v => {
          const index = data.findIndex(item => {
            return item.name == v && item.name != this.currentName;
          });
          return index === -1 || 'Already exists';
        });
      })
      .finally(() => {
        this.isLoading = false;
        this.isLoadingTable = false;
      });
  },
};
</script>

<style src="./vacancy.scss" lang="scss" scoped></style>

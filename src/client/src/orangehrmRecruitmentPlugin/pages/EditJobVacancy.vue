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
      <oxd-text tag="h6" class="orangehrm-main-title">{{
        $t('recruitment.edit_vacancy')
      }}</oxd-text>
      <oxd-divider />

      <oxd-form novalidate="true" :loading="isLoading" @submitValid="onSave">
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="vacancy.name"
              :label="$t('recruitment.vacancy_name')"
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
              v-model="vacancy.description"
              type="textarea"
              :label="$t('general.description')"
              placeholder="Type description here"
              :rules="rules.description"
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-autocomplete
              v-model="vacancy.hiringManager"
              :params="{
                includeEmployees: 'onlyCurrent',
              }"
              required
              :rules="rules.hiringManager"
              :label="$t('recruitment.hiring_manager')"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-input-field
                v-model.number="vacancy.numOfPositions"
                :label="$t('recruitment.no_of_positions')"
                :rules="rules.numOfPositions"
              />
            </oxd-grid>
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangerhrm-switch-wrapper">
            <oxd-text class="orangehrm-text" tag="p">
              {{ $t('recruitment.active') }}
            </oxd-text>
            <oxd-switch-input v-model="vacancy.status" />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangerhrm-switch-wrapper">
            <oxd-text class="orangehrm-text" tag="p">
              {{ $t('publish _in_rss_feed _and_web_page') }}
            </oxd-text>
            <oxd-switch-input v-model="vacancy.isPublished" />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-grid :cols="1" class="orangehrm-full-width-grid">
          <div class="orangehrm-container orangehrm-container--border">
            <vacancy-link-card
              :label="$t('recruitment.rss_feed_url')"
              url="http://php74/orangehrm/symfony/web/index.php/recruitmentApply/jobs.rss"
            />
            <vacancy-link-card
              :label="$t('recruitment.web_page_url')"
              url="http://php74/orangehrm/symfony/web/index.php/recruitmentApply/jobs.html"
            />
          </div>
        </oxd-grid>
        <br />
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button display-type="ghost" label="Cancel" @click="onCancel" />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <br />
    <div v-if="isAddClicked && !isEditClicked" class="orangehrm-card-container">
      <oxd-text
        tag="h6"
        class="orangehrm-main-title orangehrm-attachment-header__title"
      >
        {{ $t('recruitment.add_attachment') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoadingAttachment" @submitValid="onSaveAttachment">
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <file-upload-input
              v-model:newFile="vacancyAttachment.newAttachment"
              v-model:method="vacancyAttachment.method"
              label="Select File"
              button-label="Browse"
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
              v-model="vacancyAttachment.comment"
              type="textarea"
              label="Comment"
              placeholder="Type comment here"
            />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            label="Cancel"
            @click="updateVisibility"
          />
          <submit-button label="Upload" />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <div v-if="isEditClicked && !isAddClicked" class="orangehrm-card-container">
      <oxd-text
        tag="h6"
        class="orangehrm-main-title orangehrm-attachment-header__title"
      >
        {{ $t('recruitment.edit_attachment') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form
        :loading="isLoadingAttachment"
        @submitValid="onUpdateAttachment"
      >
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <file-upload-input
              v-model:newFile="vacancyAttachment.newAttachment"
              v-model:method="vacancyAttachment.method"
              label="Select File"
              button-label="Browse"
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
              v-model="vacancyAttachment.comment"
              type="textarea"
              label="Comment"
              placeholder="Type comment here"
            />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
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
        >
          {{ $t('recruitment.attachments') }}
        </oxd-text>
        <oxd-button
          v-if="!isAddClicked && !isEditClicked"
          label="Add"
          icon-name="plus"
          display-type="text"
          @click="onClickAdd"
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
          v-model:selected="checkedItems"
          :headers="headers"
          :items="attachments"
          :selectable="true"
          :clickable="false"
          :loading="isLoadingTable"
          row-decorator="oxd-table-decorator-card"
        />
      </div>
      <delete-confirmation ref="deleteDialog"></delete-confirmation>
      <br />
      <br />
    </div>
  </div>
</template>
<script>
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';
import FileUploadInput from '@/core/components/inputs/FileUploadInput';

import {
  required,
  shouldNotExceedCharLength,
  digitsOnly,
  max,
  validFileTypes,
  maxFileSize,
} from '@ohrm/core/util/validation/rules';
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
  oldAttachment: {},
  newAttachment: {},
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
  components: {
    'oxd-switch-input': SwitchInput,
    'employee-autocomplete': EmployeeAutocomplete,
    'jobtitle-dropdown': JobtitleDropdown,
    'vacancy-link-card': VacancyLinkCard,
    'delete-confirmation': DeleteConfirmationDialog,
    'file-upload-input': FileUploadInput,
  },

  props: {
    vacancyId: {
      type: String,
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
  data() {
    return {
      isLoading: false,
      isLoadingAttachment: false,
      isLoadingTable: false,
      isAddClicked: false,
      isEditClicked: false,
      rssFeedUrl: '',
      webFeedUrl: '',
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
  created() {
    this.isLoading = true;
    this.isLoadingTable = true;

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
      .then(() => {
        this.httpAttachments.get(this.vacancyId).then(response => {
          const {data} = response.data;
          this.attachments = attachmentNormalizer(data);
        });
      })
      .finally(() => {
        this.isLoadingTable = false;
        this.isLoading = false;
      });
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
      this.isLoadingTable = true;
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
          this.updateVisibility();
          this.resetDataTable();
          this.isLoadingAttachment = false;
          this.isLoadingTable = false;
        });
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
    resetDataTable() {
      this.checkedItems = [];
      this.httpAttachments.get(this.vacancyId).then(response => {
        const {data} = response.data;
        this.attachments = attachmentNormalizer(data);
      });
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
      this.isLoadingTable = true;
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
          this.updateVisibility();
          this.resetDataTable();
          this.isLoadingAttachment = false;
          this.isLoadingTable = false;
        });
    },
    updateVisibility() {
      this.isAddClicked = false;
      this.isEditClicked = false;
      this.vacancyAttachment = {...VacancyAttachmentModel};
    },
    downloadFile(item) {
      if (!item?.id) return;
      const fileUrl = 'recruitment/viewVacancyAttachment/attachId';
      const downUrl = `${window.appGlobal.baseUrl}/${fileUrl}/${item.id}`;
      window.open(downUrl, '_blank');
    },
  },
};
</script>

<style src="./vacancy.scss" lang="scss" scoped></style>

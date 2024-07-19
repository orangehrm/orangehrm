<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
 -->
<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('recruitment.edit_vacancy') }}
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
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
              required
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangehrm-grid-item-span-2">
            <oxd-input-field
              v-model="vacancy.description"
              type="textarea"
              :label="$t('general.description')"
              :placeholder="$t('general.type_description_here')"
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
              <oxd-grid-item>
                <oxd-input-field
                  v-model="vacancy.numOfPositions"
                  :label="$t('recruitment.num_of_positions')"
                  :rules="rules.numOfPositions"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangerhrm-switch-wrapper">
            <oxd-text class="orangehrm-text" tag="p">
              {{ $t('general.active') }}
            </oxd-text>
            <oxd-switch-input v-model="vacancy.status" />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangerhrm-switch-wrapper">
            <oxd-text class="orangehrm-text" tag="p">
              {{ $t('recruitment.publish_in_rss_feed_and_web_page') }}
            </oxd-text>
            <oxd-switch-input v-model="vacancy.isPublished" />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangehrm-grid-item-span-2">
            <div class="orangehrm-vacancy-links">
              <vacancy-link-card
                :label="$t('recruitment.rss_feed_url')"
                :url="rssFeedUrl"
              />
              <vacancy-link-card
                :label="$t('recruitment.web_page_url')"
                :url="webUrl"
              />
            </div>
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
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
        {{ $t('general.add_attachment') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoadingAttachment" @submit-valid="onSaveAttachment">
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <file-upload-input
              v-model:newFile="vacancyAttachment.newAttachment"
              v-model:method="vacancyAttachment.method"
              :label="$t('general.select_file')"
              :button-label="$t('general.browse')"
              :file="vacancyAttachment.oldAttachment"
              :rules="rules.addAttachment"
              :url="`recruitment/vacancyAttachment/attachId`"
              :hint="
                $t('general.accepts_up_to_n_mb', {count: formattedFileSize})
              "
              required
            />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="vacancyAttachment.comment"
              type="textarea"
              :label="$t('general.comment')"
              :placeholder="$t('general.type_comment_here')"
              :rules="rules.comment"
            />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="updateVisibility"
          />
          <submit-button :label="$t('general.save')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <div v-if="isEditClicked && !isAddClicked" class="orangehrm-card-container">
      <oxd-text
        tag="h6"
        class="orangehrm-main-title orangehrm-attachment-header__title"
      >
        {{ $t('general.edit_attachment') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form
        :loading="isLoadingAttachment"
        @submit-valid="onUpdateAttachment"
      >
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <file-upload-input
              v-model:newFile="vacancyAttachment.newAttachment"
              v-model:method="vacancyAttachment.method"
              :label="$t('general.select_file')"
              :button-label="$t('general.browse')"
              :file="vacancyAttachment.oldAttachment"
              :rules="rules.updateAttachment"
              :url="`recruitment/viewVacancyAttachment/attachId`"
              :hint="
                $t('general.accepts_up_to_n_mb', {count: formattedFileSize})
              "
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
              :label="$t('general.comment')"
              :placeholder="$t('general.type_comment_here')"
              :rules="rules.comment"
            />
          </oxd-grid-item>
        </oxd-grid>
        <br />
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="updateVisibility"
          />
          <submit-button :label="$t('general.save')" />
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
          {{ $t('general.attachments') }}
        </oxd-text>
        <oxd-button
          v-if="!isAddClicked && !isEditClicked"
          :label="$t('general.add')"
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
import FileUploadInput from '@/core/components/inputs/FileUploadInput';
import {
  required,
  numericOnly,
  maxFileSize,
  validSelection,
  validFileTypes,
  shouldNotExceedCharLength,
  numberShouldBeBetweenMinAndMaxValue,
} from '@ohrm/core/util/validation/rules';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import VacancyLinkCard from '../components/VacancyLinkCard.vue';
import {OxdSwitchInput} from '@ohrm/oxd';
import useServerValidation from '@/core/util/composable/useServerValidation';

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
  newAttachment: null,
  method: 'keepCurrent',
};

const basePath = `${window.location.protocol}//${window.location.host}${window.appGlobal.baseUrl}`;

const attachmentNormalizer = (data) => {
  return data.map((item) => {
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
    'oxd-switch-input': OxdSwitchInput,
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
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/vacancies',
    );
    const httpAttachments = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/vacancy/attachments',
    );
    const {createUniqueValidator} = useServerValidation(http);
    const vacancyNameUniqueValidation = createUniqueValidator(
      'Vacancy',
      'name',
      {entityId: props.vacancyId},
    );
    return {
      http,
      httpAttachments,
      vacancyNameUniqueValidation,
    };
  },
  data() {
    return {
      isLoading: false,
      isLoadingAttachment: false,
      isLoadingTable: false,
      isAddClicked: false,
      isEditClicked: false,
      currentName: '',
      vacancy: {...vacancyModel},
      vacancyAttachment: {...VacancyAttachmentModel},
      rules: {
        jobTitle: [required],
        name: [
          required,
          this.vacancyNameUniqueValidation,
          shouldNotExceedCharLength(50),
        ],
        hiringManager: [
          required,
          validSelection,
          (v) => (v?.isPastEmployee ? this.$t('general.invalid') : true),
        ],
        numOfPositions: [
          (value) => {
            if (value === null || value === '') return true;
            return typeof numericOnly(value) === 'string'
              ? numericOnly(value)
              : numberShouldBeBetweenMinAndMaxValue(1, 99)(value);
          },
        ],
        description: [],
        status: [required],
        isPublished: [required],
        addAttachment: [
          required,
          maxFileSize(this.maxFileSize),
          validFileTypes(this.allowedFileTypes),
        ],
        updateAttachment: [
          (v) => {
            if (this.vacancyAttachment.method == 'replaceCurrent') {
              return required(v);
            } else {
              return true;
            }
          },
          validFileTypes(this.allowedFileTypes),
          maxFileSize(this.maxFileSize),
        ],
        comment: [shouldNotExceedCharLength(200)],
      },
      headers: [
        {
          name: 'fileName',
          slot: 'title',
          title: this.$t('general.file_name'),
          style: {flex: 3},
        },
        {
          name: 'fileSize',
          title: this.$t('general.file_size'),
          style: {flex: 2},
        },
        {
          name: 'fileType',
          title: this.$t('general.file_type'),
          style: {flex: 2},
        },
        {
          name: 'comment',
          title: this.$t('general.comment'),
          style: {flex: 4},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
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
      rssFeedUrl: `${basePath}/recruitmentApply/jobs.rss`,
      webUrl: `${basePath}/recruitmentApply/jobs.html`,
    };
  },
  computed: {
    formattedFileSize() {
      return Math.round((this.maxFileSize / (1024 * 1024)) * 100) / 100;
    },
  },
  created() {
    this.isLoading = true;
    this.isLoadingTable = true;

    this.http
      .get(this.vacancyId)
      .then((response) => {
        const {data} = response.data;
        this.currentName = data.name;
        this.vacancy.name = data.name;
        this.vacancy.description = data.description;
        this.vacancy.numOfPositions = data.numOfPositions || '';
        this.vacancy.status = data.status;
        this.vacancy.isPublished = data.isPublished;
        this.vacancy.hiringManager = data.hiringManager.id
          ? {
              id: data.hiringManager.id,
              label: `${data.hiringManager.firstName} ${data.hiringManager.middleName} ${data.hiringManager.lastName}`,
              isPastEmployee: data.hiringManager.terminationId ? true : false,
            }
          : this.$t('general.deleted');
        this.vacancy.jobTitle = data.jobTitle.isDeleted
          ? null
          : {
              id: data.jobTitle.id,
              label: data.jobTitle.title,
            };
      })
      .then(() => {
        this.httpAttachments
          .request({
            method: 'GET',
            url: `/api/v2/recruitment/vacancies/${this.vacancyId}/attachments`,
          })
          .then((response) => {
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
        numOfPositions: this.vacancy.numOfPositions
          ? parseInt(this.vacancy.numOfPositions)
          : null,
        description: this.vacancy.description,
        status: this.vacancy.status,
        isPublished: this.vacancy.isPublished,
      };
      this.http
        .update(this.vacancyId, {...this.vacancy})
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          navigate('/recruitment/addJobVacancy/{id}', {id: this.vacancyId});
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
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteData([item.id]);
        }
      });
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map((index) => {
        return this.attachments[index]?.id;
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
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
      this.httpAttachments
        .request({
          method: 'GET',
          url: `/api/v2/recruitment/vacancies/${this.vacancyId}/attachments`,
        })
        .then((response) => {
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
        .request({
          method: 'PUT',
          url: `/api/v2/recruitment/vacancies/${this.vacancyId}/attachments/${this.vacancyAttachment.id}`,
          data: {
            vacancyId: parseInt(this.vacancyId),
            currentAttachment: this.vacancyAttachment.oldAttachment
              ? this.vacancyAttachment.method
              : undefined,
            attachment: this.vacancyAttachment.newAttachment
              ? this.vacancyAttachment.newAttachment
              : undefined,
            comment: this.vacancyAttachment.comment,
            attachmentType: 1,
          },
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

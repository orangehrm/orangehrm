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
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('admin.organization_structure') }}
        </oxd-text>
        <oxd-switch-input
          v-if="!isLoading"
          v-model="editable"
          :option-label="$t('general.edit')"
          label-position="left"
        />
      </div>
      <oxd-divider
        v-if="!isLoading"
        class="orangehrm-horizontal-margin orangehrm-clear-margins"
      />
      <div v-if="!isLoading" class="org-root-container">
        <oxd-text
          tag="p"
          :class="{
            '--parent': data && data.children != 0,
          }"
        >
          {{ data.name }}
        </oxd-text>
        <oxd-button
          v-show="editable"
          class="org-structure-add"
          :label="$t('general.add')"
          icon-name="plus"
          display-type="secondary"
          @click="onAddOrglevel(data)"
        />
      </div>
      <div class="org-container">
        <div v-if="isLoading" class="loader">
          <oxd-loading-spinner />
        </div>
        <oxd-tree-view
          v-else
          :data="data"
          :open="true"
          :show-root="false"
          class="org-structure"
        >
          <template #content="{nodeData}">
            <oxd-sheet
              type="pastel-white"
              :class="{
                'org-structure-card': true,
                '--edit': editable,
              }"
            >
              <div class="org-name">
                {{
                  nodeData.unitId
                    ? `${nodeData.unitId}: ${nodeData.name}`
                    : `${nodeData.name}`
                }}
              </div>
              <div v-if="editable" class="org-action">
                <oxd-dropdown v-if="isMobile">
                  <oxd-icon-button name="three-dots" :with-container="true" />
                  <template #content>
                    <li
                      class="org-action-description"
                      @click="onDelete(nodeData)"
                    >
                      <oxd-text tag="p">
                        {{ $t('performance.delete') }}
                      </oxd-text>
                    </li>
                    <li
                      class="org-action-description"
                      @click="onEditOrglevel(nodeData)"
                    >
                      <oxd-text tag="p">
                        {{ $t('general.edit') }}
                      </oxd-text>
                    </li>
                    <li
                      class="org-action-description"
                      @click="onAddOrglevel(nodeData)"
                    >
                      <oxd-text tag="p">
                        {{ $t('general.add') }}
                      </oxd-text>
                    </li>
                  </template>
                </oxd-dropdown>
                <template v-else>
                  <oxd-icon-button
                    class="org-action-icon"
                    name="trash-fill"
                    role="none"
                    @click="onDelete(nodeData)"
                  />
                  <oxd-icon-button
                    class="org-action-icon"
                    name="pencil-fill"
                    role="none"
                    @click="onEditOrglevel(nodeData)"
                  />
                  <oxd-icon-button
                    class="org-action-icon"
                    name="plus"
                    role="none"
                    @click="onAddOrglevel(nodeData)"
                  />
                </template>
              </div>
            </oxd-sheet>
          </template>
        </oxd-tree-view>
      </div>
    </div>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
    <save-org-unit
      v-if="showSaveModal"
      :data="saveModalState"
      @close="onSaveModalClose"
    ></save-org-unit>
    <edit-org-unit
      v-if="showEditModal"
      :data="editModalState"
      @close="onEditModalClose"
    ></edit-org-unit>
  </div>
</template>

<script>
import {
  OxdSheet,
  OxdSpinner,
  OxdTreeView,
  DEVICE_TYPES,
  useResponsive,
  OxdSwitchInput,
  OxdDropdownMenu,
} from '@ohrm/oxd';
import {computed} from 'vue';
import SaveOrgUnit from './SaveOrgUnit';
import EditOrgUnit from './EditOrgUnit';
import {APIService} from '@/core/util/services/api.service';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';

export default {
  components: {
    'oxd-sheet': OxdSheet,
    'save-org-unit': SaveOrgUnit,
    'edit-org-unit': EditOrgUnit,
    'oxd-tree-view': OxdTreeView,
    'oxd-dropdown': OxdDropdownMenu,
    'oxd-loading-spinner': OxdSpinner,
    'oxd-switch-input': OxdSwitchInput,
    'delete-confirmation': DeleteConfirmationDialog,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/subunits',
    );
    const responsiveState = useResponsive();
    const isMobile = computed(() => {
      return !(
        responsiveState.screenType === DEVICE_TYPES.DEVICE_LG ||
        responsiveState.screenType === DEVICE_TYPES.DEVICE_XL
      );
    });
    return {
      http,
      isMobile,
    };
  },
  data() {
    return {
      isLoading: false,
      editable: false,
      showSaveModal: false,
      saveModalState: null,
      showEditModal: false,
      editModalState: null,
      data: {},
    };
  },

  created() {
    this.fetchOrgStructure();
  },
  methods: {
    onDelete(node) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.isLoading = true;
          this.http
            .delete(node.id)
            .then(() => {
              return this.$toast.deleteSuccess();
            })
            .then(() => {
              this.isLoading = false;
              this.fetchOrgStructure();
            });
        }
      });
    },
    onAddOrglevel(node) {
      if (this.editable) {
        this.saveModalState = node;
        this.showSaveModal = true;
      }
    },
    onEditOrglevel(node) {
      if (this.editable) {
        this.editModalState = node;
        this.showEditModal = true;
      }
    },
    onSaveModalClose() {
      this.saveModalState = null;
      this.showSaveModal = false;
      this.fetchOrgStructure();
    },
    onEditModalClose() {
      this.editModalState = null;
      this.showEditModal = false;
      this.fetchOrgStructure();
    },
    fetchOrgStructure() {
      this.isLoading = true;
      this.http
        .getAll({
          mode: 'tree',
        })
        .then((response) => {
          const {data} = response.data;
          this.data = data[0];
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>

<style src="./org-structure.scss" lang="scss" scoped></style>

<style lang="scss">
.oxd-tree-node-content {
  width: 100%;
}
.oxd-tree-node-toggle {
  & .oxd-icon-button {
    background-color: $oxd-white-color !important;
  }
}
</style>

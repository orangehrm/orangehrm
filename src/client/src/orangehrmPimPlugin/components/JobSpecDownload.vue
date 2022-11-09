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
  <oxd-input-group :label="$t('general.job_specification')">
    <div :class="{'input-container': true, '--disabled': !file.id}">
      <oxd-text
        class="input-container-filename"
        tag="p"
        :title="file.filename"
        @click="downloadFile"
      >
        {{ file.id ? file.filename : 'Not Defined' }}
      </oxd-text>
      <oxd-icon-button
        v-if="!isLoading && file.id"
        class="input-container-icon"
        name="download"
        @click="downloadFile"
      />
      <oxd-loading-spinner
        v-if="isLoading"
        class="input-container-loader"
        :with-container="false"
      />
    </div>
  </oxd-input-group>
</template>

<script>
import {onBeforeMount, reactive, toRefs} from 'vue';
import {APIService} from '@ohrm/core/util/services/api.service';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner.vue';

export default {
  name: 'JobSpecDownload',
  components: {
    'oxd-loading-spinner': Spinner,
  },
  props: {
    resourceId: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/admin/job-titles/${props.resourceId}/specification`,
    );
    const state = reactive({
      isLoading: false,
      file: {
        id: '',
        filename: '',
        fileType: '',
        fileSize: 0,
      },
    });

    const fetchFile = async () => {
      state.isLoading = true;
      http
        .request({
          method: 'GET',
          // Prevent triggering response interceptor on 404
          validateStatus: status => {
            return (status >= 200 && status < 300) || status == 404;
          },
        })
        .then(({data}) => {
          state.file = {
            ...data.data,
          };
        })
        .finally(() => {
          state.isLoading = false;
        });
    };

    const downloadFile = () => {
      if (!state.file.id) return;
      const downUrl = `${window.appGlobal.baseUrl}/admin/viewJobSpecification/attachId/${state.file.id}`;
      window.open(downUrl, '_blank');
    };

    if (props.resourceId) {
      onBeforeMount(fetchFile);
    }

    return {
      ...toRefs(state),
      downloadFile,
    };
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.input-container {
  position: relative;
  display: flex;
  align-items: center;
  @include oxd-input-control();
  @include oxd-input-control-size();
  border: $oxd-input-control-border--active;
  min-height: 45px;
  cursor: pointer;
  text-decoration: underline;
  &-filename {
    text-overflow: ellipsis;
    overflow: hidden;
    width: 75%;
    white-space: nowrap;
  }
  &-loader {
    position: absolute;
    right: 10px;
  }
  &-icon {
    font-size: inherit !important;
    min-width: unset;
    min-height: unset;
    border-radius: 0.65rem;
    padding: 0.3rem;
    margin-left: auto;
  }
  &.--disabled {
    cursor: not-allowed;
    text-decoration: none;
  }
  ::v-deep(.oxd-loading-spinner) {
    width: 1rem;
    height: 1rem;
  }
}
</style>

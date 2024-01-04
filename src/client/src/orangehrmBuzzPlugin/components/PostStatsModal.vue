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
  <oxd-sheet
    v-if="!mobile"
    :gutters="false"
    type="white"
    class="orangehrm-buzz-stats-modal"
  >
    <div
      v-for="user in users"
      :key="user"
      class="orangehrm-buzz-stats-modal-employee"
    >
      <profile-image :employee="user.employee"></profile-image>
      <oxd-text tag="p" class="orangehrm-buzz-stats-modal-employee-name">
        {{ user.fullName }}
      </oxd-text>
    </div>
    <oxd-loading-spinner v-if="isLoading" class="orangehrm-buzz-loader" />
  </oxd-sheet>
  <oxd-dialog v-else class="orangehrm-buzz-stats-dialog" @update:show="onClose">
    <div class="orangehrm-buzz-stats-dialog-header">
      <oxd-icon
        :class="{
          'orangehrm-buzz-stats-dialog-icon': true,
          '--likes': type === 'likes',
        }"
        :name="icon"
        :with-container="true"
      />
      <oxd-text v-if="type === 'shares'">
        {{ $t('buzz.n_share', {shareCount: total}) }}
      </oxd-text>
      <oxd-text v-if="type === 'likes'">
        {{ $t('buzz.n_like', {likesCount: total}) }}
      </oxd-text>
    </div>
    <oxd-divider />
    <div
      v-for="user in users"
      :key="user"
      class="orangehrm-buzz-stats-dialog-employee"
    >
      <profile-image :employee="user.employee"></profile-image>
      <oxd-text tag="p" class="orangehrm-buzz-stats-dialog-employee-name">
        {{ user.fullName }}
      </oxd-text>
    </div>
    <oxd-loading-spinner v-if="isLoading" class="orangehrm-buzz-loader" />
  </oxd-dialog>
</template>

<script>
import {onBeforeMount, reactive, toRefs} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import ProfileImage from '@/orangehrmBuzzPlugin/components/ProfileImage';
import useInfiniteScroll from '@/core/util/composable/useInfiniteScroll';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import {OxdDialog, OxdIcon, OxdSheet, OxdSpinner} from '@ohrm/oxd';

export default {
  name: 'PostStatsModal',

  components: {
    'oxd-icon': OxdIcon,
    'oxd-sheet': OxdSheet,
    'oxd-dialog': OxdDialog,
    'profile-image': ProfileImage,
    'oxd-loading-spinner': OxdSpinner,
  },

  props: {
    postId: {
      type: Number,
      required: true,
    },
    type: {
      type: String,
      required: true,
    },
    icon: {
      type: String,
      required: true,
    },
    mobile: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['close'],

  setup(props, context) {
    let apiPath;
    const EMPLOYEE_LIMIT = 10;
    const {$tEmpName} = useEmployeeNameTranslate();

    switch (props.type) {
      case 'likes':
        apiPath = `/api/v2/buzz/shares/${props.postId}/likes`;
        break;

      case 'shares':
        apiPath = `/api/v2/buzz/posts/${props.postId}/shares`;
        break;

      default:
        break;
    }

    const http = new APIService(window.appGlobal.baseUrl, apiPath);

    const state = reactive({
      total: 0,
      offset: 0,
      users: [],
      isLoading: false,
    });

    const fetchData = () => {
      state.isLoading = true;
      http
        .getAll({
          limit: EMPLOYEE_LIMIT,
          offset: state.offset,
        })
        .then((response) => {
          const {data, meta} = response.data;
          state.total = meta?.total || 0;
          if (Array.isArray(data)) {
            const _data = data.map((user) => {
              const {employee} = user;
              return {
                employee,
                fullName: $tEmpName(employee, {
                  includeMiddle: false,
                  excludePastEmpTag: false,
                }),
              };
            });
            state.users = [...state.users, ..._data];
          }
        })
        .finally(() => (state.isLoading = false));
    };

    useInfiniteScroll(() => {
      if (state.users.length >= state.total) return;
      state.offset += EMPLOYEE_LIMIT;
      fetchData();
    });

    onBeforeMount(() => fetchData());

    const onClose = () => {
      context.emit('close');
    };

    return {
      onClose,
      fetchData,
      ...toRefs(state),
    };
  },
};
</script>

<style lang="scss" scoped src="./post-stats-modal.scss"></style>

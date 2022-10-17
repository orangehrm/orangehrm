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
  <div>
    <oxd-dialog class="orangehrm-buzz-post-mobile">
      <oxd-sheet
        :gutters="false"
        type="white"
        class="orangehrm-buzz-post-footer-mobile-list"
      >
        <div class="orangehrm-buzz-post-footer-mobile-list-header">
          <oxd-icon
            class="orangehrm-buzz-post-footer-mobile-list-header-icon"
            :name="iconName"
            :with-container="true"
          />
          <oxd-text v-if="statusName === 'shares'">
            {{ $t('buzz.n_share', {shareCount: total}) }}
          </oxd-text>
          <oxd-text v-if="statusName === 'likes'">
            {{ $t('buzz.n_like', {likesCount: total}) }}
          </oxd-text>
        </div>
        <oxd-divider />
        <div
          v-for="user in users"
          :key="user"
          class="orangehrm-buzz-post-footer-share-employee"
        >
          <div class="orangehrm-buzz-post-profile-image">
            <img
              alt="profile picture"
              class="employee-image"
              :src="`../pim/viewPhoto/empNumber/${user.employee.empNumber}`"
            />
          </div>
          <oxd-text tag="p">
            {{ user.employee.firstName }}{{ user.employee.lastName }}
          </oxd-text>
        </div>
        <oxd-loading-spinner v-if="isLoading" class="orangehrm-buzz-loader" />
      </oxd-sheet>
    </oxd-dialog>
  </div>
</template>
<script>
import {onBeforeMount, reactive, toRefs} from 'vue';
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import {APIService} from '@/core/util/services/api.service';
import Dialog from '@ohrm/oxd/core/components/Dialog/Dialog';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import useInfiniteScroll from '@/core/util/composable/useInfiniteScroll';

export default {
  name: 'PostStatusDialog',

  components: {
    'oxd-icon': Icon,
    'oxd-sheet': Sheet,
    'oxd-dialog': Dialog,
    'oxd-loading-spinner': Spinner,
  },

  props: {
    postId: {
      type: Number,
      required: true,
    },
    statusName: {
      type: String,
      required: true,
    },
    iconName: {
      type: String,
      required: true,
    },
  },

  setup(props) {
    const EMPLOYEE_LIMIT = 10;
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/buzz/posts/${props.postId}/${props.statusName}`,
    );

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
        .then(response => {
          const {data, meta} = response.data;
          state.total = meta?.count || 0;
          if (Array.isArray(data)) {
            state.users = [...state.users, ...data];
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

    return {
      fetchData,
      ...toRefs(state),
    };
  },
};
</script>
<style lang="scss" scoped src="./post-status-dialog.scss"></style>

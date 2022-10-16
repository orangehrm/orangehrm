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
  <oxd-sheet :gutters="false" type="white" class="orangehrm-buzz">
    <div class="orangehrm-buzz-post">
      <div class="orangehrm-buzz-post-header">
        <div class="orangehrm-buzz-post-header-details">
          <div class="orangehrm-buzz-post-profile-image">
            <img
              alt="profile picture"
              class="employee-image"
              :src="`../pim/viewPhoto/empNumber/${employeeId}`"
            />
          </div>
          <div class="orangehrm-buzz-post-header-text">
            <oxd-text tag="p" class="orangehrm-buzz-post-emp-name">
              {{ employeeFullName }}
            </oxd-text>
            <oxd-text tag="p" class="orangehrm-buzz-post-time">
              {{ postedTime }}
            </oxd-text>
          </div>
        </div>
        <div class="orangehrm-buzz-post-header-config">
          <oxd-dropdown icon="three-dots" :options="dropdownOptions" />
        </div>
      </div>
      <oxd-divider />
    </div>
    <div class="orangehrm-buzz-post-body">
      <oxd-text>{{ content }}</oxd-text>
      <slot name="body"></slot>
    </div>
    <div class="orangehrm-buzz-post-footer">
      <div class="orangehrm-buzz-post-footer-left">
        <slot name="actionButton"> </slot>
      </div>
      <slot name="postStatus"> </slot>
    </div>
    <slot name="comments"></slot>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </oxd-sheet>
</template>
<script>
import {computed} from 'vue';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import {APIService} from '@/core/util/services/api.service';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import Dropdown from '@ohrm/oxd/core/components/CardTable/Cell/Dropdown.vue';

export default {
  name: 'PostContainer',
  components: {
    'oxd-sheet': Sheet,
    'oxd-dropdown': Dropdown,
  },
  props: {
    postId: {
      type: Number,
      required: true,
    },
    content: {
      type: String,
      required: true,
    },
    employee: {
      type: Object,
      required: true,
    },
  },

  setup(props) {
    const {$tEmpName} = useEmployeeNameTranslate();
    // todo add date formatter
    const postedTime = '10-01-2022';
    const http = new APIService(window.appGlobal.baseUrl, '');

    const employeeFullName = computed(() => {
      return $tEmpName(props.employee, {
        includeMiddle: true,
        excludePastEmpTag: false,
      });
    });

    const employeeId = computed(() => {
      return props.employee.employeeId;
    });

    // change name
    const postIds = computed(() => {
      return props.postId;
    });

    return {
      http,
      postIds,
      postedTime,
      employeeId,
      employeeFullName,
    };
  },

  data() {
    return {
      isLoading: false,
      showComments: false,
      showDropdown: false,
      showLikeList: false,
      showSharesList: false,
      likedList: [],
      sharesList: [],
      dropdownOptions: [
        {label: this.$t('general.edit'), context: 'edit'},
        {label: this.$t('performance.delete'), context: 'delete'},
      ],
    };
  },

  methods: {
    onClickShowDropdown() {
      this.showDropdown = !this.showDropdown;
    },
  },
};
</script>
<style lang="scss" scoped src="./base-post.scss"></style>

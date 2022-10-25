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
          <profile-image :employee="employee"></profile-image>
          <div class="orangehrm-buzz-post-header-text">
            <oxd-text tag="p" class="orangehrm-buzz-post-emp-name">
              {{ employeeFullName }}
            </oxd-text>
            <oxd-text tag="p" class="orangehrm-buzz-post-time">
              {{ postDate }}
            </oxd-text>
          </div>
        </div>
        <div class="orangehrm-buzz-post-header-config">
          <oxd-dropdown>
            <oxd-icon-button name="three-dots" :with-container="true" />
            <template #content>
              <li>
                <div class="orangehrm-buzz-post-header-config-item">
                  <oxd-icon name="trash" />
                  <oxd-text tag="p" @click="$emit('delete', $event)">
                    {{ $t('buzz.delete_post') }}
                  </oxd-text>
                </div>
              </li>
              <li>
                <div class="orangehrm-buzz-post-header-config-item">
                  <oxd-icon name="pencil" />
                  <oxd-text tag="p" @click="$emit('edit', $event)">
                    {{ $t('buzz.edit_post') }}
                  </oxd-text>
                </div>
              </li>
            </template>
          </oxd-dropdown>
        </div>
      </div>
      <oxd-divider />
    </div>
    <div class="orangehrm-buzz-post-body">
      <slot name="content"> </slot>
      <slot name="body"></slot>
    </div>
    <div class="orangehrm-buzz-post-footer">
      <div class="orangehrm-buzz-post-footer-left">
        <slot name="actionButton"> </slot>
      </div>
      <slot name="postStats"> </slot>
    </div>
    <slot name="comments"></slot>
  </oxd-sheet>
</template>
<script>
import {computed} from 'vue';
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
import useLocale from '@/core/util/composable/useLocale';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import {APIService} from '@/core/util/services/api.service';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import ProfileImage from '@/orangehrmBuzzPlugin/components/ProfileImage';
import Dropdown from '@ohrm/oxd/core/components/DropdownMenu/DropdownMenu.vue';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'PostContainer',
  components: {
    'oxd-icon': Icon,
    'oxd-sheet': Sheet,
    'oxd-dropdown': Dropdown,
    'profile-image': ProfileImage,
  },
  props: {
    postId: {
      type: Number,
      required: true,
    },
    postedDate: {
      type: String,
      default: null,
    },
    employee: {
      type: Object,
      required: true,
    },
  },

  emits: ['edit', 'delete'],

  setup(props) {
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();

    const postDate = computed(() => {
      return formatDate(parseDate(props.postedDate), jsDateFormat, {locale});
    });
    const http = new APIService(window.appGlobal.baseUrl, '');

    const employeeFullName = computed(() => {
      return $tEmpName(props.employee, {
        includeMiddle: true,
        excludePastEmpTag: false,
      });
    });

    return {
      http,
      postDate,
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
    };
  },
};
</script>
<style lang="scss" scoped src="./post-container.scss"></style>

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
  <div class="orangehrm-comment">
    <div class="orangehrm-comment-profile">
      <div class="orangehrm-comment-profile-image-wrapper">
        <img
          alt="profile picture"
          class="orangehrm-comment-profile-image"
          :src="imgSrc"
        />
      </div>
    </div>
    <div class="orangehrm-comment-body">
      <div class="orangehrm-comment-profile-name">
        <oxd-text type="subtitle-2">
          {{ fullName }}
        </oxd-text>
      </div>
      <div class="orangehrm-comment-message">
        <oxd-text type="subtitle-2">
          {{ data.comment }}
        </oxd-text>
      </div>
      <div class="orangehrm-comment-timestamp">
        <oxd-text type="subtitle-2">
          {{ commentDate }} - {{ data.time }}
        </oxd-text>
      </div>
    </div>
  </div>
</template>

<script>
import {computed} from 'vue';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';
const defaultPic = `${window.appGlobal.publicPath}/images/default-photo.png`;

export default {
  name: 'LeaveComment',
  props: {
    data: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const imgSrc = computed(() => {
      const employee = props.data.createdByEmployee;
      return employee
        ? `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${employee.empNumber}`
        : defaultPic;
    });

    const fullName = computed(() => {
      const employee = props.data.createdByEmployee;
      return employee && `${employee.firstName} ${employee.lastName}`;
    });

    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const commentDate = computed(() => {
      return formatDate(parseDate(props.data?.date), jsDateFormat, {locale});
    });

    return {
      imgSrc,
      fullName,
      commentDate,
    };
  },
};
</script>

<style src="./leave-comment.scss" lang="scss" scoped></style>

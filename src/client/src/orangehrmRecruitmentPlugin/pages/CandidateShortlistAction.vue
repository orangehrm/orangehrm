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
  <recruitment-status :data="['first', 's', 's']">
    <template #header-title>
      <oxd-text tag="h6" class="orangehrm-main-title">
        Appplication Stage
      </oxd-text>
    </template>
    <template #footer-title>
      <oxd-text type="subtitle-2">
        Status: Application Initiated
      </oxd-text>
    </template>
    <template #footer-options>
      <oxd-button display-type="secondary" :label="$t('general.edit')" />
      <oxd-button display-type="ghost" :label="$t('general.edit')" />
      <oxd-button display-type="secondary" :label="$t('general.submit')" />
    </template>
  </recruitment-status>
</template>

<script>
import RecruitmentStatus from '@/orangehrmRecruitmentPlugin/components/RecruitmentStatus';
export default {
  name: 'CandidateShortlistAction',
  components: {'recruitment-status': RecruitmentStatus},
};
</script>

<style scoped lang="scss"></style>

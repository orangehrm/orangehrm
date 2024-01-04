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
  <base-widget
    icon="camera-fill"
    :empty="isEmpty"
    :loading="isLoading"
    :title="$t('dashboard.buzz_latest_posts')"
    :empty-text="$t('dashboard.no_posts_added')"
  >
    <oxd-grid class="orangehrm-buzz-widget" :cols="1">
      <oxd-grid-item
        v-for="post in posts"
        :key="post"
        class="orangehrm-buzz-widget-card"
      >
        <div class="orangehrm-buzz-widget-header" @click="onClickPost">
          <profile-image :employee="post.employee"></profile-image>
          <div class="orangehrm-buzz-widget-header-text">
            <oxd-text tag="p" class="orangehrm-buzz-widget-header-emp">
              {{ post.employeeFullName }}
            </oxd-text>
            <oxd-text tag="p" class="orangehrm-buzz-widget-header-time">
              {{ post.dateTime }}
            </oxd-text>
          </div>
        </div>
        <oxd-divider class="orangehrm-buzz-widget-divider" />
        <oxd-text v-if="post.text" tag="p" class="orangehrm-buzz-widget-body">
          {{ post.text }}
        </oxd-text>
        <img
          v-if="post.postImgSrc"
          :src="post.postImgSrc"
          class="orangehrm-buzz-widget-picture"
        />
        <video-frame v-if="post.postVideoSrc" :video-src="post.postVideoSrc">
        </video-frame>
      </oxd-grid-item>
    </oxd-grid>
  </base-widget>
</template>

<script>
import {ref, computed, onBeforeMount} from 'vue';
import {navigate} from '@/core/util/helper/navigation';
import useLocale from '@/core/util/composable/useLocale';
import {APIService} from '@/core/util/services/api.service';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import VideoFrame from '@/orangehrmBuzzPlugin/components/VideoFrame';
import useBuzzAPIs from '@/orangehrmBuzzPlugin/util/composable/useBuzzAPIs';
import ProfileImage from '@/orangehrmBuzzPlugin/components/ProfileImage.vue';
import BaseWidget from '@/orangehrmDashboardPlugin/components/BaseWidget.vue';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'BuzzLatestPostWidget',

  components: {
    'base-widget': BaseWidget,
    'video-frame': VideoFrame,
    'profile-image': ProfileImage,
  },

  setup() {
    const posts = ref([]);
    const isLoading = ref(false);
    const {locale} = useLocale();
    const {jsDateFormat, jsTimeFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();
    const {fetchPosts} = useBuzzAPIs(
      new APIService(window.appGlobal.baseUrl, ''),
    );

    const isEmpty = computed(() => posts.value.length === 0);

    const onClickPost = () => navigate('/buzz/viewBuzz');

    onBeforeMount(() => {
      isLoading.value = true;
      fetchPosts(5, 0, 'DESC', 'share.createdAtUtc')
        .then((response) => {
          const {data} = response.data;
          posts.value = data.map((post) => {
            const {employee, createdDate, createdTime, originalPost} = post;

            const postVideoSrc = post.video?.link || null;
            const utcDate = parseDate(
              `${createdDate} ${createdTime} +00:00`,
              'yyyy-MM-dd HH:mm xxx',
            );
            const dateTime = formatDate(
              utcDate,
              `${jsDateFormat} ${jsTimeFormat}`,
              {
                locale,
              },
            );
            const employeeFullName = $tEmpName(employee, {
              includeMiddle: true,
              excludePastEmpTag: false,
            });
            const postImgSrc = Array.isArray(post?.photoIds)
              ? `${window.appGlobal.baseUrl}/buzz/photo/${post.photoIds[0]}`
              : null;
            const text = originalPost ? originalPost.text : post.text;

            return {
              ...post,
              text,
              dateTime,
              postImgSrc,
              postVideoSrc,
              employeeFullName,
            };
          });
        })
        .finally(() => {
          isLoading.value = false;
        });
    });

    return {
      posts,
      isEmpty,
      isLoading,
      onClickPost,
    };
  },
};
</script>

<style src="./buzz-latest-post-widget.scss" lang="scss" scoped></style>

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
  <div class="orangehrm-buzz-post-body">
    <oxd-text v-if="post.text" tag="p" :class="postClasses">
      {{ post.text }}
    </oxd-text>
    <oxd-text
      v-show="!readMore"
      tag="p"
      class="orangehrm-buzz-post-body-readmore"
      @click="onClickReadMore"
    >
      {{ $t('buzz.read_more') }}
    </oxd-text>
    <br v-if="post.text && (post.type === 'video' || post.type === 'photo')" />
    <video-frame v-if="post.type === 'video'" :video-src="post.video.link">
    </video-frame>
    <photo-frame v-if="post.type === 'photo'" :media="post.photoIds">
      <template #content="{index}">
        <div
          v-if="index === 3 && post.photoIds.length === 5"
          class="orangehrm-buzz-post-body-picture --more"
          @click="onClickPicture(index)"
        >
          <oxd-text tag="p" class="orangehrm-buzz-post-body-more">+1</oxd-text>
          <oxd-icon class="orangehrm-buzz-post-body-more" name="images" />
        </div>
        <div
          v-else
          class="orangehrm-buzz-post-body-picture"
          @click="onClickPicture(index)"
        ></div>
      </template>
    </photo-frame>
    <template v-if="originalPost">
      <br v-if="post.text || post.type === 'video' || post.type === 'photo'" />
      <oxd-text tag="p" class="orangehrm-buzz-post-body-employee">
        {{ originalPost.employee }}
      </oxd-text>
      <oxd-text tag="p" class="orangehrm-buzz-post-body-date">
        {{ originalPost.dateTime }}
      </oxd-text>
      <oxd-text
        v-if="originalPost.text"
        tag="p"
        class="orangehrm-buzz-post-body-original-text"
      >
        {{ originalPost.text }}
      </oxd-text>
    </template>
  </div>
</template>

<script>
import {computed, reactive, toRefs} from 'vue';
import useLocale from '@/core/util/composable/useLocale';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import PhotoFrame from '@/orangehrmBuzzPlugin/components/PhotoFrame';
import VideoFrame from '@/orangehrmBuzzPlugin/components/VideoFrame';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import {OxdIcon} from '@ohrm/oxd';

export default {
  name: 'PostBody',

  components: {
    'oxd-icon': OxdIcon,
    'photo-frame': PhotoFrame,
    'video-frame': VideoFrame,
  },

  props: {
    post: {
      type: Object,
      required: true,
    },
  },

  emits: ['close', 'selectPhoto'],

  setup(props, context) {
    const {locale} = useLocale();
    const {jsDateFormat, jsTimeFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();
    const state = reactive({
      readMore: new String(props.post?.text).length < 500,
    });

    const postClasses = computed(() => ({
      'orangehrm-buzz-post-body-text': true,
      '--truncate': state.readMore === false,
    }));

    const onClickReadMore = () => {
      state.readMore = !state.readMore;
    };

    const onClickPicture = (index) => {
      context.emit('selectPhoto', index);
    };

    const originalPost = computed(() => {
      if (props.post.originalPost === null) return null;
      const {createdDate, createdTime} = props.post.originalPost;

      const utcDate = parseDate(
        `${createdDate} ${createdTime} +00:00`,
        'yyyy-MM-dd HH:mm xxx',
      );

      return {
        text: props.post.originalPost.text,
        employee: $tEmpName(props.post.originalPost.employee, {
          includeMiddle: true,
          excludePastEmpTag: false,
        }),
        dateTime: formatDate(utcDate, `${jsDateFormat} ${jsTimeFormat}`, {
          locale,
        }),
      };
    });

    return {
      postClasses,
      originalPost,
      onClickPicture,
      onClickReadMore,
      ...toRefs(state),
    };
  },
};
</script>

<style src="./post-body.scss" lang="scss" scoped></style>

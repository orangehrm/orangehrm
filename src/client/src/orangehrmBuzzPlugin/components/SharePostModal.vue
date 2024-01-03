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
  <post-modal
    :loading="isLoading"
    :title="$t('buzz.share_post')"
    @submit="onSubmit"
    @close="$emit('close', false)"
  >
    <template #header>
      <oxd-buzz-post-input
        v-model="post.text"
        :rules="rules.text"
        :placeholder="$t('buzz.post_placeholder')"
      >
      </oxd-buzz-post-input>
    </template>
    <video-frame v-if="data.type === 'video'" :video-src="data.video.link">
    </video-frame>
    <photo-frame v-if="data.type === 'photo'" :media="data.photoIds">
    </photo-frame>
    <br v-if="data.type === 'video' || data.type === 'photo'" />

    <oxd-text tag="p" class="orangehrm-buzz-share-employee">
      {{ originalPost.employee }}
    </oxd-text>
    <oxd-text tag="p" class="orangehrm-buzz-share-date">
      {{ originalPost.dateTime }}
    </oxd-text>
    <oxd-text
      v-if="originalPost.text"
      tag="p"
      class="orangehrm-buzz-share-text"
    >
      {{ originalPost.text }}
    </oxd-text>
  </post-modal>
</template>

<script>
import {computed, reactive, toRefs} from 'vue';
import useToast from '@/core/util/composable/useToast';
import useLocale from '@/core/util/composable/useLocale';
import {APIService} from '@/core/util/services/api.service';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import PostModal from '@/orangehrmBuzzPlugin/components/PostModal';
import PhotoFrame from '@/orangehrmBuzzPlugin/components/PhotoFrame';
import VideoFrame from '@/orangehrmBuzzPlugin/components/VideoFrame';
import {shouldNotExceedCharLength} from '@/core/util/validation/rules';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import {OxdBuzzPostInput} from '@ohrm/oxd';

export default {
  name: 'SharePostModal',

  components: {
    'post-modal': PostModal,
    'photo-frame': PhotoFrame,
    'video-frame': VideoFrame,
    'oxd-buzz-post-input': OxdBuzzPostInput,
  },

  props: {
    data: {
      type: Object,
      required: true,
    },
  },

  emits: ['close'],

  setup(props, context) {
    const {locale} = useLocale();
    const {saveSuccess} = useToast();
    const {jsDateFormat, jsTimeFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();
    const rules = {
      text: [shouldNotExceedCharLength(65530)],
    };
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/buzz/shares',
    );

    const state = reactive({
      post: {
        text: null,
      },
      isLoading: false,
    });

    const onSubmit = () => {
      state.isLoading = true;
      http
        .create({
          text: state.post.text,
          shareId: props.data.id,
        })
        .then(() => {
          saveSuccess();
          context.emit('close', true);
        });
    };

    const originalPost = computed(() => {
      const originalText = props.data.originalPost?.text || props.data.text;
      const originalEmployee =
        props.data.originalPost?.employee || props.data.employee;
      const {createdDate, createdTime} = props.data.originalPost || props.data;
      const utcDate = parseDate(
        `${createdDate} ${createdTime} +00:00`,
        'yyyy-MM-dd HH:mm xxx',
      );

      return {
        text: originalText,
        employee: $tEmpName(originalEmployee, {
          includeMiddle: true,
          excludePastEmpTag: false,
        }),
        dateTime: formatDate(utcDate, `${jsDateFormat} ${jsTimeFormat}`, {
          locale,
        }),
      };
    });

    return {
      rules,
      onSubmit,
      originalPost,
      ...toRefs(state),
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-share {
  &-employee {
    font-size: 0.9rem;
  }
  &-date {
    font-size: 0.6rem;
    color: $oxd-interface-gray-color;
  }
  &-text {
    font-weight: 300;
    margin-top: 0.5rem;
    @include truncate(6, 1.5, #fff);
  }
}
</style>

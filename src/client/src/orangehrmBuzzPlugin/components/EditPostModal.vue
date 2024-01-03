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
    :title="$t('buzz.edit_post')"
    :action-label="$t('buzz.post')"
    @submit="onSubmit"
    @close="$emit('close', false)"
  >
    <template #header>
      <oxd-buzz-post-input v-model="post.text" :rules="rules.text">
      </oxd-buzz-post-input>
    </template>

    <template v-if="data.originalPost">
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
    </template>

    <template v-else>
      <photo-input
        v-if="post.type === 'text' || post.type === 'photo'"
        v-model="post.photos"
      />
      <oxd-input-field
        v-if="post.type === 'video'"
        v-model="post.video"
        type="textarea"
        :rules="rules.url"
        :label="$t('buzz.video_url')"
      />
      <video-frame v-if="embedURL" :video-src="embedURL"> </video-frame>
    </template>
  </post-modal>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {computed, reactive, toRefs} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
import useToast from '@/core/util/composable/useToast';
import useLocale from '@/core/util/composable/useLocale';
import {APIService} from '@/core/util/services/api.service';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import PostModal from '@/orangehrmBuzzPlugin/components/PostModal';
import PhotoFrame from '@/orangehrmBuzzPlugin/components/PhotoFrame';
import VideoFrame from '@/orangehrmBuzzPlugin/components/VideoFrame';
import PhotoInput from '@/orangehrmBuzzPlugin/components/PhotoInput';
import useBuzzAPIs from '@/orangehrmBuzzPlugin/util/composable/useBuzzAPIs';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import {OxdBuzzPostInput, promiseDebounce} from '@ohrm/oxd';

export default {
  name: 'EditPostModal',

  components: {
    'post-modal': PostModal,
    'photo-frame': PhotoFrame,
    'photo-input': PhotoInput,
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
    const {$t} = usei18n();
    const {locale} = useLocale();
    const {jsDateFormat, jsTimeFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();
    const http = new APIService(window.appGlobal.baseUrl, '');
    const {updateSuccess} = useToast();
    const {updatePost, updateSharedPost} = useBuzzAPIs(http);

    const state = reactive({
      post: {
        text: props.data.text,
        type: props.data.type,
        photos: props.data.photoIds || [],
        video: props.data.video?.link || null,
      },
      isLoading: false,
      embedURL: props.data.video?.link || null,
    });

    const onSubmit = () => {
      let type = 'text';
      state.isLoading = true;

      if (state.post.photos.length > 0) {
        type = 'photo';
      }
      if (state.post.video) {
        type = 'video';
      }

      new Promise((resolve) => {
        if (props.data.originalPost) {
          resolve(updateSharedPost(props.data.id, state.post.text));
        } else {
          resolve(
            updatePost(props.data.post.id, {
              type: type,
              text: state.post.text,
              link: state.post.video,
              photos: state.post.photos.filter((id) => typeof id === 'object'),
              deletedPhotos: (props.data.photoIds || []).filter((id) => {
                return (
                  state.post.photos.findIndex((photo) => photo === id) === -1
                );
              }),
            }),
          );
        }
      }).then((response) => {
        updateSuccess();
        context.emit('close', response.data);
      });
    };

    const rules = {
      url: [
        required,
        promiseDebounce(async (value) => {
          if (!value) return true;
          state.embedURL = null;
          const response = await http.request({
            method: 'GET',
            url: '/api/v2/buzz/validation/links',
            params: {
              url: value,
            },
          });
          const {data} = response.data;
          if (data?.valid === true) {
            state.embedURL = data.embeddedURL;
            return true;
          } else {
            return $t('general.invalid_video_url_message');
          }
        }, 500),
      ],
      text: [
        shouldNotExceedCharLength(65530),
        (value) => {
          if (props.data.type === 'video' || state.post.photos.length > 0) {
            return true;
          }
          return required(value);
        },
      ],
    };

    const originalPost = computed(() => {
      const originalText = props.data.originalPost?.text;
      const originalEmployee = props.data.originalPost?.employee;
      const {createdDate, createdTime} = props.data.originalPost;
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

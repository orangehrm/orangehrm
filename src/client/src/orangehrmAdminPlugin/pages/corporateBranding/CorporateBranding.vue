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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('general.corporate_branding') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form ref="formRef" :loading="isLoading" @submit-valid="onFormSubmit">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <inline-color-input
                v-model="colors.primaryColor"
                :rules="rules.color"
                :label="$t('admin.primary_color')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-column-3">
              <inline-color-input
                v-model="colors.secondaryColor"
                :rules="rules.color"
                :label="$t('admin.secondary_color')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <inline-color-input
                v-model="colors.primaryFontColor"
                :rules="rules.color"
                :label="$t('admin.primary_font_color')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2 --offset-column-3">
              <inline-color-input
                v-model="colors.secondaryFontColor"
                :rules="rules.color"
                :label="$t('admin.secondary_font_color')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-3">
              <inline-color-input
                v-model="colors.primaryGradientStartColor"
                :rules="rules.color"
                :label="$t('admin.primary_gradient_color_one')"
                type="color"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-3 --offset-column-3">
              <inline-color-input
                v-model="colors.primaryGradientEndColor"
                :rules="rules.color"
                :label="$t('admin.primary_gradient_color_two')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <br />
        <oxd-divider />
        <br />
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <file-upload-input
                v-model:newFile="clientLogo.newAttachment"
                v-model:method="clientLogo.method"
                :label="$t('admin.client_logo')"
                :button-label="$t('general.browse')"
                :file="clientLogo.oldAttachment"
                :rules="rules.clientLogo"
                :hint="
                  $t('general.accept_jpg_png_gif_upto_recommended_dimensions', {
                    fileSize: formattedFileSize,
                    width: 50,
                    height: 50,
                  })
                "
                button-icon=""
                url="admin/theme/attachments/image"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <file-upload-input
                v-model:newFile="clientBanner.newAttachment"
                v-model:method="clientBanner.method"
                :label="$t('admin.client_banner')"
                :button-label="$t('general.browse')"
                :file="clientBanner.oldAttachment"
                :rules="rules.clientBanner"
                :hint="
                  $t('general.accept_jpg_png_gif_upto_recommended_dimensions', {
                    fileSize: formattedFileSize,
                    width: 182,
                    height: 50,
                  })
                "
                button-icon=""
                url="admin/theme/attachments/image"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <file-upload-input
                v-model:newFile="loginBanner.newAttachment"
                v-model:method="loginBanner.method"
                :label="$t('admin.login_banner')"
                :button-label="$t('general.browse')"
                :file="loginBanner.oldAttachment"
                :rules="rules.loginBanner"
                :hint="
                  $t('general.accept_jpg_png_gif_upto_recommended_dimensions', {
                    fileSize: formattedFileSize,
                    width: 340,
                    height: 65,
                  })
                "
                button-icon=""
                url="admin/theme/attachments/image"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <div class="orangehrm-sm-field">
                <oxd-text tag="p" class="orangehrm-sm-field-label">
                  {{ $t('admin.social_media_images') }}
                </oxd-text>
                <oxd-switch-input v-model="showSocialMediaImages" />
              </div>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <div class="orangehrm-actions-group">
            <oxd-button
              type="button"
              display-type="ghost"
              :label="$t('general.reset_to_default')"
              @click="onClickReset"
            />
            <oxd-button
              type="button"
              display-type="ghost"
              :label="$t('general.preview')"
              @click="onClickPreview"
            />
            <oxd-button
              type="submit"
              display-type="secondary"
              :label="$t('general.publish')"
            />
          </div>
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {onBeforeMount, reactive, toRefs} from 'vue';
import {
  required,
  maxFileSize,
  validHexFormat,
  validFileTypes,
  imageShouldHaveDimensions,
} from '@ohrm/core/util/validation/rules';
import useForm from '@/core/util/composable/useForm';
import useToast from '@/core/util/composable/useToast';
import {APIService} from '@/core/util/services/api.service';
import {reloadPage} from '@ohrm/core/util/helper/navigation';
import FileUploadInput from '@/core/components/inputs/FileUploadInput';
import InlineColorInput from '@/orangehrmAdminPlugin/components/InlineColorInput';
import {OxdSwitchInput} from '@ohrm/oxd';

const colorModel = {
  primaryColor: null,
  primaryFontColor: null,
  secondaryColor: null,
  secondaryFontColor: null,
  primaryGradientStartColor: null,
  primaryGradientEndColor: null,
};

const fileUploadModel = {
  oldAttachment: null,
  newAttachment: null,
  method: 'keepCurrent',
};

export default {
  components: {
    'oxd-switch-input': OxdSwitchInput,
    'file-upload-input': FileUploadInput,
    'inline-color-input': InlineColorInput,
  },
  props: {
    allowedImageTypes: {
      type: Array,
      required: true,
    },
    aspectRatios: {
      type: Object,
      required: true,
    },
    aspectRatioTolerance: {
      type: Number,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/admin/theme`,
    );

    const {saveSuccess} = useToast();
    const {formRef, invalid, validate} = useForm();

    const state = reactive({
      isLoading: false,
      colors: {
        ...colorModel,
      },
      clientLogo: {
        ...fileUploadModel,
      },
      clientBanner: {
        ...fileUploadModel,
      },
      loginBanner: {
        ...fileUploadModel,
      },
      showSocialMediaImages: true,
    });

    const rules = {
      color: [required, validHexFormat],
      clientLogo: [
        (v) =>
          state.clientLogo.method === 'replaceCurrent' ? required(v) : true,
        maxFileSize(props.maxFileSize),
        imageShouldHaveDimensions(
          props.aspectRatios.clientLogo,
          props.aspectRatioTolerance,
        ),
        validFileTypes(props.allowedImageTypes),
      ],
      clientBanner: [
        (v) =>
          state.clientBanner.method === 'replaceCurrent' ? required(v) : true,
        maxFileSize(props.maxFileSize),
        imageShouldHaveDimensions(
          props.aspectRatios.clientBanner,
          props.aspectRatioTolerance,
        ),
        validFileTypes(props.allowedImageTypes),
      ],
      loginBanner: [
        (v) =>
          state.loginBanner.method === 'replaceCurrent' ? required(v) : true,
        maxFileSize(props.maxFileSize),
        imageShouldHaveDimensions(
          props.aspectRatios.loginBanner,
          props.aspectRatioTolerance,
        ),
        validFileTypes(props.allowedImageTypes),
      ],
    };

    const onFormSubmit = () => {
      const getAttachment = (fileUploadModel) => {
        if (
          fileUploadModel.method === null ||
          fileUploadModel.method === 'replaceCurrent'
        ) {
          return fileUploadModel.newAttachment;
        }
        return undefined;
      };
      state.isLoading = true;
      http
        .request({
          method: 'PUT',
          url: '/api/v2/admin/theme',
          data: {
            variables: state.colors,
            showSocialMediaImages: state.showSocialMediaImages,
            currentClientLogo: state.clientLogo.method,
            clientLogo: getAttachment(state.clientLogo),
            currentClientBanner: state.clientBanner.method,
            clientBanner: getAttachment(state.clientBanner),
            currentLoginBanner: state.loginBanner.method,
            loginBanner: getAttachment(state.loginBanner),
          },
        })
        .then(() => {
          return saveSuccess();
        })
        .then(() => reloadPage());
    };

    const onClickReset = () => {
      state.isLoading = true;
      http
        .request({
          method: 'DELETE',
          url: '/api/v2/admin/theme',
        })
        .then(() => reloadPage());
    };

    const onClickPreview = () => {
      validate().then(() => {
        if (invalid.value === true) return;
        state.isLoading = true;
        http
          .request({
            method: 'POST',
            url: '/api/v2/admin/theme/preview',
            data: {
              ...state.colors,
            },
          })
          .then((response) => {
            const {data} = response.data;
            for (const key in data) {
              const value = data[key];
              document.documentElement.style.setProperty(key, value);
            }
          })
          .finally(() => (state.isLoading = false));
      });
    };

    onBeforeMount(() => {
      state.isLoading = true;
      http
        .getAll()
        .then((response) => {
          const {data} = response.data;
          const {
            clientLogo,
            clientBanner,
            loginBanner,
            showSocialMediaImages,
            variables,
          } = data;
          state.colors = variables;
          if (clientLogo === null) {
            state.clientLogo.method = null;
          } else {
            state.clientLogo.oldAttachment = clientLogo;
            state.clientLogo.oldAttachment.id = 'clientLogo';
          }
          if (clientBanner === null) {
            state.clientBanner.method = null;
          } else {
            state.clientBanner.oldAttachment = clientBanner;
            state.clientBanner.oldAttachment.id = 'clientBanner';
          }
          if (loginBanner === null) {
            state.loginBanner.method = null;
          } else {
            state.loginBanner.oldAttachment = loginBanner;
            state.loginBanner.oldAttachment.id = 'loginBanner';
          }
          state.showSocialMediaImages = showSocialMediaImages;
        })
        .finally(() => (state.isLoading = false));
    });

    return {
      rules,
      formRef,
      onFormSubmit,
      onClickReset,
      onClickPreview,
      ...toRefs(state),
    };
  },
  computed: {
    formattedFileSize() {
      let size = Math.round((this.maxFileSize / (1024 * 1024)) * 100) / 100;
      return size === 1 ? size + 'MB' : size + 'MBs';
    },
  },
};
</script>

<style src="./corporate-branding.scss" lang="scss" scoped></style>

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
  <div class="orangehrm-paper-container">
    <div class="orangehrm-vacancy-card-header">
      <oxd-text type="card-title">
        {{ vacancyTitle }}
      </oxd-text>
      <oxd-button
        :label="$t('general.apply')"
        class="oxd-button--success"
      ></oxd-button>
    </div>
    <oxd-divider v-show="vacancyDescription"></oxd-divider>
    <div :class="{'orangehrm-vacancy-card-body': isViewDetails}">
      <oxd-text type="toast-message"
        ><pre
          v-if="vacancyDescription"
          class="orangehrm-vacancy-card-pre-tag"
          >{{ vacancyDescription }}</pre
        ></oxd-text
      >
    </div>
    <div
      v-if="vacancyDescription && vacancyDescription.length > descriptionLength"
      class="orangehrm-vacancy-card-footer"
    >
      <a @click="viewDetails">
        <oxd-text
          class="orangehrm-vacancy-card-anchor-tag"
          type="toast-message"
        >
          {{
            isViewDetails
              ? $t('recruitment.show_more')
              : $t('recruitment.show_less')
          }}
        </oxd-text>
      </a>
    </div>
  </div>
</template>

<script>
import OxdDivider from '@ohrm/oxd/core/components/Divider/Divider';
import useResponsive from '@ohrm/oxd/composables/useResponsive';
import {toRefs} from 'vue';

export default {
  name: 'VacancyCard',
  components: {
    'oxd-divider': OxdDivider,
  },
  props: {
    vacancyId: {
      type: Number,
      required: true,
    },
    vacancyTitle: {
      type: String,
      required: true,
    },
    vacancyDescription: {
      type: String,
      required: true,
    },
  },
  setup() {
    const responsiveState = useResponsive();
    return {
      ...toRefs(responsiveState),
    };
  },
  data() {
    return {
      viewMore: true,
      descriptionLength: 0,
    };
  },
  computed: {
    isViewDetails() {
      return this.viewMore;
    },
    isMobile() {
      return this.windowWidth < 600;
    },
  },
  watch: {
    isMobile: function() {
      this.validDescriptionLength();
    },
  },
  beforeMount() {
    this.onResize();
    this.validDescriptionLength();
    window.addEventListener('resize', this.onResize);
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.onResize);
  },
  methods: {
    viewDetails() {
      return (this.viewMore = !this.viewMore);
    },
    validDescriptionLength() {
      return this.isMobile
        ? (this.descriptionLength = 150)
        : this.windowWidth < 1920
        ? (this.descriptionLength = 300)
        : (this.descriptionLength = 400);
    },
    onResize() {
      this.windowWidth = window.innerWidth;
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-paper-container {
  width: 80%;
  margin: 1rem auto;
  padding: 0.75rem 1.5rem;
}

.orangehrm-vacancy-card {
  display: block;

  &-header {
    display: flex;
    justify-content: space-between;
  }

  &-anchor-tag {
    color: $oxd-primary-one-color;
    padding-top: 0.5rem;
  }

  &-anchor-tag:hover {
    cursor: pointer;
  }

  &-body {
    text-align: left;
    justify-content: space-between;
    word-break: break-all;
    max-height: 60px;
    -webkit-line-clamp: 4;
    @include oxd-respond-to('md') {
      max-height: 30px;
      -webkit-line-clamp: 2;
    }
    text-overflow: ellipsis;
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
  }
  &-pre-tag {
    margin-top: 0;
    font-family: $oxd-font-family;
    white-space: pre-wrap;
    white-space: -moz-pre-wrap;
    white-space: -o-pre-wrap;
    word-wrap: break-word;
  }
  &-footer {
    display: flex;
    justify-content: left;
  }
}
</style>

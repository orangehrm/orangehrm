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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <div class="orangehrm-employee-tracker">
        <div class="orangehrm-employee-tracker-image-section">
          <div class="orangehrm-employee-tracker-image-wrapper">
            <div class="orangehrm-employee-tracker-image">
              <img
                alt="profile picture"
                class="employee-image"
                :src="employeeImgSrc"
              />
            </div>
          </div>
        </div>
        <div class="orangehrm-employee-tracker-header-section">
          <div class="orangehrm-employee-tracker-header">
            <oxd-text tag="h5" class="orangehrm-employee-tracker-header-title">
              {{ trackerName }}
            </oxd-text>
            <oxd-text
              tag="h6"
              class="orangehrm-employee-tracker-header-subtitle"
            >
              {{ employeeName }}
            </oxd-text>
          </div>
          <div class="orangehrm-employee-tracker-ratings">
            <div
              v-if="meta.positive > 0"
              class="orangehrm-employee-tracker-ratings-info"
            >
              <div class="orangehrm-employee-tracker-ratings-icon --positive">
                <oxd-icon name="hand-thumbs-up-fill" />
              </div>
              <oxd-text
                class="orangehrm-employee-tracker-ratings-text --positive"
              >
                {{ meta.positive }}
              </oxd-text>
            </div>
            <div
              v-if="meta.negative > 0"
              class="orangehrm-employee-tracker-ratings-info"
            >
              <div class="orangehrm-employee-tracker-ratings-icon --negative">
                <oxd-icon name="hand-thumbs-down-fill" />
              </div>
              <oxd-text
                class="orangehrm-employee-tracker-ratings-text --negative"
              >
                {{ meta.negative }}
              </oxd-text>
            </div>
          </div>
        </div>
      </div>
    </div>
    <br />
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h5" class="orangehrm-employee-tracker-log-list-header">
          {{ $t('performance.tracker_logs') }}
        </oxd-text>
        <oxd-button
          :label="$t('performance.add_log')"
          icon-name="plus"
          display-type="secondary"
          @click="onClickAdd"
        />
      </div>
      <oxd-grid ref="scrollerRef" :cols="1" class="orangehrm-container">
        <oxd-sheet
          v-for="(item, index) in items"
          :key="index"
          :gutters="false"
          type="gray-lighten-2"
          class="orangehrm-scroll-card"
        >
          <div class="orangehrm-employee-tracker-log">
            <div class="orangehrm-employee-tracker-log-image-section">
              <div class="orangehrm-employee-tracker-log-image-wrapper">
                <div class="orangehrm-employee-tracker-log-image">
                  <img
                    alt="profile picture"
                    class="employee-image"
                    :src="item.reviewerPictureSrc"
                  />
                </div>
              </div>
            </div>
            <div class="orangehrm-employee-tracker-log-content-section">
              <div class="orangehrm-employee-tracker-log-content-container">
                <div class="orangehrm-employee-tracker-log-header">
                  <div class="orangehrm-employee-tracker-log-title">
                    <oxd-text
                      tag="h6"
                      class="orangehrm-employee-tracker-log-title-text"
                    >
                      {{ item.log }}
                    </oxd-text>
                    <div
                      :class="{
                        'orangehrm-employee-tracker-log-title-icon': true,
                        '--positive': item.achievement === 1,
                        '--negative': item.achievement === 2,
                      }"
                    >
                      <oxd-icon
                        :name="
                          `hand-thumbs-${
                            item.achievement === 1 ? 'up' : 'down'
                          }-fill`
                        "
                      />
                    </div>
                  </div>
                  <oxd-dropdown
                    :options="dropdownOptions"
                    @click="onTrackerDropdownAction($event, item)"
                  />
                </div>
                <div class="orangehrm-employee-tracker-log-body">
                  <oxd-text
                    tag="p"
                    class="orangehrm-employee-tracker-log-body-text"
                  >
                    {{ item.comment }}
                  </oxd-text>
                </div>
              </div>
              <div class="orangehrm-employee-tracker-log-reviewer">
                <oxd-text class="orangehrm-employee-tracker-log-reviewer-name">
                  {{ item.reviewer.firstName + ' ' + item.reviewer.lastName }}
                </oxd-text>
                <oxd-text>
                  {{ $t('performance.added_on') + ': ' + item.addedDate }}
                </oxd-text>
                <oxd-text v-if="item.modifiedDate">
                  {{ $t('performance.modified_on') + ': ' + item.modifiedDate }}
                </oxd-text>
              </div>
            </div>
          </div>
        </oxd-sheet>
        <oxd-loading-spinner
          v-if="isLoading"
          class="orangehrm-container-loader"
        />
      </oxd-grid>
    </div>
    <add-tracker-log-modal
      v-if="showAddTrackerModal"
      :tracker-id="trackerId"
      @close="onAddTrackerModalClose"
    ></add-tracker-log-modal>
    <edit-tracker-log-modal
      v-if="showEditTrackerModal"
      :tracker-id="trackerId"
      :tracker-log-id="editTrackerLogId"
      @close="onEditTrackerModalClose"
    ></edit-tracker-log-modal>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {reactive, toRefs} from 'vue';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useLocale from '@/core/util/composable/useLocale';
import useInfiniteScroll from '@/core/util/composable/useInfiniteScroll';
import Icon from '@ohrm/oxd/core/components/Icon/Icon.vue';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import Dropdown from '@ohrm/oxd/core/components/CardTable/Cell/Dropdown.vue';
import AddTrackerLogModal from '@/orangehrmPerformancePlugin/components/AddTrackerLogModal';
import EditTrackerLogModal from '@/orangehrmPerformancePlugin/components/EditTrackerLogModal';

export default {
  name: 'ViewEmployeeTrackerLogs',
  components: {
    'oxd-icon': Icon,
    'oxd-sheet': Sheet,
    'oxd-loading-spinner': Spinner,
    'oxd-dropdown': Dropdown,
    'add-tracker-log-modal': AddTrackerLogModal,
    'edit-tracker-log-modal': EditTrackerLogModal,
  },
  props: {
    trackerId: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    // TODO change to window.appGlobal.baseUrl
    const http = new APIService(
      'https://942be86c-56c6-42e3-ac85-874a20c7ce9b.mock.pstmn.io',
      `api/v2/performance/trackers/${props.trackerId}/logs`,
    );
    const limit = 10;
    const state = reactive({
      total: 0,
      items: [],
      meta: {
        positive: 0,
        negative: 0,
      },
      infinite: false,
      isLoading: false,
    });

    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const fetchData = () => {
      state.isLoading = true;
      http
        .getAll({
          limit: limit,
          offset: state.items.length === 0 ? 0 : limit,
        })
        .then(response => {
          const {data, meta} = response.data;
          state.total = meta?.total || 0;
          if (Array.isArray(data)) {
            state.items = [
              ...state.items,
              ...data.map(item => {
                return {
                  ...item,
                  reviewerPictureSrc: `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${item.reviewer.empNumber}`,
                  addedDate: formatDate(
                    parseDate(item.addedDate),
                    jsDateFormat,
                    {locale},
                  ),
                  modifiedDate: formatDate(
                    parseDate(item.modifiedDate),
                    jsDateFormat,
                    {locale},
                  ),
                };
              }),
            ];
            state.meta = {...state.meta, ...meta};
          }
        })
        .finally(() => (state.isLoading = false));
    };
    const {scrollerRef} = useInfiniteScroll(() => {
      if (state.items.length >= state.total) return;
      fetchData();
    });
    fetchData();
    return {
      http,
      scrollerRef,
      fetchData,
      ...toRefs(state),
    };
  },
  data() {
    return {
      trackerName: '',
      employeeName: '',
      employeeImgSrc: '',
      showAddTrackerModal: false,
      showEditTrackerModal: false,
      editTrackerLogId: null,
      dropdownOptions: [
        {label: this.$t('general.edit'), context: 'edit'},
        {label: 'Delete', context: 'delete'},
      ],
    };
  },
  beforeMount() {
    this.http
      .request({
        method: 'GET',
        url: `api/v2/performance/employees/trackers/${this.trackerId}`,
      })
      .then(response => {
        const {data} = response.data;
        this.trackerName = data.title;
        this.employeeName =
          data.employee.firstName + ' ' + data.employee.lastName;
        this.employeeImgSrc = `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${data.employee.empNumber}`;
      });
  },
  methods: {
    onClickAdd() {
      this.showAddTrackerModal = true;
    },
    onAddTrackerModalClose() {
      this.showAddTrackerModal = false;
      this.resetItems();
    },
    onEditTrackerModalClose() {
      this.showEditTrackerModal = false;
      this.resetItems();
    },
    onTrackerDropdownAction(event, item) {
      switch (event.context) {
        case 'edit':
          this.showEditTrackerModal = true;
          this.editTrackerLogId = item.id;
          break;
        case 'delete':
          // TODO implement delete
          break;
      }
    },
    resetItems() {
      this.items = [];
      this.fetchData();
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-container {
  overflow: auto;
  max-height: 400px;
  @include oxd-scrollbar();
  &-loader {
    margin: 0 auto;
    background-color: $oxd-white-color;
  }
}
.orangehrm-scroll-card {
  padding: 0.5rem 1rem;
  margin-bottom: 0.5rem;
  &-header {
    display: flex;
    justify-content: space-between;
  }
}

.orangehrm-employee-tracker {
  display: flex;
  overflow: hidden;
  @include oxd-respond-to('xs') {
    flex-direction: column;
  }
  @include oxd-respond-to('sm') {
    flex-direction: row;
  }

  &-image-wrapper {
    padding: 0.6rem 1.2rem;
  }

  &-image {
    width: 100px;
    height: 100px;
    border-radius: 100%;
    display: flex;
    overflow: hidden;
    justify-content: center;
    box-sizing: border-box;
    border: 0.5rem solid $oxd-background-pastel-white-color;
  }

  &-image-section {
    display: flex;
    align-items: center;
    @include oxd-respond-to('xs') {
      flex-direction: row-reverse;
      justify-content: center;
    }
    @include oxd-respond-to('md') {
      flex-direction: column;
      justify-content: center;
    }
  }

  &-header-section {
    display: flex;
    @include oxd-respond-to('xs') {
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
    @include oxd-respond-to('sm') {
      flex-direction: row;
      align-items: flex-start;
      text-align: start;
    }
  }

  &-header {
    display: flex;
    flex-direction: column;
    padding-left: 1.2rem;
    padding-right: 0.6rem;
    padding-top: 1.2rem;

    &-title {
      font-weight: 700;
    }

    &-subtitle {
      font-weight: 700;
      color: $oxd-interface-gray-color;
    }
  }

  &-ratings {
    display: flex;
    padding-top: 1.2rem;

    &-info {
      display: flex;
      flex-direction: row;
      padding-left: 0.6rem;
      padding-right: 0.6rem;
      text-align: center;
    }

    &-icon {
      font-size: 25px;
      padding-right: 0.6rem;

      &.--positive {
        ::v-deep(.oxd-icon) {
          color: $oxd-secondary-four-color;
        }
      }

      &.--negative {
        ::v-deep(.oxd-icon) {
          color: $oxd-feedback-danger-color;
        }
      }
    }

    &-text {
      font-size: 25px;

      &.--positive {
        color: $oxd-secondary-four-color;
      }

      &.--negative {
        color: $oxd-feedback-danger-color;
      }
    }
  }
}

.orangehrm-employee-tracker-log {
  display: flex;
  flex-direction: row;

  &-list-header {
    font-size: 16px;
    font-weight: 800;
  }

  &-image {
    width: 60px;
    height: 60px;
    border-radius: 100%;
    display: flex;
    overflow: hidden;
    box-sizing: border-box;
    border: 0.1rem solid $oxd-background-pastel-white-color;
  }

  &-image-section {
    display: flex;
  }

  &-content-section {
    display: flex;
    flex-direction: column;
    width: 100%;
    margin-left: 1.2rem;
    margin-right: 1.2rem;
  }

  &-content-container {
    background-color: $oxd-white-color;
    border-radius: 1.2rem;
    padding: 1.2rem;
  }

  &-header {
    display: flex;
    justify-content: space-between;
    padding-bottom: 0.6rem;
    align-items: flex-start;
  }

  &-title {
    display: flex;
    @include oxd-respond-to('xs') {
      flex-direction: column;
    }
    @include oxd-respond-to('sm') {
      flex-direction: row;
    }

    &-text {
      font-weight: 700;
      padding-right: 0.6rem;
    }

    &-icon {
      font-size: 21px;

      &.--positive {
        ::v-deep(.oxd-icon) {
          color: $oxd-secondary-four-color;
        }
      }

      &.--negative {
        ::v-deep(.oxd-icon) {
          color: $oxd-feedback-danger-color;
        }
      }
    }
  }

  &-body-text {
    font-size: 14px;
  }

  &-reviewer {
    margin-top: 0.6rem;
    margin-left: 1.2rem;
    font-size: 14px;

    &-name {
      font-weight: 700;
    }
  }
}
</style>

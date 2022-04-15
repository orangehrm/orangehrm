<template>
  <div class="orangehrm-upgrade-process orangehrm-upgrader-container">
    <oxd-text
      tag="h5"
      class="orangehrm-upgrade-process-title orangehrm-upgrader-container-content"
    >
      Upgrading OrangeHRM
    </oxd-text>
    <oxd-text
      class="orangehrm-upgrade-process-content orangehrm-upgrader-container-content"
    >
      This may take some time. Please do not close the window of the progress
      become 100%
    </oxd-text>

    <div class="orangehrm-upgrade-process-list">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangehrm-upgrade-process-item">
            <oxd-text :class="getDatabaseClass" tag="h6"
              >Applying Database Changes</oxd-text
            >
            <oxd-icon
              v-if="isDatabaseChanges"
              name="check-circle-fill"
            ></oxd-icon>
            <oxd-icon v-else name="circle"></oxd-icon>
          </oxd-grid-item>
        </oxd-grid>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangehrm-upgrade-process-item">
            <oxd-text tag="h6" :class="getConfigClass"
              >Creating Configuration files</oxd-text
            >
            <oxd-icon v-if="isFileCreated" name="check-circle-fill"></oxd-icon>
            <oxd-icon v-else name="circle"></oxd-icon>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>
    </div>
    <div class="orangehrm-upgrade-process-progress">
      <oxd-progress :progress="progress" type="success" />
      <oxd-text class="orangehrm-upgrade-process-text"
        >Please Wait installation in Progress</oxd-text
      >
    </div>
  </div>
</template>

<script>
import Icon from '@ohrm/oxd/core/components/Icon/Icon.vue';
import {APIService} from '@/core/util/services/api.service';
import ProgressBar from '@ohrm/oxd/core/components/Progressbar/Progressbar';
export default {
  name: 'UpgradeScreen',
  components: {
    'oxd-icon': Icon,
    'oxd-progress': ProgressBar,
  },
  setup() {
    const http = new APIService(
      'https://8fdc0dda-8987-4f6f-9014-cb8c49a3a717.mock.pstmn.io',
      'upgrader/database-changes',
    );
    return {
      http,
    };
  },
  data() {
    return {
      progress: 0,
      isDatabaseChanges: false,
      isFileCreated: false,
    };
  },
  computed: {
    getDatabaseClass() {
      if (this.isDatabaseChanges) {
        return 'orangehrm-upgrade-process-text--bold';
      }
      return 'orangehrm-upgrade-process-text--normal';
    },
    getConfigClass() {
      if (this.isFileCreated) {
        return 'orangehrm-upgrade-process-text--bold';
      }
      return 'orangehrm-upgrade-process-text--normal';
    },
  },
  beforeMount() {
    this.getDatabaseChanges();
  },

  methods: {
    getConfigFiles() {
      this.isFileCreated = false;
      this.http.get(2).then(() => {
        this.progress = 100;
        this.isFileCreated = true;
      });
    },
    getDatabaseChanges() {
      this.isDatabaseChanges = false;
      this.http.get(1).then(() => {
        this.progress = 49;
        this.isDatabaseChanges = true;
        this.getConfigFiles();
      });
    },
  },
};
</script>

<style src="./installer-page.scss" lang="scss" scoped></style>
<style scoped lang="scss">
.orangehrm-upgrade-process {
  &-list {
    padding-top: 4rem;
  }
  &-text {
    display: flex;
    justify-content: center;
    padding-top: 0.75rem;
    &--bold {
      font-weight: 700;
    }
    &--normal {
      font-weight: 500;
    }
  }
  &-progress {
    padding-top: 10rem;
    padding-left: 0.5rem;
  }
  &-title {
    padding-top: 0;
    color: $oxd-primary-one-color;
  }
  .orangehrm-upgrade-process-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  ::v-deep(.oxd-grid-3) {
    width: 100%;
    margin: 0 !important;
  }
  ::v-deep(.oxd-icon) {
    color: $oxd-feedback-success-color;
    font-size: 18px;
  }
}
</style>
<template>
  <div class="orangehrm-admin-access-container">
    <div class="orangehrm-card-container">
      <oxd-form ref="verifyForm" method="post" @submitValid="submitRequest">
        <oxd-text tag="h6" class="orangehrm-admin-access-title">
          Reset password
        </oxd-text>

        <br />

        <oxd-text :class="noteClasses">
          Please enter your username to identify your account to reset your
          password
        </oxd-text>

        <oxd-form-row>
          <oxd-input-field
            :model-value="username"
            name="username"
            label="Username"
            label-icon="person"
            :rules="rules.username"
          />
        </oxd-form-row>

        <div class="orangehrm-admin-access-button-container">
          <oxd-button
            class="orangehrm-admin-access-button"
            display-type="ghost"
            size="large"
            label="Cancel"
            @click="onCancel"
          />
          <oxd-button
            class="orangehrm-admin-access-button"
            display-type="secondary"
            size="large"
            label="Reset Password"
            type="submit"
          />
        </div>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation';
import {required} from '@/core/util/validation/rules';

export default {
  name: 'RequestResetPassword',
  props: {
    username: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      rules: {
        username: [required],
      },
      noteClasses: {
        'orangehrm-admin-access-note': true,
        '--padding': this.error === null,
      },
    };
  },

  methods: {
    submitRequest() {
      // this.$refs.verifyForm.$el.submit();
    },
    onCancel() {
      navigate('/auth/login');
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-card-container {
  box-shadow: 3px 3px 10px $oxd-interface-gray-color;
  width: 60vh;
}

.orangehrm-admin-access {
  &-container {
    display: flex;
    height: 100vh;
    justify-content: center;
    align-items: center;
  }
  &-title {
    font-weight: 700;
  }
  &-note {
    font-size: 12px;
    &.--padding {
      padding-bottom: 1.2rem;
    }
  }
  &-button {
    flex: 1;
    margin-left: 0.6rem;
    margin-right: 0.6rem;
    &-container {
      display: flex;
      flex-direction: row;
      justify-content: center;
    }
  }
}
</style>

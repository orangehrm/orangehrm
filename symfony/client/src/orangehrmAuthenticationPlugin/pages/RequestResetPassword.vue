<template>
  <div class="orangehrm-forgot-password-container">
    <div class="orangehrm-forgot-password-wrapper">
      <div class="orangehrm-card-container">
        <oxd-form
          ref="resetForm"
          method="post"
          :action="submitUrl"
          @submitValid="onSubmit"
        >
          <oxd-text tag="h6" class="orangehrm-forgot-password-title">
            Reset Password
          </oxd-text>
          <oxd-divider />
          <card-note
            note-text="Please enter your username to identify your account to reset your
            password"
            class="orangehrm-forgot-password-card-note"
          />
          <oxd-form-row>
            <oxd-input-field
              v-model="username"
              name="username"
              label="Username"
              label-icon="person"
              :rules="rules.username"
              placeholder="username"
            />
          </oxd-form-row>
          <oxd-divider />
          <div class="orangehrm-forgot-password-button-container">
            <oxd-button
              class="orangehrm-forgot-password-button"
              display-type="ghost"
              size="large"
              label="Cancel"
              @click="onCancel"
            />
            <oxd-button
              class="orangehrm-forgot-password-button"
              display-type="secondary"
              size="large"
              label="Reset Password"
              type="submit"
            />
          </div>
        </oxd-form>
      </div>
    </div>
    <slot name="footer"></slot>
  </div>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation';
import {required} from '@/core/util/validation/rules';
import CardNote from '../components/CardNote';
import {urlFor} from '@/core/util/helper/url';
export default {
  name: 'RequestResetPassword',
  components: {
    'card-note': CardNote,
  },
  data() {
    return {
      username: '',
      rules: {
        username: [required],
      },
    };
  },
  computed: {
    submitUrl() {
      return urlFor('/auth/userNameVerify');
    },
  },
  methods: {
    onCancel() {
      navigate('/auth/login');
    },
    onSubmit() {
      this.$refs.resetForm.$el.submit();
    },
  },
};
</script>

<style src="./reset-password.scss" lang="scss" scoped></style>

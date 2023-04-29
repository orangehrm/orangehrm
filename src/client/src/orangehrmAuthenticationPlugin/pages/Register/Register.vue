<template>
  <div class="register-container">
    <div class="register-wrapper">
      <div class="orangehrm-card-container">
        <oxd-form
          ref="registerForm"
          method="post"
          :action="submitUrl"
          @submitValid="onSubmit"
        >
          <oxd-text tag="h6">
            Create Account
          </oxd-text>
          <oxd-divider />
          <oxd-text tag="h6" class="register-subheading">
            Organization Details
          </oxd-text>
          <input name="_token" :value="token" type="hidden" />
          <oxd-form-row>
            <oxd-input-field
              v-model="organizationName"
              label="Organization Name"
              required
              name="organizationName"
              :rules="rules.organizationName"
              placeholder="Organization Name"
              autofocus
            />
          </oxd-form-row>
          <oxd-divider />
          <oxd-form-row>
            <oxd-input-field
              v-model="countryCode"
              label="Country"
              name="countryCode"
              required
              type="select"
              :options="countries"
              :rules="rules.countryCode"
            />
          </oxd-form-row>
          <oxd-divider />
          <oxd-text tag="h6" class="register-subheading">
            Admin User
          </oxd-text>
          <oxd-form-row>
            <oxd-input-field
              v-model="firstName"
              name="firstName"
              :rules="rules.firstName"
              placeholder="First Name"
              label="First Name"
              required
            />
          </oxd-form-row>
          <oxd-divider />
          <oxd-form-row>
            <oxd-input-field
              v-model="lastName"
              name="lastName"
              :rules="rules.lastName"
              placeholder="Last Name"
              label="Last Name"
            />
          </oxd-form-row>
          <oxd-divider />
          <oxd-form-row>
            <oxd-input-field
              v-model="email"
              name="email"
              :rules="rules.email"
              label="Email"
              required
            />
          </oxd-form-row>
          <oxd-divider />
          <oxd-form-row>
            <oxd-input-field
              v-model="password"
              type="password"
              name="password"
              label="Password"
              :rules="rules.password"
              required
            />
          </oxd-form-row>
          <oxd-divider />
          <oxd-form-row>
            <oxd-input-field
              v-model="confirmPassword"
              name="confirmPassword"
              type="password"
              label="Confirm Password"
              :rules="rules.passwordConfirm"
              required
            />
          </oxd-form-row>

          <oxd-divider />

          <oxd-form-actions class="register-button-wrapper">
            <oxd-button
              class="register-button orangehrm-forgot-password-button--reset"
              display-type="secondary"
              size="large"
              label="Create Account"
              type="submit"
            />
          </oxd-form-actions>

          <div class="orangehrm-login-forgot">
            <oxd-text
              class="orangehrm-login-forgot-header"
              @click="goToLoginPage"
            >
              {{ $t('auth.login') }}?
            </oxd-text>
          </div>
        </oxd-form>
      </div>
    </div>
    <div class="orangehrm-login-footer">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
  validEmailFormat,
} from '@/core/util/validation/rules';
import {checkPassword} from '@/core/util/helper/password';
import {navigate} from '@/core/util/helper/navigation';
import {urlFor} from '@/core/util/helper/url';
import debounce from '@ohrm/oxd/utils/debounce';
import {APIService} from '@/core/util/services/api.service';

export default {
  name: 'Register',

  setup() {
    const http = new APIService(window.appGlobal.baseUrl, '/auth/register');
    return {
      http,
    };
  },

  props: {
    error: {
      type: Object,
      default: () => null,
    },
    token: {
      type: String,
      required: true,
    },
    countries: {
      type: Array,
      default: Array.from([]),
    },
    isDemoMode: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      organizationName: '',
      countryCode: '',
      isUsernameValid: false,
      firstName: '',
      lastName: '',
      email: '',
      password: '',
      confirmPassword: '',
      registrationConsent: true,
      rules: {
        // Organization rules
        organizationName: [required, shouldNotExceedCharLength(100)],
        countryCode: [required],

        // Admin user rules
        firstName: [required, shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        email: [
          required,
          shouldNotExceedCharLength(50),
          validEmailFormat,
          v => (!!v && this.isUsernameValid) || 'This email is already taken',
        ],
        password: [required, shouldNotExceedCharLength(64), checkPassword],
        passwordConfirm: [
          required,
          shouldNotExceedCharLength(64),
          v => (!!v && v === this.password) || 'Passwords do not match',
        ],
      },
    };
  },

  computed: {
    submitUrl() {
      return urlFor('/auth/signup');
    },
    username() {
      return this.email;
    },
  },

  watch: {
    username: debounce(function(value) {
      new APIService('/installer/index.php').http
        .post('/installer/api/validate-username', {
          type: 'username',
          value: value,
        })
        .then(({data}) => {
          this.isUsernameValid = data.status;
        });
    }, 300),
  },

  mounted() {
    this.testFields();
  },

  methods: {
    onSubmit() {
      console.log(this.$refs.registerForm.$el);
      this.$refs.registerForm.$el.submit();
    },
    goToLoginPage() {
      navigate('/auth/login');
    },
    testFields() {
      this.organizationName = 'Test Org';
      this.firstName = 'James';
      this.lastName = 'Essien';
      this.email = 'james@example.com';
      this.password = '56$$bWsS5v6e7Ml6';
      this.confirmPassword = '56$$bWsS5v6e7Ml6';
    },
  },
};
</script>

<style src="./register.scss" lang="scss" scoped></style>

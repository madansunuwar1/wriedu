<template>
  <div id="main-wrapper" class="p-0 bg-white auth-customizer-none">
    <div class="auth-login position-relative overflow-hidden d-flex align-items-center justify-content-center px-7 px-xxl-0 rounded-3 h-n20">
      <div class="auth-login-shape position-relative w-100">
        <div class="auth-login-wrapper card mb-0 container position-relative z-1 h-100 mh-n100" data-simplebar>
          <div class="card-body">
            <a href="/">
              <img src="/img/wri.png" class="light-logo" alt="WRI Logo" style="max-height: 50px;">
            </a>
            <div class="row align-items-center justify-content-around pt-6 pb-5">
              <div class="col-lg-6 col-xl-5 d-none d-lg-block">
                <div class="text-center text-lg-start">
                  <img src="/assets/images/backgrounds/login-security.png" alt="wri-img" class="img-fluid">
                </div>
              </div>
              <div class="col-lg-6 col-xl-5">
                <h2 class="mb-6 fs-8 fw-bolder">Welcome to WRI Education</h2>
                <p class="text-dark fs-4 mb-7">Registration Form</p>

                <div class="form-transition-container" :class="{ 'sliding': transitioning }">
                  <form v-if="showRegisterForm" key="register-form" @submit.prevent="submitForm" class="registration-form">
                      <!-- Name -->
                      <div class="mb-7">
                        <label for="name" class="form-label fw-bold">Name</label>
                        <input id="name" class="form-control py-6" type="text" v-model="form.name" :disabled="isLoading" required autofocus autocomplete="name">
                      </div>

                      <!-- Last Name -->
                      <div class="mb-7">
                        <label for="last" class="form-label fw-bold">Last Name</label>
                        <input id="last" class="form-control py-6" type="text" v-model="form.last" :disabled="isLoading" required autocomplete="last">
                      </div>

                      <!-- Gender -->
                      <div class="mb-7">
                        <label for="gender" class="form-label fw-bold">Gender</label>
                        <select id="gender" class="form-control py-6" v-model="form.gender" :disabled="isLoading" required>
                          <option value="" disabled>Select Gender</option>
                          <option value="male">Male</option>
                          <option value="female">Female</option>
                        </select>
                      </div>

                      <!-- Email Address -->
                      <div class="mb-7">
                        <label for="email" class="form-label fw-bold">Email address</label>
                        <input id="email" class="form-control py-6" type="email" v-model="form.email" :disabled="isLoading" required autocomplete="username">
                      </div>

                      <!-- Password -->
                      <div class="mb-7">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <div class="position-relative">
                          <input id="password" class="form-control py-6" type="password" v-model="form.password" :disabled="isLoading" required autocomplete="new-password">
                          <span id="password-toggle" class="position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer" @click="togglePasswordVisibility('password')">
                            <i class="fas fa-eye"></i>
                          </span>
                        </div>
                      </div>

                      <!-- Confirm Password -->
                      <div class="mb-7">
                        <label for="password_confirmation" class="form-label fw-bold">Confirm Password</label>
                        <div class="position-relative">
                          <input id="password_confirmation" class="form-control py-6" type="password" v-model="form.password_confirmation" :disabled="isLoading" required autocomplete="new-password">
                          <span id="password-confirmation-toggle" class="position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer" @click="togglePasswordVisibility('password_confirmation')">
                            <i class="fas fa-eye"></i>
                          </span>
                        </div>
                      </div>

                      <button type="submit" class="btn btn-primary w-100 mb-7 rounded-pill py-6" :disabled="isLoading">
                        <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Register
                      </button>

                      <div class="d-flex align-items-center">
                        <p class="fs-3 mb-0 fw-medium">Already have an Account?</p>
                        <router-link to="/login" class="text-primary fw-bold ms-2 fs-3">
                          Sign In
                        </router-link>
                      </div>
                  </form>

                  <IndustrialOtpVerification v-if="!showRegisterForm" key="otp-verification" ref="otpVerification" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import IndustrialOtpVerification from '../Auth/VerifyEmail.vue';

export default {
  components: {
    IndustrialOtpVerification
  },
  data() {
    return {
      form: {
        name: '',
        last: '',
        gender: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      errors: {},
      showRegisterForm: true,
      isLoading: false,
      transitioning: false
    };
  },
  methods: {
    async submitForm() {
      if (this.transitioning) return;

      this.isLoading = true;

      try {
        const response = await axios.post('/register', this.form, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });

        console.log('Registration successful:', response.data);

        if (response.data.session_id) {
          localStorage.setItem('otp_session_id', response.data.session_id);
        }

        this.errors = {};

        // Trigger the transition after successful registration
        this.transitioning = true;
        this.$nextTick(() => {
          // Wait for the "sliding" class to be applied and the animation to start

          // THEN, after a short delay (duration of transition), switch components
          setTimeout(() => {
            this.showRegisterForm = false;
            this.transitioning = false; // Transition complete
            this.$nextTick(() => {
              // Initialize the OTP component
              this.$refs.otpVerification.startTimer();
              this.$refs.otpVerification.startResendCooldown();

              if (this.$refs.otpVerification.$refs.otpInputs && this.$refs.otpVerification.$refs.otpInputs[0]) {
                  this.$refs.otpVerification.$refs.otpInputs[0].focus();
              }
            });
          }, 500); // Match this delay to the CSS transition duration (0.5s)
        });

      } catch (error) {
        console.error('Registration failed:', error);
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          alert('An unexpected error occurred.');
        }
        this.transitioning = false;
      } finally {
        this.isLoading = false;
      }
    },
    togglePasswordVisibility(field) {
      const passwordInput = document.getElementById(field);
      const toggleIcon = document.getElementById(`${field}-toggle`);

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.innerHTML = '<i class="fas fa-eye-slash"></i>';
      } else {
        passwordInput.type = "password";
        toggleIcon.innerHTML = '<i class="fas fa-eye"></i>';
      }
    }
  }
};
</script>

<style scoped>

.form-transition-container {
    position: relative;
    overflow: hidden; /* Important: Prevents content from overflowing during transition */
    height: auto; /* Adjust as needed based on your content's height */
}

.form-transition-container > * {
    transition: transform 0.5s ease-in-out; /* Apply transition to direct children only */
}

.registration-form {
  /* Initial state: visible */
  transform: translateX(0);
}

.form-transition-container:not(.sliding) > .registration-form {
  /* When not sliding, registration form is in its normal position */
  transform: translateX(0);
}

.form-transition-container.sliding > .registration-form {
  /* During slide-out */
  transform: translateX(-100%);
}

.industrial-otp-verification {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
}

.form-transition-container:not(.sliding) > .industrial-otp-verification {
    transform: translateX(100%);
}

.form-transition-container.sliding > .industrial-otp-verification {
    transform: translateX(0);
}

</style>
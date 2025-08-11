<template>
  <div id="main-wrapper" class="p-0 bg-white auth-customizer-none" :style="{ backgroundImage: `url(${backgroundImage})` }">
    <div class="auth-login position-relative overflow-hidden d-flex align-items-center justify-content-center px-7 px-xxl-0 rounded-3 h-n20">
      <div class="auth-login-shape position-relative w-100">
        <div class="auth-login-wrapper card mb-0 container position-relative z-1 h-100 mh-n100" data-simplebar>
          <div class="card-body">
            <a href="index.html" class="">
            
              <img :src="logo" class="dark-logo" alt="Logo-light" width="100px">
            </a>
            <div class="row align-items-center justify-content-around pt-6 pb-5">
              <div class="col-lg-6 col-xl-5 d-none d-lg-block">
                <div class="text-center text-lg-start">
                  <img :src="loginImage" alt="login-image" class="img-fluid">
                </div>
              </div>
              <div class="col-lg-6 col-xl-5">
                <h2 class="mb-6 fs-8 fw-bolder">Welcome Back</h2>
                <p class="text-dark fs-4 mb-7">Your Admin Dashboard</p>

                <form @submit.prevent="login">
                  <div class="mb-7">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control py-6" id="email" v-model="email" required autofocus autocomplete="username">
                  </div>

                  <div class="mb-9">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <div class="position-relative">
                      <input :type="passwordFieldType" class="form-control py-6" id="password" v-model="password" required autocomplete="current-password">
                      <span id="password-toggle" class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer" @click="togglePasswordVisibility">
                        <i :class="passwordToggleIcon"></i>
                      </span>
                    </div>
                  </div>

                  <div class="d-md-flex align-items-center justify-content-between mb-7 pb-1">
                    <div class="form-check mb-3 mb-md-0">
                      <input class="form-check-input primary" type="checkbox" id="remember_me" v-model="remember">
                      <label class="form-check-label text-dark fs-3" for="remember_me">
                        Remember me
                      </label>
                    </div>
                    <a class="text-primary fw-medium fs-3 fw-bold" href="#">Forgot your password?</a>
                  </div>

                  <button type="submit" class="btn btn-primary w-100 mb-7 rounded-pill">Log in</button>
                  <div class="d-flex align-items-center">
                    <p class="fs-3 mb-0 fw-medium">Don't have an Account?</p>
                    <router-link 
  to="/register" 
  class="text-primary fw-bold ms-2 fs-3"
>
  Sign Up
</router-link>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      password: '',
      remember: false,
      passwordFieldType: 'password',
      passwordToggleIcon: 'fas fa-eye text-gray-400 hover:text-gray-600',
      backgroundImage: '/assets/images/backgrounds/bg1.jpg',
      logo: '/img/wri.png',
      loginImage: '/assets/images/backgrounds/bg1.jpg'
    };
  },
  methods: {
    togglePasswordVisibility() {
      this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
      this.passwordToggleIcon = this.passwordFieldType === 'password' ? 'fas fa-eye text-gray-400 hover:text-gray-600' : 'fas fa-eye-slash text-gray-400 hover:text-gray-600';
    },
    async login() {
      try {
        const response = await axios.post('/login', {
          email: this.email,
          password: this.password,
          remember: this.remember
        });
        // Handle successful login response
        window.location.href = '/app/dashboard';
      } catch (error) {
        // Handle login error
        console.error('Login error:', error);
      }
    }
  }
};
</script>

<style scoped>
/* Add your styles here */
</style>

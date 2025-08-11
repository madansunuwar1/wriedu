<template>
  <div class="otp-container">
    <div class="otp-card">
      <div class="otp-header">
        <img src="/img/wri.png" alt="Company Logo" class="otp-logo">
        <h2 class="otp-title">Verification Code</h2>
        <!-- Bootstrap Error Alert -->
        <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <strong>Error!</strong> {{ errorMessage }}
          <button type="button" class="btn-close" @click="errorMessage = ''" aria-label="Close"></button>
        </div>
        <!-- Bootstrap Success Alert -->
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle me-2"></i>
          <strong>Success!</strong> {{ successMessage }}
          <button type="button" class="btn-close" @click="successMessage = ''" aria-label="Close"></button>
        </div>
        <p class="otp-subtitle">Enter the 6-digit code sent to your email.</p>
      </div>
      <div class="otp-form-group">
        <input
          v-for="(digit, index) in otp"
          :key="index"
          :id="`otp-digit-${index}`"
          type="text"
          maxlength="1"
          class="otp-digit"
          v-model="otp[index]"
          @input="handleInput(index, $event)"
          @keydown="handleKeydown(index, $event)"
          @paste="handlePaste"
          :ref="el => { if (el) otpInputRefs[index] = el }"
          :disabled="isSubmitting"
          aria-describedby="otp-help"
          placeholder="-"
        />
      </div>
      <div class="otp-timer">
        <span class="timer-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h3a.75.75 0 000-1.5h-2.25V6z" clip-rule="evenodd" />
          </svg>
        </span>
        Expires in: {{ formattedTime }}
      </div>
      <button
        class="otp-button"
        @click="submitForm"
        :disabled="isVerifyButtonDisabled"
        :class="{ 'otp-button-loading': isSubmitting }"
      >
        <span v-if="isSubmitting" class="spinner"></span>
        {{ isSubmitting ? 'Verifying...' : 'Verify Code' }}
      </button>
      <div class="otp-resend">
        <p>Didn't receive the code?</p>
        <button
          type="button"
          class="otp-resend-button"
          :disabled="isResendButtonDisabled"
          @click="resendCode"
        >
          {{ resendButtonText }}
        </button>
      </div>
      <div class="otp-footer">
        <small>© {{ new Date().getFullYear() }} WRI Education. All rights reserved.</small>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { ref, reactive, computed, onMounted, onUnmounted, nextTick } from 'vue';

export default {
  name: 'OTPComponent',
  setup() {
    // Reactive data
    const otp = reactive(['', '', '', '', '', '']); // 6-digit OTP
    const timeLeft = ref(600); // 10 minutes in seconds
    const timer = ref(null);
    const isSubmitting = ref(false);
    const errorMessage = ref('');
    const successMessage = ref('');
    const isResendButtonDisabled = ref(true);
    const resendCooldown = ref(60);
    const resendTimer = ref(null);
    const otpInputRefs = reactive({});

    // Setup axios defaults
    const setupAxios = () => {
      // Get CSRF token from meta tag
      const token = document.querySelector('meta[name="csrf-token"]');
      if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
      }
      
      // Set default headers
      axios.defaults.headers.common['Accept'] = 'application/json';
      axios.defaults.headers.common['Content-Type'] = 'application/json';
      
      // Add request interceptor for debugging
      axios.interceptors.request.use(
        (config) => {
          console.log('Request config:', config);
          return config;
        },
        (error) => {
          console.error('Request error:', error);
          return Promise.reject(error);
        }
      );

      // Add response interceptor for better error handling
      axios.interceptors.response.use(
        (response) => {
          return response;
        },
        (error) => {
          console.error('Response error:', error);
          
          if (error.response) {
            // Server responded with error status
            console.error('Error response data:', error.response.data);
            console.error('Error response status:', error.response.status);
          } else if (error.request) {
            // Request was made but no response received
            console.error('No response received:', error.request);
          } else {
            // Something else happened
            console.error('Request setup error:', error.message);
          }
          
          return Promise.reject(error);
        }
      );
    };

    // Computed properties
    const formattedTime = computed(() => {
      const minutes = Math.floor(timeLeft.value / 60);
      const seconds = timeLeft.value % 60;
      return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    });

    const isVerifyButtonDisabled = computed(() => {
      return isSubmitting.value || otp.some(digit => digit === '');
    });

    const resendButtonText = computed(() => {
      if (isResendButtonDisabled.value) {
        return `Resend in ${resendCooldown.value}s`;
      } else {
        return 'Resend Code';
      }
    });

    // Methods
    const startTimer = () => {
      timeLeft.value = 600; // 10 minutes
      if (timer.value) {
        clearInterval(timer.value);
      }
      timer.value = setInterval(() => {
        timeLeft.value--;
        if (timeLeft.value <= 0) {
          clearInterval(timer.value);
          isResendButtonDisabled.value = false; // Enable resend when timer expires
          errorMessage.value = "Code expired. Please resend.";
        }
      }, 1000);
    };

    const startResendCooldown = () => {
      isResendButtonDisabled.value = true;
      resendCooldown.value = 60;
      if (resendTimer.value) {
        clearInterval(resendTimer.value);
      }
      resendTimer.value = setInterval(() => {
        resendCooldown.value--;
        if (resendCooldown.value <= 0) {
          clearInterval(resendTimer.value);
          isResendButtonDisabled.value = false;
        }
      }, 1000);
    };

    const handleInput = (index, event) => {
      const value = event.target.value.replace(/[^0-9]/g, '').substring(0, 1);
      otp[index] = value;
      
      if (value && index < 5) {
        nextTick(() => {
          if (otpInputRefs[index + 1]) {
            otpInputRefs[index + 1].focus();
          }
        });
      }
      
      // Clear error message when user starts typing
      if (errorMessage.value) {
        errorMessage.value = '';
      }
    };

    const handleKeydown = (index, event) => {
      if (event.key === 'Backspace' && !otp[index] && index > 0) {
        if (otpInputRefs[index - 1]) {
          otpInputRefs[index - 1].focus();
        }
      }
    };

    const handlePaste = (event) => {
      event.preventDefault();
      const pasteData = (event.clipboardData || window.clipboardData).getData('text/plain').trim();
      const cleanedData = pasteData.replace(/\D/g, '');

      if (cleanedData.length === 6) {
        for (let i = 0; i < 6; i++) {
          if (i < cleanedData.length) {
            otp[i] = cleanedData[i];
          }
        }

        nextTick(() => {
          const lastInputIndex = Math.min(cleanedData.length - 1, 5);
          if (otpInputRefs[lastInputIndex]) {
            otpInputRefs[lastInputIndex].focus();
          }
        });
      } else {
        console.warn('Pasted data is not a 6-digit code.');
      }
    };

    const submitForm = async () => {
      if (isSubmitting.value) return;
      
      // Validate OTP
      const otpCode = otp.join('');
      if (otpCode.length !== 6 || !/^\d{6}$/.test(otpCode)) {
        errorMessage.value = 'Please enter all 6 digits.';
        return;
      }

      isSubmitting.value = true;
      errorMessage.value = '';
      successMessage.value = '';
      
      try {
        console.log('Submitting OTP:', otpCode);
        
        // Send as array format to match your blade template expectation
        const response = await axios.post('/verify-otp', { 
          otp: otp.filter(digit => digit !== '') // Send as array
        });
        
        console.log('Response:', response.data);
        
        if (response.data.success) {
          successMessage.value = response.data.message || 'Verification successful!';
          
          // Redirect if provided
          if (response.data.redirect) {
            setTimeout(() => {
              window.location.href = response.data.redirect;
            }, 1500);
          }
          
          // Emit event for parent component if needed
          // this.$emit('verified', response.data);
        } else {
          errorMessage.value = response.data.message || 'Verification failed.';
          // Clear OTP inputs on failure
          clearOTPInputs();
        }
      } catch (error) {
        console.error('OTP verification error:', error);
        
        let errorMsg = 'An error occurred during verification.';
        
        if (error.response) {
          // Server responded with error
          if (error.response.data && error.response.data.message) {
            errorMsg = error.response.data.message;
          } else if (error.response.data && error.response.data.error) {
            errorMsg = error.response.data.error;
          } else if (error.response.status === 422) {
            // Validation errors
            if (error.response.data.errors) {
              const errors = Object.values(error.response.data.errors).flat();
              errorMsg = errors.join(', ');
            } else {
              errorMsg = 'Invalid OTP format.';
            }
          } else if (error.response.status === 500) {
            errorMsg = 'Server error. Please try again.';
          }
        } else if (error.request) {
          errorMsg = 'Network error. Please check your connection.';
        }
        
        errorMessage.value = errorMsg;
        clearOTPInputs();
      } finally {
        isSubmitting.value = false;
      }
    };

    const clearOTPInputs = () => {
      for (let i = 0; i < 6; i++) {
        otp[i] = '';
      }
      // Focus on first input
      nextTick(() => {
        if (otpInputRefs[0]) {
          otpInputRefs[0].focus();
        }
      });
    };

    const resendCode = async () => {
      if (isResendButtonDisabled.value) return;
      
      isResendButtonDisabled.value = true;
      if (resendTimer.value) {
        clearInterval(resendTimer.value);
      }
      resendCooldown.value = 60;
      startResendCooldown();
      
      try {
        console.log('Resending OTP...');
        const response = await axios.post('/resend-otp');
        
        console.log('Resend response:', response.data);
        
        if (response.data.success) {
          startTimer();
          errorMessage.value = '';
          successMessage.value = response.data.message || 'New code sent!';
          clearOTPInputs();
        } else {
          errorMessage.value = response.data.message || 'Failed to resend code.';
          isResendButtonDisabled.value = false;
          if (resendTimer.value) {
            clearInterval(resendTimer.value);
          }
        }
      } catch (error) {
        console.error('Resend OTP error:', error);
        
        let errorMsg = 'An error occurred while resending the code.';
        
        if (error.response && error.response.data && error.response.data.message) {
          errorMsg = error.response.data.message;
        } else if (error.response && error.response.status === 500) {
          errorMsg = 'Server error. Please try again.';
        }
        
        errorMessage.value = errorMsg;
        isResendButtonDisabled.value = false;
        if (resendTimer.value) {
          clearInterval(resendTimer.value);
        }
      }
    };

    // Lifecycle hooks
    onMounted(() => {
      setupAxios();
      startTimer();
      startResendCooldown();
      nextTick(() => {
        if (otpInputRefs[0]) {
          otpInputRefs[0].focus();
        }
      });
    });

    onUnmounted(() => {
      if (timer.value) {
        clearInterval(timer.value);
      }
      if (resendTimer.value) {
        clearInterval(resendTimer.value);
      }
    });

    return {
      otp,
      timeLeft,
      timer,
      isSubmitting,
      errorMessage,
      successMessage,
      isResendButtonDisabled,
      resendCooldown,
      resendTimer,
      otpInputRefs,
      formattedTime,
      isVerifyButtonDisabled,
      resendButtonText,
      startTimer,
      startResendCooldown,
      handleInput,
      handleKeydown,
      handlePaste,
      submitForm,
      resendCode
    };
  }
};
</script>

<style>


.otp-card {
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  padding: 32px;
  width: 100%;
  max-width: 480px;
  text-align: center;
}

.otp-header {
  margin-bottom: 32px;
}

.otp-logo {
  width: 60px;
  height: 60px;
  margin: 0 auto 16px;
  display: block;
  border-radius: 8px;
  object-fit: contain;
}

.otp-title {
  font-size: 24px;
  font-weight: 600;
  color: #333333;
  margin-bottom: 8px;
}

.otp-subtitle {
  font-size: 16px;
  color: #777777;
  margin: 0;
}

.otp-form-group {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-bottom: 24px;
}

.otp-digit {
  width: 48px;
  height: 48px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  text-align: center;
  font-size: 18px;
  font-weight: 500;
  color: #333333;
  outline: none;
  padding: 0;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.otp-digit::placeholder {
  color: #999999;
  opacity: 1;
}

.otp-digit:focus {
  border-color: #198b3f;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.otp-digit:disabled {
  background-color: #f8f9fa;
  border-color: #e9ecef;
  cursor: not-allowed;
}

.otp-timer {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666666;
  margin-bottom: 24px;
  font-size: 14px;
}

.timer-icon {
  margin-right: 8px;
  display: flex;
  align-items: center;
}

.timer-icon svg {
  width: 18px;
  height: 18px;
  fill: #666666;
}

.otp-button {
  background-color: #1f6924;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  padding: 12px 32px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s ease;
  width: 100%;
  position: relative;
  min-height: 48px;
}

.otp-button:hover:not(:disabled) {
  background-color: #295b33;
}

.otp-button:disabled {
  background-color: #737577;
  cursor: not-allowed;
}

.otp-button-loading {
  color: transparent !important;
}

.spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 20px;
  height: 20px;
  border: 2px solid #ffffff;
  border-top: 2px solid transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: translate(-50%, -50%) rotate(360deg);
  }
}

.otp-resend {
  margin-top: 24px;
}

.otp-resend p {
  color: #777777;
  margin: 0 0 8px 0;
  font-size: 14px;
}

.otp-resend-button {
  background: none;
  border: none;
  color: #17862d;
  cursor: pointer;
  transition: color 0.2s ease;
  font-size: 14px;
  font-weight: 500;
}

.otp-resend-button:hover:not(:disabled) {
  color: #245c1b;
  text-decoration: underline;
}

.otp-resend-button:disabled {
  color: #6c757d;
  cursor: not-allowed;
}

.otp-footer {
  margin-top: 32px;
}

.otp-footer small {
  color: #a8a6a6;
  font-size: 12px;
}

/* Bootstrap alerts will handle their own styling */
.alert {
  margin-bottom: 16px;
  text-align: left;
}

.alert i {
  margin-right: 8px;
}

@media (max-width: 576px) {
  .otp-container {
    padding: 16px;
  }
  
  .otp-card {
    padding: 24px 20px;
  }
  
  .otp-digit {
    width: 40px;
    height: 40px;
    font-size: 16px;
  }
  
  .otp-form-group {
    gap: 6px;
  }
  
  .otp-title {
    font-size: 20px;
  }
  
  .otp-subtitle {
    font-size: 14px;
  }
}
</style>
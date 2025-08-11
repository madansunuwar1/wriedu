<!-- otp-verification.blade.php -->
<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ __('OTP Verification') }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('Please enter the verification code we sent to your email.') }}
                </p>
                <!-- Add timer display -->
                <p class="mt-2 text-sm" id="timer-display"></p>
            </div>

            @if (session('error'))
                <div class="mb-4 font-medium text-sm text-red-600">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('expired'))
                <div class="mb-4 font-medium text-sm text-red-600">
                    {{ __('Your verification code has expired. Please request a new one.') }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify') }}" class="mt-4" id="otpForm">
                @csrf

                <div class="flex justify-center gap-2 mb-4">
                    @for ($i = 1; $i <= 6; $i++)
                        <div class="w-12">
                            <input
                                type="text"
                                name="otp[]"
                                id="otp-{{ $i }}"
                                maxlength="1"
                                class="w-full h-12 text-center text-2xl border-2 rounded-lg focus:border-blue-500 focus:ring-blue-500 @error('otp.'.$i-1) border-red-500 @enderror"
                                required
                                autocomplete="off"
                                pattern="[0-9]"
                                inputmode="numeric"
                            >
                        </div>
                    @endfor
                </div>

                @error('otp')
                    <div class="mb-4 text-sm text-red-600">{{ $message }}</div>
                @enderror

                <div class="flex flex-col gap-4">
                    <button type="submit" 
                            id="verify-button"
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Verify Code') }}
                    </button>
                </div>
            </form>

            <div class="mt-4 flex items-center justify-between">
                <form method="POST" action="{{ route('otp.resend') }}" class="inline" id="resendForm">
                    @csrf
                    <button type="submit" 
                            id="resend-button"
                            class="text-sm text-blue-600 hover:text-blue-900">
                        {{ __('Resend verification code') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // OTP input handling
            const inputs = document.querySelectorAll('input[name^="otp"]');
            const form = document.getElementById('otpForm');
            const resendButton = document.getElementById('resend-button');
            const verifyButton = document.getElementById('verify-button');
            const timerDisplay = document.getElementById('timer-display');
            
            let timeLeft = {{ session('otp_expiry', 600) }}; // 10 minutes in seconds
            let timerId = null;

            function startTimer() {
                clearInterval(timerId);
                timeLeft = 600; // Reset to 10 minutes
                updateTimerDisplay();
                
                timerId = setInterval(() => {
                    timeLeft--;
                    updateTimerDisplay();
                    
                    if (timeLeft <= 0) {
                        clearInterval(timerId);
                        handleExpiredOTP();
                    }
                }, 1000);
            }

            function updateTimerDisplay() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                timerDisplay.textContent = `Time remaining: ${timeString}`;
                timerDisplay.className = `mt-2 text-sm ${timeLeft < 60 ? 'text-red-600' : 'text-gray-600'}`;
            }

            function handleExpiredOTP() {
                verifyButton.disabled = true;
                verifyButton.classList.add('opacity-50', 'cursor-not-allowed');
                timerDisplay.textContent = 'OTP has expired. Please request a new one.';
                timerDisplay.className = 'mt-2 text-sm text-red-600';
                resendButton.disabled = false;
                resendButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            // Initialize timer on page load
            startTimer();

            // Handle resend button click
            resendButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Disable resend button temporarily
                resendButton.disabled = true;
                resendButton.classList.add('opacity-50', 'cursor-not-allowed');
                
                // Submit resend form
                fetch(document.getElementById('resendForm').action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reset form and start new timer
                        inputs.forEach(input => input.value = '');
                        verifyButton.disabled = false;
                        verifyButton.classList.remove('opacity-50', 'cursor-not-allowed');
                        startTimer();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resendButton.disabled = false;
                    resendButton.classList.remove('opacity-50', 'cursor-not-allowed');
                });
            });

            // Input field handling
            inputs.forEach((input, index) => {
                // Auto-focus next input
                input.addEventListener('input', function(e) {
                    if (this.value.length === 1) {
                        if (inputs[index + 1]) {
                            inputs[index + 1].focus();
                        }
                        checkFormCompletion();
                    }
                });

                // Handle backspace
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (!this.value && inputs[index - 1]) {
                            inputs[index - 1].focus();
                            inputs[index - 1].value = '';
                        } else {
                            this.value = '';
                        }
                        e.preventDefault();
                        checkFormCompletion();
                    }
                });

                // Allow only numbers
                input.addEventListener('keypress', function(e) {
                    if (e.key.match(/[^0-9]/)) {
                        e.preventDefault();
                    }
                });
            });

            // Handle paste event
            document.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').trim();
                const numbers = pastedData.match(/[0-9]/g);
                
                if (numbers) {
                    numbers.slice(0, 6).forEach((number, idx) => {
                        if (inputs[idx]) {
                            inputs[idx].value = number;
                        }
                    });
                    if (inputs[5].value) inputs[5].focus();
                    checkFormCompletion();
                }
            });

            function checkFormCompletion() {
                const isComplete = Array.from(inputs).every(input => input.value.length === 1);
                verifyButton.disabled = !isComplete || timeLeft <= 0;
                verifyButton.classList.toggle('opacity-50', !isComplete || timeLeft <= 0);
                verifyButton.classList.toggle('cursor-not-allowed', !isComplete || timeLeft <= 0);
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                if (timeLeft <= 0) {
                    e.preventDefault();
                    handleExpiredOTP();
                }
            });
        });
    </script>
</x-guest-layout>
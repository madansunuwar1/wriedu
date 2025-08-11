<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <title>WRI Education Consultancy</title>
    <link rel="icon" type="image/png" href="{{ asset('img/wri.png') }}">
</head>
<body>
    <script>
        
class SessionManager {
    constructor() {
        this.timeoutDuration = 1800000;
        this.warningThreshold = 1000; // 1 second warning
        this.lastActivity = Date.now();
        this.timeoutTimer = null;
        this.warningTimer = null;
        this.isWarningShown = false;
    }

    initialize() {
        this.setupEventListeners();
        this.startTimers();
    }

    setupEventListeners() {
        const events = ['mousedown', 'keydown', 'scroll', 'touchstart'];
        events.forEach(event => {
            document.addEventListener(event, () => this.resetTimers());
        });
    }

    startTimers() {
        this.checkSession();
        this.timeoutTimer = setInterval(() => this.checkSession(), 1000);
    }

    resetTimers() {
        this.lastActivity = Date.now();
        if (this.isWarningShown) {
            this.hideWarning();
        }
    }

    async checkSession() {
        const currentTime = Date.now();
        const inactiveTime = currentTime - this.lastActivity;

        if (inactiveTime >= this.timeoutDuration) {
            this.handleTimeout();
        } else if (inactiveTime >= this.warningThreshold && !this.isWarningShown) {
            this.showWarning();
        }
    }

    showWarning() {
        const modal = document.getElementById('timeoutModal');
        const message = document.getElementById('timeoutMessage');
        const remainingTime = Math.ceil((this.timeoutDuration - (Date.now() - this.lastActivity)) / 1000);

        message.textContent = `Your session will expire in ${remainingTime} second(s). Would you like to continue?`;
        modal.style.display = 'block';
        this.isWarningShown = true;
    }

    hideWarning() {
        const modal = document.getElementById('timeoutModal');
        modal.style.display = 'none';
        this.isWarningShown = false;
    }

    async handleTimeout() {
        clearInterval(this.timeoutTimer);
        
        try {
            // First attempt to logout via AJAX
            const response = await fetch('/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                window.location.href = '/login?timeout=1';
            } else {
                // Fallback redirect
                window.location.href = '/login?timeout=1';
            }
        } catch (error) {
            // If AJAX fails, redirect anyway
            window.location.href = '/login?timeout=1';
        }
    }

    extendSession() {
        this.resetTimers();
        this.hideWarning();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const sessionManager = new SessionManager();
    sessionManager.initialize();
    
    // Make extendSession globally available
    window.extendSession = () => sessionManager.extendSession();
});


let countdownTimer;
let secondsLeft = 60;

function showTimeoutWarning() {
    const modal = document.getElementById('timeoutModal');
    modal.style.display = 'block';
    startCountdown();
}

function startCountdown() {
    const countdownElement = document.getElementById('countdown');
    
    countdownTimer = setInterval(() => {
        secondsLeft--;
        countdownElement.textContent = secondsLeft;
        
        if (secondsLeft <= 0) {
            clearInterval(countdownTimer);
            window.location.href = '/login';
        }
    }, 1000);
}

// Example: Show warning when 1 minute remains
setTimeout(showTimeoutWarning, sessionTimeoutWarningAt);

    </script>
</body>
</html>
<template>
  <div>
    <header>
      <div class="header-container">
        <div class="logo">
          <img src="/img/wri.png" width="60px" height="80px" style="margin-bottom:20px;"/>
        </div>
        <nav class="nav-links">
          <router-link to="/login" class="nav-link">Log in</router-link>
          <router-link to="/register" class="nav-link">Register</router-link>
        </nav>
      </div>
    </header>

    <main class="main-content">
      <section class="hero-section">
        <h1 class="hero-title">Transform Your Educational Institution</h1>
        <p class="hero-text">
          Revolutionize your educational consultancy with our cutting-edge CRM system. Designed specifically for education professionals, our platform streamlines operations and enhances student success.
        </p>

        <div class="features-grid">
          <div class="feature-card">
            <i class="fas fa-users feature-icon"></i>
            <h3 class="feature-title">Student Management</h3>
            <p class="feature-text">Track student progress, manage applications, and maintain detailed profiles efficiently.</p>
          </div>
          <div class="feature-card">
            <i class="fas fa-calendar-alt feature-icon"></i>
            <h3 class="feature-title">Appointment Scheduling</h3>
            <p class="feature-text">Streamline consultation bookings with automated scheduling system.</p>
          </div>
          <div class="feature-card">
            <i class="fas fa-chart-line feature-icon"></i>
            <h3 class="feature-title">Performance Analytics</h3>
            <p class="feature-text">Generate insights with comprehensive reporting and analytics tools.</p>
          </div>
        </div>

        <div class="stats-section">
          <div class="stat-card">
            <div class="stat-number">1000+</div>
            <div class="stat-label">Active Students</div>
          </div>
          <div class="stat-card">
            <div class="stat-number">50+</div>
            <div class="stat-label">Partner Institutions</div>
          </div>
          <div class="stat-card">
            <div class="stat-number">95%</div>
            <div class="stat-label">Success Rate</div>
          </div>
        </div>
      </section>
    </main>

    <footer>
      <div class="footer-container">
        <div class="footer-text">© 2024 WRI Education Consultancy</div>
        <div class="footer-date">{{ currentDateTime }}</div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'WriEducationConsultancy',
  data() {
    return {
      currentDateTime: '',
      dateTimeInterval: null
    }
  },
  mounted() {
    this.updateDateTime();
    this.dateTimeInterval = setInterval(this.updateDateTime, 1000);
  },
  beforeDestroy() {
    if (this.dateTimeInterval) {
      clearInterval(this.dateTimeInterval);
    }
  },
  methods: {
    updateDateTime() {
      const date = new Date();
      const dateOptions = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
      };
      
      const timeOptions = {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
      };

      const dateStr = date.toLocaleDateString('en-US', dateOptions);
      const timeStr = date.toLocaleTimeString('en-US', timeOptions);
      
      this.currentDateTime = `${dateStr} ${timeStr}`;
    }
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}

body {
  background: white;
  min-height: 100vh;
  padding-bottom: 60px; /* Space for footer */
  position: relative;
}

header {
  background: white;
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 1000;
}

.header-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.05);
}

.logo img {
  max-width: 100%;
  height: auto;
}

.nav-links {
  display: flex;
  gap: 2rem;
}

.nav-link {
  color: #2e7d32;
  text-decoration: none;
  font-weight: 550;
  font-size: large;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.nav-link:hover {
  background: #e8f5e9;
  color: #1b5e20;
}

.main-content {
  max-width: 1200px;
  margin: 120px auto 40px;
  padding: 1rem;
  width: 100%;
}

.hero-section {
  border-radius: 20px;
  text-align: center;
  margin-bottom: 2rem;
  padding: 0 1rem;
}

.hero-title {
  font-size: clamp(1.5rem, 5vw, 2.5rem);
  margin-bottom: 1.5rem;
  background: linear-gradient(120deg, #2e7d32, #4caf50);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  line-height: 1.2;
}

.hero-text {
  font-size: clamp(0.9rem, 2vw, 1rem);
  max-width: 800px;
  margin: 0 auto;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  margin: 3rem auto;
  padding: 0 1rem;
  max-width: 1200px;
}

.feature-card {
  background: white;
  padding: 2rem;
  border-radius: 15px;
  text-align: center;
  transition: transform 0.3s ease;
  border: 1px solid rgba(76, 175, 80, 0.1);
  height: 100%;
}

.feature-card:hover {
  transform: translateY(-5px);
  border-color: #4caf50;
}

.feature-icon {
  font-size: 2.5rem;
  color: #4caf50;
  margin-bottom: 1rem;
}

.feature-title {
  color: #2e7d32;
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
}

.feature-text {
  color: #666;
  font-size: 0.9rem;
  line-height: 1.6;
}

.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  margin: 3rem auto;
  padding: 0 1rem;
  max-width: 1200px;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(76, 175, 80, 0.1);
  border: 1px solid rgba(76, 175, 80, 0.1);
  height: 100%;
}

.stat-number {
  font-size: clamp(1.5rem, 4vw, 2rem);
  font-weight: 700;
  color: #4caf50;
  margin-bottom: 0.5rem;
}

.stat-label {
  color: #666;
  font-size: clamp(0.8rem, 2vw, 0.9rem);
}

footer {
  background: linear-gradient(135deg, #4caf50, #2e7d32);
  color: white;
  padding: 1rem 0;
  position: fixed;
  bottom: 0;
  width: 100%;
}

.footer-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.footer-text, .footer-date {
  font-size: clamp(0.7rem, 2vw, 0.9rem);
}

/* Media Queries */
@media (max-width: 1024px) {
  .main-content {
    margin-top: 100px;
  }

  .features-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }
}

@media (max-width: 768px) {
  .header-container {
    padding: 0.8rem;
  }

  .nav-links {
    gap: 1rem;
  }

  .nav-link {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
  }

  .footer-container {
    flex-direction: column;
    gap: 0.5rem;
    text-align: center;
    padding: 0.5rem 1rem;
  }
}

@media (max-width: 480px) {
  .logo img {
    width: 80px;
  }

  .nav-link {
    padding: 0.3rem 0.6rem;
    font-size: 0.8rem;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .stats-section {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .feature-card {
    padding: 1.5rem;
  }
}

@media (max-width: 360px) {
  .nav-links {
    gap: 0.5rem;
  }

  .nav-link {
    padding: 0.3rem 0.5rem;
    font-size: 0.75rem;
  }
}

/* For very small devices */
@media (max-width: 320px) {
  .header-container {
    flex-direction: column;
    gap: 1rem;
  }

  .nav-links {
    justify-content: center;
  }
}
</style>
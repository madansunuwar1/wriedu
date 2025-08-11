import { createRouter, createWebHistory } from "vue-router";
import Login from "../js/components/Auth/Login.vue";
import Register from "../js/components/Auth/register.vue";
import Welcome from "../js/components/Auth/Welcome.vue";
import VerifyEmail from "../js/components/Auth/VerifyEmail.vue";
import Calendar from "../js/components/calendar/Calendar.vue"

const routes = [
{
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { 
      requiresAuth: false,
  
    }
  },
{
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { 
      requiresAuth: false,
  
    }
  },

  {
    path: '/',
    name: 'Welcome',
    component: Welcome,
    meta: { 
      requiresAuth: false,
  
    }
  },
  {
    path: '/verify-email',
    name: 'VerifyEmail',
    component: VerifyEmail,
    meta: { 
      requiresAuth: false,
  
    }
  },
  ];

const router = createRouter({
    history: createWebHistory(),
    routes,
});


export default router;
import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        roles: [],
        permissions: [],
    }),
    getters: {
        isAuthenticated: (state) => !!state.user,
        userName: (state) => state.user ? `${state.user.name} ${state.user.last || ''}`.trim() : 'Guest',
        userRole: (state) => state.roles.length > 0 ? state.roles[0] : 'User',
        userAvatar: (state) => state.user?.avatar ? `/storage/avatars/${state.user.avatar}` : '/assets/images/profile/user-1.jpg',
    },
    actions: {
        /**
         * Initializes the store from the global window object.
         * This action is called once in app.js when the app starts.
         * @param {object} initialData - The auth_user object from the Blade template.
         */
        init(initialData) {
            if (initialData && initialData.user) {
                this.user = initialData.user;
                // This is the more defensive version that handles both arrays and objects
                this.roles = Array.isArray(initialData.roles) ? initialData.roles : Object.values(initialData.roles || {});
                this.permissions = Array.isArray(initialData.permissions) ? initialData.permissions : Object.values(initialData.permissions || {});
            }
        },
        
        /**
         * Handles user logout.
         */
        async logout() {
            // Post to Laravel's standard logout route
            await axios.post('/logout');
            
            // Clear the local state
            this.user = null;
            this.roles = [];
            this.permissions = [];
            
            // Redirect to the login page.
            window.location.href = '/login';
        },

        // --- Helper functions for checking permissions in components ---
        hasPermission(permission) {
            return this.permissions.includes(permission);
        },
        hasRole(roles) {
            // Ensure this.roles is always an array before calling .some()
            if (!Array.isArray(this.roles)) return false;
            
            const requiredRoles = Array.isArray(roles) ? roles : [roles];
            return this.roles.some(role => requiredRoles.includes(role));
        },
        
        // --- Convenience wrappers from your Blade file's @can/@if checks ---
        canViewApplications() { return this.hasPermission('view_applications'); },
        canViewLeads() { return this.hasPermission('view_leads'); },
        canViewEnquiries() { return this.hasPermission('view_enquiries'); },
        canViewDataEntries() { return this.hasPermission('view_data_entries'); },
        canViewFinances() { return this.hasPermission('view_finances'); },
        canAccessRawLeads() { return this.hasRole(['Administrator', 'Manager', 'Leads Manager']); },
        canAccessSettings() { return this.hasRole(['Administrator', 'Manager', 'Leads Manager', 'Applications Manager']); },
    },
});
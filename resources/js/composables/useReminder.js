// resources/js/composables/useReminders.js

import { ref, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import axios from 'axios';

export function useReminders(initialReminders = []) {
    const router = useRouter();
    // A reactive array of reminders. We filter out any that have already been shown.
    const reminders = ref(initialReminders.filter(r => !r.shown));
    let checkInterval = null;

    const checkReminders = () => {
        const now = new Date();
        const dueReminders = [];

        // Find all due reminders
        reminders.value.forEach(reminder => {
            if (new Date(reminder.date_time) <= now) {
                dueReminders.push(reminder);
            }
        });
        
        // Remove due reminders from the main list to prevent re-triggering
        reminders.value = reminders.value.filter(r => new Date(r.date_time) > now);

        // Show a popup for each one
        dueReminders.forEach(showReminderPopup);
    };

    const showReminderPopup = (reminder) => {
        Swal.fire({
            title: 'Lead Follow-up Reminder',
            html: `Time to follow up with <strong>${reminder.lead.name}</strong>.<br><small>Note: ${reminder.comment}</small>`,
            icon: 'info',
            showDenyButton: true,
            confirmButtonText: 'View Lead',
            denyButtonText: 'Snooze (15 min)',
            showCancelButton: true,
            cancelButtonText: 'Dismiss',
            customClass: {
                denyButton: 'btn btn-warning',
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                router.push({ name: 'leads.record', params: { leadId: reminder.lead_id } });
            } else if (result.isDenied) {
                // Snooze functionality
                try {
                    await axios.post(`/api/v1/comments/${reminder.id}/snooze`, { snooze_minutes: 15 });
                    Swal.fire('Snoozed!', 'We will remind you again in 15 minutes.', 'success');
                    // No need to add it back to the list, the user can refresh the page to get the updated list of reminders.
                } catch (error) {
                    Swal.fire('Error', 'Could not snooze the reminder.', 'error');
                }
            }
        });
    };

    const start = () => {
        if (!checkInterval) {
            // Check for reminders every 30 seconds
            checkInterval = setInterval(checkReminders, 30000); 
        }
    };

    const stop = () => {
        clearInterval(checkInterval);
        checkInterval = null;
    };

    onUnmounted(stop);

    return { start, stop };
}
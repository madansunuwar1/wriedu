<template>
    <div class="main-container">
        <div id="financeContentArea" class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div class="mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ti ti-database text-2xl text-[#2e7d32] me-2"></i>
                            <div class="text-[24px] text-[#2e7d32] font-semibold font-g">
                                {{ pageTitle }}
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn" :class="{ active: view === 'receivable' }" @click="setView('receivable')">
                                <i class="ti ti-chart-bar me-1"></i> Accounts Receivable
                            </button>
                            <button type="button" class="btn" :class="{ active: view === 'payable' }" @click="setView('payable')">
                                <i class="ti ti-moneybag me-1"></i> Commission Payable
                            </button>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="position-relative" style="width: 250px;">
                            <input type="text" class="form-control product-search ps-5" v-model="searchTerm" placeholder="Search this table...">
                            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                        </div>
                        <a v-if="view === 'receivable'" href="/backend/Finance/accountreceivable" class="icon-btn btn-success">
                            <i class="ti ti-plus fs-5"></i><span class="tooltip-text">Create A/R</span>
                        </a>
                        <a v-else href="/backend/comission/payable/comission" class="icon-btn btn-success">
                            <i class="ti ti-plus fs-5"></i><span class="tooltip-text">Create A/P</span>
                        </a>
                        <button class="icon-btn btn-outline-primary" data-bs-toggle="modal" :data-bs-target="view === 'receivable' ? '#receivableImportModal' : '#payableImportModal'">
                            <i class="ti ti-file-import fs-5"></i><span class="tooltip-text">Import</span>
                        </button>
                        <button @click="exportToExcel" class="icon-btn btn-outline-primary">
                            <i class="ti ti-file-export fs-5"></i><span class="tooltip-text">Export</span>
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="isLoading" class="text-center py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div v-else class="card border-0 shadow-sm mt-4">
                <div v-if="view === 'receivable'" class="table-responsive">
                    <table class="table table-hover text-nowrap mb-0 align-middle" ref="receivableDataTable">
                        <thead class="table-light">
                            <tr>
                                <th><div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Student</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-calendar-time me-1"></i><span>Intake</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-file-dollar me-1"></i><span>Total Fees Paid</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-percentage me-1"></i><span>Commission</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="bi bi-gift me-1"></i><span>Bonus</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="bi bi-star me-1"></i><span>Incentive</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-wallet me-1"></i><span>Total (USD)</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-arrows-exchange me-1"></i><span>Ex. Rate</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-cash-banknote me-1"></i><span>Paid (NPR)</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-cash me-1"></i><span>Balance (NPR)</span></div></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="filteredApplications.length > 0">
                                <tr v-for="app in filteredApplications" :key="app.id" class="clickable-row" @click="navigateToDetail(app, $event)">
                                    <td>
                                        <h6 class="fw-semibold mb-1">{{ app.name || 'N/A' }}</h6>
                                        <small class="text-muted d-block">{{ app.university || 'N/A' }}</small>
                                        <small class="text-muted d-block">{{ app.english || 'N/A' }}</small>
                                    </td>
                                    <td>{{ app.intake || 'N/A' }}</td>
                                    <td>${{ formatNumber(app.fee || 0) }}</td>
                                    <td>{{ app.displayedCommissionValueString || 'N/A' }}</td>
                                    <td><span class="badge-status" :class="app.displayedBonus > 0 ? 'paid' : 'default'">{{ app.displayedBonus > 0 ? 'Yes' : 'No' }}</span></td>
                                    <td><span class="badge-status" :class="app.displayedIncentive > 0 ? 'paid' : 'default'">{{ app.displayedIncentive > 0 ? 'Yes' : 'No' }}</span></td>
                                    <td>${{ formatNumber(app.totalValueUSD || 0) }}</td>
                                    <td>
                                        <div v-if="app.commission_transaction && app.commission_transaction.type === 'receivable'" class="editable-field" @click.stop>
                                            <div v-if="isEditing(app.id, 'exchange_rate')" class="edit-input">
                                                <input type="number" :value="app.displayedExchangeRate" @blur="saveField(app, 'exchange_rate', $event.target.value)" @keydown.enter.prevent="saveField(app, 'exchange_rate', $event.target.value)" @keydown.esc.prevent="cancelEditing" class="edit-field-input" step="0.01" ref="editInput" />
                                            </div>
                                            <span v-else class="display-value" @click="startEditing(app, 'exchange_rate')">
                                                Npr. {{ formatNumber(app.displayedExchangeRate || 1) }}
                                                <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                                            </span>
                                        </div>
                                        <span v-else>-</span>
                                    </td>
                                    <td>
                                        <div v-if="app.commission_transaction && app.commission_transaction.type === 'receivable'" class="editable-field" @click.stop>
                                            <div v-if="isEditing(app.id, 'paid')" class="edit-input">
                                                <input type="number" :value="app.displayedPaid" @blur="saveField(app, 'paid', $event.target.value)" @keydown.enter.prevent="saveField(app, 'paid', $event.target.value)" @keydown.esc.prevent="cancelEditing" class="edit-field-input" step="0.01" ref="editInput" />
                                            </div>
                                            <span v-else class="display-value" @click="startEditing(app, 'paid')">
                                                Npr. {{ formatNumber(app.displayedPaid || 0) }}
                                                <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                                            </span>
                                        </div>
                                        <span v-else>-</span>
                                    </td>
                                    <td><span :style="{ color: app.balanceDueNPR > 0 ? '#dc3545' : 'inherit', fontWeight: app.balanceDueNPR > 0 ? 'bold' : 'normal' }">Npr. {{ formatNumber(app.balanceDueNPR || 0) }}</span></td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="10" class="text-center py-4">No Accounts Receivable data available.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="view === 'payable'" class="table-responsive">
                    <table class="table table-hover text-nowrap mb-0 align-middle" ref="payableDataTable">
                        <thead class="table-light">
                            <tr>
                                <th><div class="d-flex align-items-center"><i class="ti ti-user me-1"></i><span>Student</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-calendar-time me-1"></i><span>Intake</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-file-dollar me-1"></i><span>Total Fees Paid</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-percentage me-1"></i><span>Commission</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="bi bi-gift me-1"></i><span>Bonus</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="bi bi-star me-1"></i><span>Incentive</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="bi bi-star me-1"></i><span>Agency Name</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-wallet me-1"></i><span>Total (USD)</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-arrows-exchange me-1"></i><span>Ex. Rate</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-cash-banknote me-1"></i><span>Paid (NPR)</span></div></th>
                                <th><div class="d-flex align-items-center"><i class="ti ti-cash me-1"></i><span>Balance (NPR)</span></div></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="filteredApplications.length > 0">
                                <tr v-for="app in filteredApplications" :key="app.id" class="clickable-row" @click="navigateToDetail(app, $event)">
                                    <td>
                                        <h6 class="fw-semibold mb-1">{{ app.name || 'N/A' }}</h6>
                                        <small class="text-muted d-block">{{ app.university || 'N/A' }}</small>
                                        <small class="text-muted d-block">{{ app.english || 'N/A' }}</small>
                                    </td>
                                    <td>{{ app.intake || 'N/A' }}</td>
                                    <td>${{ formatNumber(app.fee || 0) }}</td>
                                    <td>{{ app.displayedCommissionValueString || 'N/A' }}</td>
                                    <td><span class="badge-status" :class="app.displayedBonus > 0 ? 'paid' : 'default'">{{ app.displayedBonus > 0 ? 'Yes' : 'No' }}</span></td>
                                    <td><span class="badge-status" :class="app.displayedIncentive > 0 ? 'paid' : 'default'">{{ app.displayedIncentive > 0 ? 'Yes' : 'No' }}</span></td>
                                    <td>{{ app.partner ? app.partner.agency_name : 'N/A' }}</td>
                                    <td>${{ formatNumber(app.totalValueUSD || 0) }}</td>
                                    <td>
                                        <div v-if="app.commission_transaction && app.commission_transaction.type === 'payable'" class="editable-field" @click.stop>
                                            <div v-if="isEditing(app.id, 'exchange_rate')" class="edit-input">
                                                <input type="number" :value="app.displayedExchangeRate" @blur="saveField(app, 'exchange_rate', $event.target.value)" @keydown.enter.prevent="saveField(app, 'exchange_rate', $event.target.value)" @keydown.esc.prevent="cancelEditing" class="edit-field-input" step="0.01" ref="editInput" />
                                            </div>
                                            <span v-else class="display-value" @click="startEditing(app, 'exchange_rate')">
                                                Npr. {{ formatNumber(app.displayedExchangeRate || 1) }}
                                                <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                                            </span>
                                        </div>
                                        <span v-else>-</span>
                                    </td>
                                    <td>
                                        <div v-if="app.commission_transaction && app.commission_transaction.type === 'payable' && app.displayedPaid > 0">
                                            <span class="badge-status" :class="getPayableStatusClass(app)" :title="`Amount: Npr. ${formatNumber(app.displayedPaid)}`">
                                                {{ getPayableStatusText(app) }}
                                            </span>
                                        </div>
                                        <span v-else>-</span>
                                    </td>
                                    <td><span :style="{ color: app.balanceDueNPR > 0 ? '#dc3545' : 'inherit', fontWeight: app.balanceDueNPR > 0 ? 'bold' : 'normal' }">Npr. {{ formatNumber(app.balanceDueNPR || 0) }}</span></td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="11" class="text-center py-4">No Commission Payable data available.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import * as XLSX from 'xlsx';

export default {
    name: 'FinanceComponent',
    data() {
        return {
            view: 'receivable',
            applications: [],
            isLoading: true,
            searchTerm: '',
            editingCell: {
                appId: null,
                field: null,
                originalValue: null,
            },
        };
    },
    computed: {
        pageTitle() {
            return this.view === 'receivable' ? 'Accounts Receivable' : 'Commission Payable';
        },
        filteredApplications() {
            if (!this.searchTerm) {
                return this.applications;
            }
            const lowerCaseSearch = this.searchTerm.toLowerCase();
            return this.applications.filter(app => {
                return (app.name && app.name.toLowerCase().includes(lowerCaseSearch)) ||
                       (app.university && app.university.toLowerCase().includes(lowerCaseSearch)) ||
                       (app.english && app.english.toLowerCase().includes(lowerCaseSearch));
            });
        },
    },
    methods: {
        async fetchData() {
            this.isLoading = true;
            this.applications = [];
            try {
                const response = await axios.get('/api/finance', {
                    params: { view: this.view }
                });
                this.applications = response.data.applications;
            } catch (error) {
                console.error("Error fetching finance data:", error);
                Swal.fire('Error', 'Could not fetch data from the server.', 'error');
            } finally {
                this.isLoading = false;
            }
        },
        setView(newView) {
            if (this.view !== newView) {
                this.view = newView;
                this.searchTerm = '';
                this.fetchData();
            }
        },
        formatNumber(value, decimals = 2) {
            const num = parseFloat(value);
            if (isNaN(num)) return '0.00';
            return num.toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        },
        navigateToDetail(app, event) {
            if (event.target.closest('.editable-field')) {
                return;
            }
            const viewType = this.view === 'receivable' ? 'accountreceivableview' : 'accountpayableview';
            window.location.href = `/backend/Finance/${viewType}/${app.id}`;
        },
        isEditing(appId, field) {
            return this.editingCell.appId === appId && this.editingCell.field === field;
        },
        startEditing(app, field) {
            this.editingCell.appId = app.id;
            this.editingCell.field = field;
            this.editingCell.originalValue = field === 'exchange_rate' ? app.displayedExchangeRate : app.displayedPaid;
            this.$nextTick(() => {
                const input = this.$refs.editInput[0];
                if (input) {
                    input.focus();
                    input.select();
                }
            });
        },
        cancelEditing() {
            this.editingCell.appId = null;
            this.editingCell.field = null;
            this.editingCell.originalValue = null;
        },
        async saveField(app, field, value) {
            const numericValue = parseFloat(value);
            if (isNaN(numericValue) || numericValue < 0) {
                Swal.fire('Invalid Input', 'Please enter a valid non-negative number.', 'warning');
                this.cancelEditing();
                return;
            }

            if (numericValue === this.editingCell.originalValue) {
                this.cancelEditing();
                return;
            }

            this.cancelEditing();

            Swal.fire({
                title: 'Saving...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading(),
            });

            try {
                const formData = new FormData();
                formData.append('field', field);
                formData.append('value', numericValue);
                formData.append('type', this.view);
                formData.append('_method', 'PUT');
                
                const response = await axios.post(`/backend/finance/transaction/update/${app.id}`, formData);

                if (response.data.success) {
                    Swal.fire('Saved!', 'The record has been updated.', 'success');
                    this.fetchData();
                } else {
                    throw new Error(response.data.message || 'Failed to save data.');
                }
            } catch (error) {
                console.error("Error saving field:", error);
                const errorMessage = error.response?.data?.message || error.message || "An unknown error occurred.";
                Swal.fire('Error', `Could not save changes: ${errorMessage}`, 'error');
            }
        },
        getPayableStatusClass(app) {
            const status = app.commission_transaction?.status?.toLowerCase() || 'default';
            return ['paid', 'pending', 'failed'].includes(status) ? status : 'default';
        },
        getPayableStatusText(app) {
            const status = app.commission_transaction?.status || 'Default';
            return status.charAt(0).toUpperCase() + status.slice(1);
        },
        exportToExcel() {
            const table = this.view === 'receivable' ? this.$refs.receivableDataTable : this.$refs.payableDataTable;
            if (!table) return;
            
            const wb = XLSX.utils.table_to_book(table, { sheet: "Finance Data" });
            XLSX.writeFile(wb, `finance_data_${this.view}.xlsx`);
        }
    },
    watch: {
        view(newView) {
            this.fetchData();
        }
    },
    mounted() {
        this.fetchData();
        // Add CSRF token for all axios requests
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
};
</script>

<style>
    .main-container {  background-color: #f8f9fa; }
    .icon-btn { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 8px; border: 1px solid #dee2e6; background: white; color: #6c757d; text-decoration: none; transition: all 0.2s ease; cursor: pointer; }
    .icon-btn:hover { background: #f8f9fa; border-color: #adb5bd; color: #495057; transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
    .icon-btn.btn-success:hover { background: #198754; border-color: #198754; color: white; }
    .icon-btn.btn-primary:hover { background: #0d6efd; border-color: #0d6efd; color: white; }
    .icon-btn.btn-outline-primary:hover { background: #0d6efd; border-color: #0d6efd; color: white; }
    .tooltip-text { position: absolute; bottom: -35px; left: 50%; transform: translateX(-50%); background: #333; color: white; padding: 6px 12px; border-radius: 4px; font-size: 12px; white-space: nowrap; opacity: 0; visibility: hidden; transition: all 0.2s ease; z-index: 1000; }
    .icon-btn:hover .tooltip-text { opacity: 1; visibility: visible; }
    .table-hover tbody tr:hover { background-color: rgba(46, 125, 50, 0.08); }
    .btn-group .btn { transition: all 0.2s ease; border-color: #2e7d32; }
    .btn-group .btn:not(.active) { background-color: #fff; color: #495057; }
    .btn-group .btn.active { background-color: #2e7d32; color: white; border-color: #2e7d32; }
    .editable-field { position: relative; display: inline-block; cursor: pointer; }
    .edit-btn { background: none; border: none; cursor: pointer; color: #6c757d; padding: 3px 5px; margin-left: 5px; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s; }
    .editable-field:hover .edit-btn { opacity: 1; visibility: visible; }
    .edit-input input { padding: 5px 8px; border: 1px solid #ced4da; border-radius: 4px; width: 120px; }
    .clickable-row { cursor: pointer; }
    .badge-status { padding: 0.35em 0.65em; font-size: .75em; font-weight: 700; line-height: 1; color: #fff; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: .25rem; }
    .badge-status.paid { background-color: #28a745; }
    .badge-status.pending { background-color: #ffc107; color: #000; }
    .badge-status.failed { background-color: #dc3545; }
    .badge-status.default { background-color: #6c757d; }
</style>
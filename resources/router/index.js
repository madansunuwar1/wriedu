import { createRouter, createWebHistory } from "vue-router";
import ApplicationList from "../js/components/application/ApplicationList.vue";
import RecordSection from "../js/components/application/RecordSection.vue";
import EnquiryIndex from "../js/components/enquiry/EnquiryIndex.vue";
import EnquiryRecord from "../js/components/enquiry/EnquiryRecord.vue";
import Admin from "../js/components/Admin.vue";
import NoticeIndex from "../js/components/notice/NoticeIndex.vue";
import NoticeRecord from "../js/components/notice/NoticeRecord.vue";
import NoticeCreate from "../js/components/notice/NoticeCreate.vue";
import Calendar from "../js/components/calendar/Calendar.vue";
import Comment from "../js/components/application/Comment.vue";
import Status from "../js/components/product/status/Status.vue";
import Productindex from "../js/components/product/Product.vue";
import Counselor from "../js/components/product/counselor/Counselor.vue";
import UniversityImage from "../js/components/product/university/UniversityImage.vue";
import FinanceIndex from "../js/components/finance/FinanceIndex.vue";
import RawLeadTable from "../js/components/Leads/RawLeadTable.vue";
import RawLeadImport from "../js/components/Leads/RawLeadImport.vue";
import LeadIndex from "../js/components/Leads/LeadIndex.vue";
import LeadRecord from "../js/components/Leads/LeadRecord.vue";
import LeadCreate from "../js/components/Leads/LeadCreate.vue";
import UniversityCreate from "../js/components/University/UniversityCreate.vue";
import UniversityDataTable from "../js/components/University/UniversityDataTable.vue";
import UniversityProfile from "../js/components/University/UniversityProfile.vue";
import CourseFinder from "../js/components/University/CourseFinder.vue";
import Dashboard from "../js/components/Dashboard.vue";
import ActivityRecord from "../js/components/Activity/ActivityRecord.vue";
import ActivityIndex from "../js/components/Activity/ActivityIndex.vue";
import UserIndex from "../js/components/User/UserIndex.vue";
import Profile from "../js/components/User/Profile.vue";
import EnquiryCreate from "../js/components/enquiry/EnquiryCreate.vue";
import ChatIndex from "../js/components/chat/ChatIndex.vue";
import PayableIndex from "../js/components/finance/PayableIndex.vue";
import PayableCreate from "../js/components/finance/PayableCreate.vue";
import ReceivableCreate from "../js/components/finance/ReceivableCreate.vue";
import ReceivableIndex from "../js/components/finance/ReceivableIndex.vue";
import ReceivableView from "../js/components/finance/ReceivableView.vue";
import PartnerIndex from "../js/components/partner/PartnerIndex.vue";
import PartnerCreate from "../js/components/partner/PartnerCreate.vue";
import RoleIndex from "../js/components/Roles/RoleIndex.vue";
import RolesShow from "../js/components/Roles/RolesShow.vue";
import RoleCreate from "../js/components/Roles/RoleCreate.vue";
import ApplicationCreate from "../js/components/application/ApplicationCreate.vue";
import Commissionindex from "../js/components/comission/commissionindex.vue";

const routes = [
    {
        path: "/",
        name: "Home",
        redirect: "/app",
    },
    {
        path: "/app/applications",
        name: "ApplicationList",
        component: ApplicationList,
    },
    {
        path: "/applications/create",
        name: "ApplicationCreate",
        component: ApplicationCreate,
    },
    {
        path: "/app/applications/record/:id/:type?",
        name: "RecordSection",
        component: RecordSection,
        props: true,
    },
    {
        path: "/app/enquiries",
        name: "EnquiryIndex",
        component: EnquiryIndex,
    },
    {
        path: "/app/enquiries/create",
        name: "EnquiryCreate",
        component: EnquiryCreate,
    },
    {
        path: "/app/enquiries/record/:id",
        name: "EnquiryRecord",
        component: EnquiryRecord,
        props: true,
    },
    {
        path: "/app/notices",
        name: "NoticeIndex",
        component: NoticeIndex,
    },
    {
        path: "/app/notice/create",
        name: "NoticeCreate",
        component: NoticeCreate,
    },
    {
        path: "/app/notices/:id",
        name: "NoticeRecord",
        component: NoticeRecord,
        props: true,
    },
    {
        path: "/app/calendar",
        name: "Calendar",
        component: Calendar,
    },
    {
        path: "/app/counselor",
        name: "Counselor",
        component: Counselor,
    },
    {
        path: "/app/image",
        name: "UniversityImage",
        component: UniversityImage,
    },

    {
        path: "/app/status",
        name: "Status",
        component: Status,
    },
    {
        path: "/app/comment",
        name: "Comment",
        component: Comment,
    },

    {
        path: "/app/product",
        name: "Product",
        component: Productindex,
    },
    {
        path: "/app/finance",
        name: "FinanceIndex",
        component: FinanceIndex,
    },
    {
        path: "/app/payable",
        name: "PayableIndex",
        component: PayableIndex,
    },
    {
        path: "/app/payable/create",
        name: "PayableCreate",
        component: PayableCreate,
    },
    {
        path: "/app/receivable/create",
        name: "ReceivableCreate",
        component: ReceivableCreate,
    },
    {
        path: "/app/receivable",
        name: "ReceivableIndex",
        component: ReceivableIndex,
    },
    {
        path: "/app/receivableview/:id",
        name: "ReceivableView",
        component: ReceivableView,
        props: true,
    },
    {
        path: "/app/rawlead",
        name: "leads.raw",
        component: RawLeadTable,
    },
    {
        path: "/app/leadform/indexs",
        name: "leads.index",
        component: LeadIndex,
    },
    {
        path: "/app/leadform/records/:leadId",
        name: "leads.record",
        component: LeadRecord,
        props: true,
    },
    {
        path: "/app/leadform/create",
        name: "leads.create",
        component: LeadCreate,
    },
    {
        path: "/app/rawlead/import",
        name: "leads.rawimport",
        component: RawLeadImport,
    },
    {
        path: "/app/universitytable",
        name: "universit.index",
        component: UniversityDataTable,
    },
    {
        path: "/app/uniprofile/:id",
        name: "universit.profile",
        component: UniversityProfile,
        props: true,
    },
    {
        path: "/app/unicreate",
        name: "university.create",
        component: UniversityCreate,
    },
    {
        path: "/app/dashboard",
        name: "dashboard.indexs",
        component: Dashboard,
    },
    {
        path: "/app/course-finder",
        name: "course.finder",
        component: CourseFinder,
    },
    {
        path: "/app/activity-logs",
        name: "activity.index",
        component: ActivityIndex,
    },
    {
        path: "/app/activity-logs/:userId",
        name: "activity.record",
        component: ActivityRecord,
    },
    {
        path: "/app/user-list",
        name: "user.index",
        component: UserIndex,
    },
    {
        path: "/app/user-profile/:id",
        name: "user.profile",
        component: Profile,
        props: true,
    },
    {
        path: "/app/user-profile",
        name: "auth.user.profile",
        component: Profile,
    },
    {
        path: "/app/admin",
        name: "Admin",
        component: Admin,
    },
    {
        path: "/app/chat",
        name: "chat",
        component: ChatIndex,
    },
    {
        path: "/app/partners",
        name: "partnerindex",
        component: PartnerIndex,
    },
    {
        path: "/app/partners/create",
        name: "partnercreate",
        component: PartnerCreate,
    },
    {
        path: "/app/roles",
        name: "RoleIndex",
        component: RoleIndex,
    },
    {
        path: "/app/roles/:id/edit",
        name: "RoleShow",
        component: RolesShow,
        props: true,
    },
    {
        path: "/app/roles/create",
        name: "RoleCreate",
        component: RoleCreate,
    },
     {
        path: "/app/commission",
        name: "commissionindex",
        component: Commissionindex,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;

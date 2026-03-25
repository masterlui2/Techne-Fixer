import { createRouter, createWebHistory } from "vue-router";

// Public
import Home from '@/views/Public/HomePage.vue';
import Services from '@/views/Public/ServicesPage.vue';
import Portfolio from '@/views/Public/PortfolioPage.vue';
import About from '@/views/Public/AboutPage.vue';

// Auth
import Login from '@/views/Auth/Login.vue';
import Register from "@/views/Auth/Register.vue";
import Forgot from "@/views/Auth/Forgot.vue";

// Admin
import Dashboard from "@/views/Manager/Dashboard.vue";
import AdminServices from "@/views/Manager/WebsiteContent/Service/Index.vue";
import AdminServicesCreate from "@/views/Manager/WebsiteContent/Service/Create.vue";
import AdminServicesShow from "@/views/Manager/WebsiteContent/Service/Show.vue";
import AdminServicesEdit from "@/views/Manager/WebsiteContent/Service/Edit.vue";
import PortfolioIndex from "@/views/Manager/WebsiteContent/Portfolio/Index.vue";
import PortfolioCreate from "@/views/Manager/WebsiteContent/Portfolio/Create.vue";
import PortfolioShow from "@/views/Manager/WebsiteContent/Portfolio/Show.vue";
import PortfolioEdit from "@/views/Manager/WebsiteContent/Portfolio/Edit.vue";
import Inquire from "@/views/Customer/Inquire.vue";
import InquireIndex from "@/views/Manager/Inquiry/Index.vue";
import QuotationIndex from "@/views/Manager/Quotation/Index.vue";
import JobOrderIndex from "@/views/Manager/JobOrder/Index.vue";
import TechnicianIndex from "@/views/Manager/Technician/Index.vue";
import CustomerIndex from "@/views/Manager/Customer/Index.vue";
import ReportsIndex from "@/views/Manager/Report/Index.vue";

// Technician
import TechnicianDashboard from "@/views/Technician/Dashboard/Index.vue";
// import TechnicianJobOrderIndex from "@/views/Technician/JobOrder/Index.vue";
// import TechnicianJobOrderShow from "@/views/Technician/JobOrder/Show.vue";
// import TechnicianInquiryIndex from "@/views/Technician/Inquiry/Index.vue";
// import TechnicianInquiryShow from "@/views/Technician/Inquiry/Show.vue";
// import TechnicianQuotationIndex from "@/views/Technician/Quotation/Index.vue";
// import TechnicianQuotationCreate from "@/views/Technician/Quotation/Create.vue";
// import TechnicianQuotationShow from "@/views/Technician/Quotation/Show.vue";
// import TechnicianProfile from "@/views/Technician/Profile.vue";

const routes = [
  // Public routes
  { path: "/", name: "Home", component: Home },
  { path: "/services", name: "Services", component: Services },
  { path: "/portfolio", name: "Portfolio", component: Portfolio },
  { path: "/about", name: "About", component: About },
  { path: "/inquire", name: "Inquire", component: Inquire, meta: { hideNavbar: true } },

  // Auth routes
  {
    path: "/auth",
    meta: { guestOnly: true },
    children: [
      { path: "login", name: "Login", component: Login },
      { path: "register", name: "Register", component: Register },
      { path: "forgot-password", name: "Forgot", component: Forgot },
    ]
  },

  // Admin routes
  {
    path: "/admin",
    meta: { requiresAdmin: true },
    children: [
      { path: "", redirect: "/admin/dashboard" },
      { path: "dashboard", name: "Dashboard", component: Dashboard },
      {
        path: "services",
        children: [
          { path: "", name: "AdminServices", component: AdminServices },
          { path: "create", name: "AdminServicesCreate", component: AdminServicesCreate },
          { path: ":id", name: "AdminServicesShow", component: AdminServicesShow },
          { path: ":id/edit", name: "AdminServicesEdit", component: AdminServicesEdit },
        ]
      },
      {
        path: 'portfolio', children: [
          { path: '', name: 'AdminPortfolio', component: PortfolioIndex },
          { path: 'create', name: 'AdminPortfolioCreate', component: PortfolioCreate },
          { path: ':id', name: 'AdminPortfolioShow', component: PortfolioShow },
          { path: ':id/edit', name: 'AdminPortfolioEdit', component: PortfolioEdit },
        ]
      },
      {
        path: 'inquiry', children: [
          { path: '', name: 'AdminInquiry', component: InquireIndex },
        ]
      },
      {
        path: 'quotation', children: [
          { path: '', name: 'AdminQuotation', component: QuotationIndex },
        ]
      },
      {
        path: 'job-order', children: [
          { path: '', name: 'AdminJob', component: JobOrderIndex },
        ]
      },
      {
        path: 'technician', children: [
          { path: '', name: 'AdminTechnician', component: TechnicianIndex },
        ]
      },
      {
        path: 'customer', children: [
          { path: '', name: 'AdminCustomer', component: CustomerIndex },
        ]
      },
      {
        path: 'report', children: [
          { path: '', name: 'AdminReport', component: ReportsIndex },
        ]
      },
    ]
  },

  // Technician routes
  {
    path: "/technician",
    meta: { requiresTechnician: true },
    children: [
      { path: "", redirect: "/technician/dashboard" },
      { path: "dashboard", name: "TechnicianDashboard", component: TechnicianDashboard },
      // {
      //   path: "job-orders",
      //   children: [
      //     { path: "", name: "TechnicianJobOrders", component: TechnicianJobOrderIndex },
      //     { path: ":id", name: "TechnicianJobOrderShow", component: TechnicianJobOrderShow },
      //   ]
      // },
      // {
      //   path: "inquiries",
      //   children: [
      //     { path: "", name: "TechnicianInquiries", component: TechnicianInquiryIndex },
      //     { path: ":id", name: "TechnicianInquiryShow", component: TechnicianInquiryShow },
      //   ]
      // },
      // {
      //   path: "quotations",
      //   children: [
      //     { path: "", name: "TechnicianQuotations", component: TechnicianQuotationIndex },
      //     { path: "create", name: "TechnicianQuotationCreate", component: TechnicianQuotationCreate },
      //     { path: ":id", name: "TechnicianQuotationShow", component: TechnicianQuotationShow },
      //   ]
      // },
      // { path: "profile", name: "TechnicianProfile", component: TechnicianProfile },
    ]
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  },
});

// ── Helpers ───────────────────────────────────────────────────────────────────

const getUserRole = () => {
  try {
    const user = JSON.parse(localStorage.getItem('user'));
    return user?.role ?? null;
  } catch {
    return null;
  }
};

const redirectToDashboard = (role) => {
  if (role === 'technician') return { name: 'TechnicianDashboard' };
  if (role === 'admin')      return { name: 'Dashboard' };
  return { name: 'Home' };
};

// ── Route Guard ───────────────────────────────────────────────────────────────

router.beforeEach((to) => {
  const token = localStorage.getItem('token');
  const role  = getUserRole();

  // Not logged in → block protected routes
  if ((to.meta.requiresAdmin || to.meta.requiresTechnician) && !token) {
    return { name: 'Login' };
  }

  // Logged in but wrong role → redirect to own dashboard
  if (to.meta.requiresAdmin && role !== 'admin') {
    return redirectToDashboard(role);
  }

  if (to.meta.requiresTechnician && role !== 'technician') {
    return redirectToDashboard(role);
  }

  // Already logged in → skip auth pages, go to role dashboard
  if (to.meta.guestOnly && token) {
    return redirectToDashboard(role);
  }
});

export default router;
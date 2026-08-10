import Link from "./Components/Icons/Link";
import Setting from "./Components/Icons/Setting";
import Chat from "./Components/Icons/Chat";
import ShortLink from "./Components/Icons/ShortLink";
import Dashboard from "./Components/Icons/Dashboard";
import Palette from "./Components/Icons/Palette";
import Pricing from "./Components/Icons/Pricing";
import Calendar from "./Components/Icons/Calendar";
import PaymentSettings from "./Components/Icons/PaymentSettings";
import IdCard from "./Components/Icons/IdCard";
import Projects from "./Components/Icons/Projects";
import LogOut from "./Components/Icons/LogOut";
import Users from "./Components/Icons/Users";
import QRcode from "./Components/Icons/QRcode";
import Page from "./Components/Icons/Page";

const icon = {
   className: "w-4 h-4 text-inherit",
};

export const routes = [
   {
      title: "User Panel",
      role: "USER",
      pages: [
         {
            icon: <Dashboard {...icon} />,
            name: "Dashboard",
            path: "/dashboard",
         },
         {
            icon: <Link {...icon} />,
            name: "Bio Links",
            path: "/bio-links",
         },
         {
            icon: <ShortLink {...icon} />,
            name: "Short Links",
            path: "/short-links",
         },
         {
            icon: <Projects {...icon} />,
            name: "Projects",
            path: "/projects",
         },
         {
            icon: <QRcode {...icon} />,
            name: "QR Codes",
            path: "/qrcodes",
         },
         {
            icon: <Pricing {...icon} />,
            name: "Current Plan",
            path: "/current-plan",
         },
         {
            icon: <Setting {...icon} />,
            name: "Settings",
            path: "/settings",
         },
         {
            icon: <LogOut {...icon} />,
            name: "Log Out",
            path: "/logout",
         },
      ],
   },
   {
      title: "Admin Panel",
      role: "SUPER-ADMIN",
      pages: [
         {
            icon: <Users {...icon} />,
            name: "Users",
            path: "/admin/users",
         },
         {
            icon: <IdCard {...icon} />,
            name: "Subscriptions",
            path: "/admin/subscriptions",
         },
         {
            icon: <Calendar {...icon} />,
            name: "Pricing Plans",
            path: "/admin/pricing-plans",
         },
         {
            icon: <Chat {...icon} />,
            name: "Testimonials",
            path: "/admin/testimonials",
         },
         {
            icon: <Palette {...icon} />,
            name: "Manage Themes",
            path: "/admin/manage-themes",
         },
         {
            icon: <PaymentSettings {...icon} />,
            name: "Payments Setup",
            path: "/admin/payments-setup",
         },
         {
            icon: <Page {...icon} />,
            name: "Custom Page",
            path: "/admin/custom-page",
         },
         {
            icon: <Setting {...icon} />,
            name: "App Settings",
            path: "/admin/app-settings",
         },
      ],
   },
];

export default routes;

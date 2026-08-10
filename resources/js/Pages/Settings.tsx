import { ReactNode } from "react";
import { Head } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import Breadcrumb from "@/Components/Breadcrumb";
import ProfileUpdate from "@/Components/Settings/ProfileUpdate";
import ForgetPassword from "@/Components/Settings/ForgetPassword";
import ChangePassword from "@/Components/Settings/ChangePassword";
import ChangeEmail from "@/Components/Settings/ChangeEmail";
import Setting from "@/Components/Icons/Setting";

const Settings = () => {
   return (
      <>
         <Head title="Ayarlar" />
         <Breadcrumb Icon={Setting} title="Ayarlar" />

         <ProfileUpdate />
         <ForgetPassword />
         <ChangePassword />
         <ChangeEmail />
      </>
   );
};

Settings.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Settings;

import { ReactNode } from "react";
import { Head } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import ProfileUpdate from "@/Components/Settings/ProfileUpdate";
import ForgetPassword from "@/Components/Settings/ForgetPassword";
import ChangePassword from "@/Components/Settings/ChangePassword";
import ChangeEmail from "@/Components/Settings/ChangeEmail";
import PageHeader from "@/Components/Panel/PageHeader";

const Settings = () => {
   return (
      <>
         <Head title="Ayarlar" />
         <PageHeader
            title="Ayarlar"
            description="Profil, e-posta ve şifre bilgilerinizi güncelleyin."
         />

         <div className="space-y-6">
            <ProfileUpdate />
            <ForgetPassword />
            <ChangePassword />
            <ChangeEmail />
         </div>
      </>
   );
};

Settings.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Settings;

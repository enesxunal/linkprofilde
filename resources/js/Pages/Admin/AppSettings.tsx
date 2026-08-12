import { ReactNode } from "react";
import Dashboard from "@/Layouts/Dashboard";
import { Head } from "@inertiajs/react";
import { AppSettingProps, SMTPProps, SocialLoginProps } from "@/types";
import GoogleAuthSettings from "@/Components/Forms/GoogleAuthSettings";
import SMTPSettings from "@/Components/Forms/SMTPSettings";
import AppSettingsForm from "@/Components/Forms/AppSettings";
import PageHeader from "@/Components/Panel/PageHeader";

interface Props {
   app: AppSettingProps;
   smtp: SMTPProps;
   google: SocialLoginProps;
}

const AppSettings = (props: Props) => {
   const { app, smtp, google } = props;
   return (
      <>
         <Head title="Uygulama Ayarları" />
         <PageHeader
            title="Uygulama Ayarları"
            description="Genel uygulama, SMTP ve Google giriş ayarları."
         />

         <div className="space-y-6">
            <AppSettingsForm app={app} />
            <GoogleAuthSettings google={google} />
            <SMTPSettings smtp={smtp} />
         </div>
      </>
   );
};

AppSettings.layout = (page: ReactNode) => <Dashboard children={page} />;

export default AppSettings;

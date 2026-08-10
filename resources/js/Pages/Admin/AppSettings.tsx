import { ReactNode } from "react";
import Dashboard from "@/Layouts/Dashboard";
import { Head } from "@inertiajs/react";
import Breadcrumb from "@/Components/Breadcrumb";
import Setting from "@/Components/Icons/Setting";
import { AppSettingProps, SMTPProps, SocialLoginProps } from "@/types";
import GoogleAuthSettings from "@/Components/Forms/GoogleAuthSettings";
import SMTPSettings from "@/Components/Forms/SMTPSettings";
import AppSettingsForm from "@/Components/Forms/AppSettings";

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
         <Breadcrumb Icon={Setting} title="Uygulama Ayarları" />

         <AppSettingsForm app={app} />
         <GoogleAuthSettings google={google} />
         <SMTPSettings smtp={smtp} />
      </>
   );
};

AppSettings.layout = (page: ReactNode) => <Dashboard children={page} />;

export default AppSettings;

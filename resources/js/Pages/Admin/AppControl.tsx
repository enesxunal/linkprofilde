import Dashboard from "@/Layouts/Dashboard";
import { Head } from "@inertiajs/react";
import Breadcrumb from "@/Components/Breadcrumb";
import Control from "@/Components/Icons/Control";
import { ReactNode } from "react";

interface Props {
   version: string;
}

const AppControl = (props: Props) => {
   const { version } = props;

   return (
      <>
         <Head title="Uygulama Kontrolü" />
         <Breadcrumb Icon={Control} title="Uygulama Kontrolü" />

         <div className="">
            <p className="font-medium">
               {"Yüklü sürüm: "}
               <span className="font-normal">{version}</span>
            </p>
            <p className="font-medium mt-4 text-gray-600 text-sm">
               Otomatik güncelleme devre dışı bırakıldı. Veritabanı güvenliği için bu özellik kapatılmıştır.
            </p>
         </div>
      </>
   );
};

AppControl.layout = (page: ReactNode) => <Dashboard children={page} />;

export default AppControl;

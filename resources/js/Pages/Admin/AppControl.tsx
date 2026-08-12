import Dashboard from "@/Layouts/Dashboard";
import { Head } from "@inertiajs/react";
import { ReactNode } from "react";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import Badge from "@/Components/Panel/Badge";

interface Props {
   version: string;
}

const AppControl = (props: Props) => {
   const { version } = props;

   return (
      <>
         <Head title="Uygulama Kontrolü" />
         <PageHeader
            title="Uygulama Kontrolü"
            description="Kurulu sürüm ve güncelleme durumu."
         />

         <PanelCard className="max-w-xl">
            <div className="flex items-center gap-3">
               <p className="text-sm font-medium text-slate-700">
                  Yüklü sürüm
               </p>
               <Badge variant="info">{version}</Badge>
            </div>
            <p className="mt-4 text-sm text-slate-600">
               Otomatik güncelleme devre dışı bırakıldı. Veritabanı güvenliği
               için bu özellik kapatılmıştır.
            </p>
         </PanelCard>
      </>
   );
};

AppControl.layout = (page: ReactNode) => <Dashboard children={page} />;

export default AppControl;

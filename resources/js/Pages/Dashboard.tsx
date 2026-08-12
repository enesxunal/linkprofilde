import { ReactNode } from "react";
import { PageProps } from "@/types";
import { Head } from "@inertiajs/react";
import Link from "@/Components/Icons/Link";
import QRcode from "@/Components/Icons/QRcode";
import DashboardLayout from "@/Layouts/Dashboard";
import AreaChart from "@/Components/Charts/AreaChart";
import LineChart from "@/Components/Charts/LineChart";
import ListCheck from "@/Components/Icons/ListCheck";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";

interface Props extends PageProps {
   links: number;
   qrcodes: number;
   projects: number;
   analytics: number;
   page_view: number[];
   visitors: number[];
}

const Dashboard = (props: Props) => {
   const { links, qrcodes, projects, analytics, page_view, visitors } = props;

   const overview = [
      {
         Icon: Link,
         title: "Tüm Profiller",
         total: links,
      },
      {
         Icon: Link,
         title: "Tüm tıklamalar",
         total: analytics,
      },
      {
         Icon: ListCheck,
         title: "Tüm Projeler",
         total: projects,
      },
      {
         Icon: QRcode,
         title: "Tüm Qr Kodları",
         total: qrcodes,
      },
   ];

   const lastSevenDays: string[] = [];
   for (let i = 6; i >= 0; i--) {
      const date = new Date();
      date.setDate(date.getDate() - i);
      const countDay = date.toLocaleDateString("tr-Tr", {
         day: "2-digit",
         month: "short",
         year: "2-digit",
      });
      lastSevenDays.push(countDay);
   }

   return (
      <>
         <Head title="Kontrol Paneli" />

         <PageHeader
            title="Kontrol Paneli"
            description="Profilleriniz, tıklamalarınız ve QR kodlarınıza genel bakış."
         />

         <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            {overview.map((item, ind) => (
               <PanelCard key={ind} noPadding bodyClassName="p-5">
                  <div className="flex flex-col gap-3">
                     <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50">
                        <item.Icon className="h-4 w-4 text-blue-600" />
                     </div>
                     <p className="text-sm font-medium text-slate-500">
                        {item.title}
                     </p>
                     <p className="text-2xl font-bold tracking-tight text-slate-900">
                        {item.total}
                     </p>
                  </div>
               </PanelCard>
            ))}
         </div>

         <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <PanelCard
               title="Aylık sayfa görüntüleme etkinlikleri"
               noPadding
               bodyClassName="pr-2 pb-2"
            >
               <AreaChart
                  height={300}
                  data={[
                     {
                        name: "Total View",
                        data: visitors,
                     },
                  ]}
               />
            </PanelCard>
            <PanelCard
               title="Günlük sayfa görüntüleme etkinlikleri"
               noPadding
               bodyClassName="pr-2 pb-2"
            >
               <LineChart
                  label={lastSevenDays}
                  height={300}
                  data={[
                     {
                        name: "Total Users",
                        data: page_view,
                     },
                  ]}
               />
            </PanelCard>
         </div>
      </>
   );
};

Dashboard.layout = (page: ReactNode) => <DashboardLayout children={page} />;

export default Dashboard;

import { ReactNode } from "react";
import { LinkProps, PageProps } from "@/types";
import { Head, Link as InertiaLink } from "@inertiajs/react";
import LinkIcon from "@/Components/Icons/Link";
import QRcode from "@/Components/Icons/QRcode";
import DashboardLayout from "@/Layouts/Dashboard";
import AreaChart from "@/Components/Charts/AreaChart";
import LineChart from "@/Components/Charts/LineChart";
import ListCheck from "@/Components/Icons/ListCheck";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EditPen from "@/Components/Icons/EditPen";
import ChartLineUp from "@/Components/Icons/ChartLineUp";

interface Props extends PageProps {
   links: number;
   qrcodes: number;
   projects: number;
   analytics: number;
   page_view: number[];
   visitors: number[];
   event_metrics: {
      interactions_30d: number;
      link_clicks_30d: number;
      social_clicks_30d: number;
      vcard_downloads_30d: number;
      shares_30d: number;
      qr_scans_30d: number;
   };
   primary_profile: Pick<
      LinkProps,
      "id" | "link_name" | "url_name" | "thumbnail" | "short_bio"
   > | null;
}

const Dashboard = (props: Props) => {
   const {
      links,
      qrcodes,
      projects,
      analytics,
      page_view,
      visitors,
      event_metrics,
      primary_profile,
   } = props;

   const overview = [
      {
         Icon: LinkIcon,
         title: "Toplam profil ve kısa link",
         total: links,
      },
      {
         Icon: ChartLineUp,
         title: "Profil görüntülenmeleri",
         total: analytics,
      },
      {
         Icon: ListCheck,
         title: "Projeler",
         total: projects,
      },
      {
         Icon: QRcode,
         title: "QR kodlar",
         total: qrcodes,
      },
   ];

   const interactionCards = [
      {
         title: "Toplam etkileşim",
         value: event_metrics.interactions_30d,
         hint: "Son 30 gün",
      },
      {
         title: "Link + sosyal tıklama",
         value: event_metrics.link_clicks_30d + event_metrics.social_clicks_30d,
         hint: `${event_metrics.link_clicks_30d} link · ${event_metrics.social_clicks_30d} sosyal`,
      },
      {
         title: "QR taraması",
         value: event_metrics.qr_scans_30d,
         hint: "Son 30 gün",
      },
      {
         title: "Paylaşım + rehbere ekle",
         value: event_metrics.shares_30d + event_metrics.vcard_downloads_30d,
         hint: `${event_metrics.shares_30d} paylaşım · ${event_metrics.vcard_downloads_30d} rehber`,
      },
   ];

   const lastSevenDays: string[] = [];
   for (let i = 6; i >= 0; i--) {
      const date = new Date();
      date.setDate(date.getDate() - i);
      const countDay = date.toLocaleDateString("tr-TR", {
         day: "2-digit",
         month: "short",
      });
      lastSevenDays.push(countDay);
   }

   return (
      <>
         <Head title="Kontrol Paneli" />

         <PageHeader
            title="Kontrol Paneli"
            description="Profilleriniz, ziyaretleriniz, etkileşimleriniz ve QR kodlarınıza tek yerden erişin."
         />

         {primary_profile ? (
            <PanelCard noPadding bodyClassName="p-5 sm:p-6">
               <div className="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                  <div className="flex min-w-0 items-center gap-4">
                     {primary_profile.thumbnail ? (
                        <img
                           src={`/${primary_profile.thumbnail}`}
                           alt={`${primary_profile.link_name} profil fotoğrafı`}
                           className="h-14 w-14 shrink-0 rounded-full border border-slate-200 object-cover"
                        />
                     ) : (
                        <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                           <LinkIcon className="h-5 w-5" />
                        </div>
                     )}
                     <div className="min-w-0">
                        <p className="text-xs font-semibold uppercase tracking-wide text-blue-600">
                           Ana profiliniz
                        </p>
                        <h2 className="mt-1 truncate text-lg font-semibold text-slate-900">
                           {primary_profile.link_name}
                        </h2>
                        <p className="mt-1 truncate text-sm text-slate-500">
                           /{primary_profile.url_name}
                        </p>
                     </div>
                  </div>

                  <div className="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                     <InertiaLink
                        href={`/bio-links/customize/${primary_profile.id}`}
                        className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700"
                     >
                        <EditPen className="h-4 w-4" />
                        Profili Düzenle
                     </InertiaLink>
                     <a
                        href={`/${primary_profile.url_name}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50"
                     >
                        Profili Gör
                     </a>
                     <InertiaLink
                        href={`/qrcodes/create?link_id=${primary_profile.id}&type=biolink`}
                        className="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50"
                     >
                        <QRcode className="h-4 w-4" />
                        QR Oluştur
                     </InertiaLink>
                     <InertiaLink
                        href={`/link/analytics/${primary_profile.id}`}
                        className="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50"
                     >
                        Analitiği Gör
                     </InertiaLink>
                  </div>
               </div>
            </PanelCard>
         ) : (
            <PanelCard noPadding bodyClassName="p-5 sm:p-6">
               <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                  <div>
                     <h2 className="text-base font-semibold text-slate-900">
                        İlk profilinizi oluşturun
                     </h2>
                     <p className="mt-1 text-sm text-slate-500">
                        Bağlantılarınızı, iletişim bilgilerinizi ve QR kodunuzu tek profilde toplayın.
                     </p>
                  </div>
                  <InertiaLink
                     href="/bio-links"
                     className="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                  >
                     Profil Oluştur
                  </InertiaLink>
               </div>
            </PanelCard>
         )}

         <div className="grid grid-cols-2 gap-3 lg:grid-cols-4">
            {overview.map((item, ind) => (
               <PanelCard key={ind} noPadding bodyClassName="p-4 sm:p-5">
                  <div className="flex flex-col gap-3">
                     <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50">
                        <item.Icon className="h-4 w-4 text-blue-600" />
                     </div>
                     <p className="text-xs font-medium leading-5 text-slate-500 sm:text-sm">
                        {item.title}
                     </p>
                     <p className="text-2xl font-bold tracking-tight text-slate-900">
                        {item.total}
                     </p>
                  </div>
               </PanelCard>
            ))}
         </div>

         <PanelCard
            title="Etkileşimler"
            description="Ziyaretçilerin profilinizde yaptığı aksiyonlar · son 30 gün"
         >
            <div className="grid grid-cols-2 gap-3 lg:grid-cols-4">
               {interactionCards.map((card) => (
                  <div
                     key={card.title}
                     className="rounded-xl border border-slate-200 bg-slate-50/70 p-4"
                  >
                     <p className="text-xs font-medium leading-5 text-slate-500 sm:text-sm">
                        {card.title}
                     </p>
                     <p className="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                        {card.value}
                     </p>
                     <p className="mt-1 text-xs text-slate-400">{card.hint}</p>
                  </div>
               ))}
            </div>
         </PanelCard>

         <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <PanelCard
               title="Aylık profil görüntülenmeleri"
               noPadding
               bodyClassName="pr-2 pb-2"
            >
               <AreaChart
                  height={300}
                  data={[
                     {
                        name: "Görüntülenme",
                        data: visitors,
                     },
                  ]}
               />
            </PanelCard>
            <PanelCard
               title="Son 7 gün"
               noPadding
               bodyClassName="pr-2 pb-2"
            >
               <LineChart
                  label={lastSevenDays}
                  height={300}
                  data={[
                     {
                        name: "Görüntülenme",
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

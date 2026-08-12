import { ReactNode, useEffect, useMemo, useState } from "react";
import { Head, router } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";
import LineChart from "@/Components/Charts/LineChart";
import Devices from "@/Components/LinkAnalytics/Devices";
import Overview from "@/Components/LinkAnalytics/Overview";
import Countries from "@/Components/LinkAnalytics/Countries";
import Referrers from "@/Components/LinkAnalytics/Referrers";
import Operating from "@/Components/LinkAnalytics/Operating";
import Languages from "@/Components/LinkAnalytics/Languages";
import Browsers from "@/Components/LinkAnalytics/Browsers";
import { BreakdownItem } from "@/Components/LinkAnalytics/BreakdownList";

interface AnalyticsPayload {
   link: {
      id: number;
      name: string;
      url_name: string;
      link_type: string;
      type_label: string;
   };
   range: {
      key: string;
      label: string;
      from: string;
      to: string;
   };
   overview: {
      total_views: number;
      today: number;
      selected_period_total: number;
      previous_period_total: number;
      period_change_percent: number | null;
   };
   timeseries: { date: string; count: number }[];
   countries: BreakdownItem[];
   devices: BreakdownItem[];
   browsers: BreakdownItem[];
   operating_systems: BreakdownItem[];
   languages: BreakdownItem[];
   referrers: BreakdownItem[];
}

interface Props {
   analytics: AnalyticsPayload;
}

const RANGE_OPTIONS = [
   { key: "today", label: "Bugün" },
   { key: "7d", label: "7 gün" },
   { key: "30d", label: "30 gün" },
   { key: "90d", label: "90 gün" },
   { key: "custom", label: "Özel" },
] as const;

const LinkAnalytics = ({ analytics }: Props) => {
   const [activeTab, setActiveTab] = useState("overview");
   const [customFrom, setCustomFrom] = useState(analytics.range.from);
   const [customTo, setCustomTo] = useState(analytics.range.to);

   useEffect(() => {
      setCustomFrom(analytics.range.from);
      setCustomTo(analytics.range.to);
   }, [analytics.range.from, analytics.range.to]);

   const changePercent = analytics.overview.period_change_percent;
   const changePositive = (changePercent ?? 0) >= 0;

   const chartLabels = useMemo(
      () =>
         analytics.timeseries.map((point) => {
            const d = new Date(`${point.date}T12:00:00`);
            return d.toLocaleDateString("tr-TR", {
               day: "2-digit",
               month: "short",
            });
         }),
      [analytics.timeseries]
   );

   const chartData = useMemo(
      () => analytics.timeseries.map((point) => point.count),
      [analytics.timeseries]
   );

   const applyRange = (key: string, from?: string, to?: string) => {
      const params: Record<string, string> = { range: key };
      if (key === "custom" && from && to) {
         params.from = from;
         params.to = to;
      }
      router.get(`/link/analytics/${analytics.link.id}`, params, {
         preserveState: true,
         preserveScroll: true,
         replace: true,
      });
   };

   const headers = [
      {
         id: "overview",
         title: "Özet",
         render: () => (
            <Overview
               countries={analytics.countries}
               referrers={analytics.referrers}
               devices={analytics.devices}
               operating_systems={analytics.operating_systems}
               browsers={analytics.browsers}
               languages={analytics.languages}
            />
         ),
      },
      {
         id: "countries",
         title: "Ülkeler",
         render: () => <Countries items={analytics.countries} />,
      },
      {
         id: "referrers",
         title: "Yönlendirenler",
         render: () => <Referrers items={analytics.referrers} />,
      },
      {
         id: "devices",
         title: "Cihazlar",
         render: () => <Devices items={analytics.devices} />,
      },
      {
         id: "os_system",
         title: "İşletim Sistemi",
         render: () => <Operating items={analytics.operating_systems} />,
      },
      {
         id: "browsers",
         title: "Tarayıcılar",
         render: () => <Browsers items={analytics.browsers} />,
      },
      {
         id: "languages",
         title: "Diller",
         render: () => <Languages items={analytics.languages} />,
      },
   ];

   const active = headers.find((header) => header.id === activeTab) ?? headers[0];

   const cards = [
      {
         title: "Toplam Görüntülenme",
         value: analytics.overview.total_views,
         hint: "Tüm zamanlar",
      },
      {
         title: "Seçili Dönem",
         value: analytics.overview.selected_period_total,
         hint: analytics.range.label,
      },
      {
         title: "Bugün",
         value: analytics.overview.today,
         hint: "Bugünkü görüntülenme",
      },
      {
         title: "Önceki Döneme Göre",
         value:
            changePercent === null
               ? "—"
               : `${changePositive ? "+" : ""}${changePercent}%`,
         hint: `Önceki: ${analytics.overview.previous_period_total}`,
      },
   ];

   return (
      <>
         <Head title={`${analytics.link.name} · Analitik`} />
         <PageHeader
            title={analytics.link.name}
            description={`/${analytics.link.url_name} için ziyaretçi analitikleri.`}
            actions={
               <span className="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                  {analytics.link.type_label}
               </span>
            }
         />

         <div className="space-y-6">
            <PanelCard
               title="Tarih aralığı"
               description="Metrikler seçili döneme göre hesaplanır. Varsayılan: son 30 gün."
               actions={
                  <div className="flex flex-wrap items-center gap-2">
                     {RANGE_OPTIONS.map((option) => {
                        const isActive = analytics.range.key === option.key;
                        return (
                           <button
                              key={option.key}
                              type="button"
                              onClick={() => {
                                 if (option.key === "custom") {
                                    applyRange("custom", customFrom, customTo);
                                    return;
                                 }
                                 applyRange(option.key);
                              }}
                              className={`rounded-lg px-3 py-1.5 text-sm font-medium transition-colors ${
                                 isActive
                                    ? "bg-blue-600 text-white"
                                    : "bg-slate-100 text-slate-700 hover:bg-slate-200"
                              }`}
                           >
                              {option.label}
                           </button>
                        );
                     })}
                  </div>
               }
            >
               <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
                  <label className="flex flex-col gap-1 text-sm text-slate-600">
                     Başlangıç
                     <input
                        type="date"
                        value={customFrom}
                        onChange={(e) => setCustomFrom(e.target.value)}
                        className="rounded-lg border border-slate-200 px-3 py-2 text-slate-900"
                     />
                  </label>
                  <label className="flex flex-col gap-1 text-sm text-slate-600">
                     Bitiş
                     <input
                        type="date"
                        value={customTo}
                        onChange={(e) => setCustomTo(e.target.value)}
                        className="rounded-lg border border-slate-200 px-3 py-2 text-slate-900"
                     />
                  </label>
                  <button
                     type="button"
                     onClick={() => applyRange("custom", customFrom, customTo)}
                     className="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                  >
                     Özel aralığı uygula
                  </button>
               </div>
            </PanelCard>

            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
               {cards.map((card) => (
                  <PanelCard key={card.title} noPadding bodyClassName="p-5">
                     <p className="text-sm font-medium text-slate-500">
                        {card.title}
                     </p>
                     <p className="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                        {card.value}
                     </p>
                     <p className="mt-1 text-xs text-slate-500">{card.hint}</p>
                  </PanelCard>
               ))}
            </div>

            <PanelCard
               title="Günlük görüntülenme"
               description={analytics.range.label}
               noPadding
               bodyClassName="pr-2 pb-2"
            >
               {analytics.timeseries.every((p) => p.count === 0) ? (
                  <EmptyState
                     title="Bu dönemde görüntülenme yok"
                     description="Tarih aralığını genişletmeyi deneyin."
                  />
               ) : (
                  <LineChart
                     height={320}
                     label={chartLabels}
                     data={[
                        {
                           name: "Görüntülenme",
                           data: chartData,
                        },
                     ]}
                  />
               )}
            </PanelCard>

            <PanelCard noPadding>
               <div className="overflow-x-auto">
                  <div
                     className="flex min-w-max gap-1 border-b border-slate-200 px-3 sm:px-4"
                     role="tablist"
                     aria-label="Analitik sekmeleri"
                  >
                     {headers.map((header) => {
                        const isActive = activeTab === header.id;
                        return (
                           <button
                              key={header.id}
                              type="button"
                              role="tab"
                              aria-selected={isActive}
                              onClick={() => setActiveTab(header.id)}
                              className={`whitespace-nowrap border-b-2 px-3 py-3 text-sm font-medium transition-colors ${
                                 isActive
                                    ? "border-blue-600 text-blue-600"
                                    : "border-transparent text-slate-500 hover:text-slate-800"
                              }`}
                           >
                              {header.title}
                           </button>
                        );
                     })}
                  </div>
               </div>
            </PanelCard>

            <div role="tabpanel">{active.render()}</div>
         </div>
      </>
   );
};

LinkAnalytics.layout = (page: ReactNode) => <Dashboard children={page} />;

export default LinkAnalytics;

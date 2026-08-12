import { ReactNode, useState } from "react";
import { Head } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import Devices from "@/Components/LinkAnalytics/Devices";
import Overview from "@/Components/LinkAnalytics/Overview";
import Countries from "@/Components/LinkAnalytics/Countries";
import Referrers from "@/Components/LinkAnalytics/Referrers";
import Operating from "@/Components/LinkAnalytics/Operating";
import Languages from "@/Components/LinkAnalytics/Languages";
import Browsers from "@/Components/LinkAnalytics/Browsers";

interface Props {
   languages: any;
   analytics: any;
}

const LinkAnalytics = (props: Props) => {
   const { languages, analytics } = props;
   const [activeTab, setActiveTab] = useState("overview");

   const headers = [
      { id: "overview", title: "Özet", Component: Overview },
      { id: "countries", title: "Ülkeler", Component: Countries },
      { id: "referrers", title: "Yönlendirenler", Component: Referrers },
      { id: "devices", title: "Cihazlar", Component: Devices },
      { id: "os_system", title: "İşletim Sistemi", Component: Operating },
      { id: "browsers", title: "Tarayıcılar", Component: Browsers },
      { id: "languages", title: "Diller", Component: Languages },
   ];

   const ActiveComponent =
      headers.find((header) => header.id === activeTab)?.Component ?? Overview;

   return (
      <>
         <Head title="Link Ziyaretçi Analitiği" />
         <PageHeader
            title="Link Ziyaretçi Analitiği"
            description="Ziyaretçi kaynaklarını ve cihaz dağılımını inceleyin."
         />

         <div className="space-y-6">
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

            <div role="tabpanel">
               <ActiveComponent analytics={analytics} languages={languages} />
            </div>
         </div>
      </>
   );
};

LinkAnalytics.layout = (page: ReactNode) => <Dashboard children={page} />;

export default LinkAnalytics;

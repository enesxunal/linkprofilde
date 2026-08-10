import {
   Tab,
   Tabs,
   TabsBody,
   TabPanel,
   TabsHeader,
} from "@material-tailwind/react";
import { ReactNode } from "react";
import { Head } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import Breadcrumb from "@/Components/Breadcrumb";
import ChartLineUp from "@/Components/Icons/ChartLineUp";
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

   const headers = [
      { id: "overview", title: "Özet", Component: Overview },
      { id: "countries", title: "Ülkeler", Component: Countries },
      { id: "referrers", title: "Yönlendirenler", Component: Referrers },
      { id: "devices", title: "Cihazlar", Component: Devices },
      { id: "os_system", title: "İşletim Sistemi", Component: Operating },
      { id: "browsers", title: "Tarayıcılar", Component: Browsers },
      { id: "languages", title: "Diller", Component: Languages },
   ];

   return (
      <>
         <Head title="Link Ziyaretçi Analitiği" />
         <Breadcrumb Icon={ChartLineUp} title="Link Ziyaretçi Analitiği" />

         <div className="">
            <Tabs value="overview">
               <TabsHeader
                  className="bg-transparent w-full mx-auto mb-3 px-2"
                  indicatorProps={{ className: "bg-blue-500 text-white" }}
               >
                  {headers.map((header) => (
                     <Tab
                        key={header.id}
                        value={header.id}
                        className="py-2 transition-colors duration-300"
                        activeClassName="text-white"
                     >
                        {header.title}
                     </Tab>
                  ))}
               </TabsHeader>
               <TabsBody>
                  {headers.map((header) => {
                     const { id, Component } = header;
                     return (
                        <TabPanel key={id} value={id} className="px-2">
                           <Component
                              analytics={analytics}
                              languages={languages}
                           />
                        </TabPanel>
                     );
                  })}
               </TabsBody>
            </Tabs>
         </div>
      </>
   );
};

LinkAnalytics.layout = (page: ReactNode) => <Dashboard children={page} />;

export default LinkAnalytics;

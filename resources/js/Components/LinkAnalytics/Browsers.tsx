import { Progress } from "@material-tailwind/react";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";

interface BrowserCount {
   [browser: string]: number;
}

interface Props {
   analytics: any[];
   overview?: boolean;
}

const Browsers = (props: Props) => {
   const { analytics, overview } = props;
   const browsers: string[] = analytics.map((item) => item.browser);

   const browserCounted: BrowserCount = browsers.reduce(
      (acc: BrowserCount, browser: string) => {
         acc[browser] = (acc[browser] || 0) + 1;
         return acc;
      },
      {}
   );

   let values;
   if (overview) {
      values = Object.entries(browserCounted).slice(0, 5);
   } else {
      values = Object.entries(browserCounted);
   }

   return (
      <PanelCard title="Tarayıcılar">
         {values.length === 0 ? (
            <EmptyState
               title="Tarayıcı verisi yok"
               description="Henüz görüntülenecek tarayıcı kaydı bulunmuyor."
            />
         ) : (
            values.map(([browser, count]) => {
               const totalWindows = Math.abs((count * 100) / browsers.length);
               return (
                  <div key={browser} className="my-3">
                     <div className="flex items-center justify-between gap-3">
                        <p className="min-w-0 truncate text-sm font-medium text-slate-800">
                           {browser}
                        </p>
                        <p className="shrink-0 text-sm text-slate-600">
                           <span>{Math.round(totalWindows)}%</span>
                           <span className="pl-4">{count}</span>
                        </p>
                     </div>
                     <Progress value={Math.round(totalWindows)} />
                  </div>
               );
            })
         )}
      </PanelCard>
   );
};

export default Browsers;

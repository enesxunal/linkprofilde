import { Progress } from "@material-tailwind/react";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";

interface RefererCount {
   Refer: number;
   Direct: number;
}

interface Props {
   analytics: any[];
   overview?: boolean;
}

const Referrers = (props: Props) => {
   const { analytics, overview } = props;
   const referers: string[] = analytics.map((item) => item.referer);

   const refererCounted: RefererCount = referers.reduce(
      (acc: RefererCount, referer: string) => {
         if (referer) {
            acc.Refer++;
         } else {
            acc.Direct++;
         }
         return acc;
      },
      { Refer: 0, Direct: 0 }
   );

   let values;
   if (overview) {
      values = Object.entries(refererCounted).slice(0, 5);
   } else {
      values = Object.entries(refererCounted);
   }

   const visible = values.filter(
      ([key, value]) =>
         (key === "Refer" && value > 0) || (key === "Direct" && value > 0)
   );

   return (
      <PanelCard title="Yönlendirenler">
         {visible.length === 0 ? (
            <EmptyState
               title="Yönlendiren verisi yok"
               description="Henüz görüntülenecek yönlendiren kaydı bulunmuyor."
            />
         ) : (
            values.map(([key, value]) => {
               if (
                  (key === "Refer" && value > 0) ||
                  (key === "Direct" && value > 0)
               ) {
                  const totalReferer = Math.abs(
                     (value * 100) / referers.length
                  );
                  return (
                     <div key={key} className="my-3">
                        <div className="flex items-center justify-between gap-3">
                           <p className="min-w-0 truncate break-all text-sm font-medium text-slate-800">
                              {key === "Refer" ? "Yönlendiren" : "Doğrudan"}
                           </p>
                           <p className="shrink-0 text-sm text-slate-600">
                              <span>{Math.round(totalReferer)}%</span>
                              <span className="pl-4">{value}</span>
                           </p>
                        </div>
                        <Progress value={Math.round(totalReferer)} />
                     </div>
                  );
               }
               return null;
            })
         )}
      </PanelCard>
   );
};

export default Referrers;

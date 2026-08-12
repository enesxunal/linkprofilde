import { Progress } from "@material-tailwind/react";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";

interface DeviceCount {
   [device: string]: number;
}

interface Props {
   analytics: any[];
   overview?: boolean;
}

const Operating = (props: Props) => {
   const { analytics, overview } = props;
   const operatingSystems: string[] = analytics.map((item) => item.platform);

   const osCounted: DeviceCount = operatingSystems.reduce(
      (acc: DeviceCount, device: string) => {
         acc[device] = (acc[device] || 0) + 1;
         return acc;
      },
      {}
   );

   let values;
   if (overview) {
      values = Object.entries(osCounted).slice(0, 5);
   } else {
      values = Object.entries(osCounted);
   }

   return (
      <PanelCard title="İşletim Sistemleri">
         {values.length === 0 ? (
            <EmptyState
               title="İşletim sistemi verisi yok"
               description="Henüz görüntülenecek işletim sistemi kaydı bulunmuyor."
            />
         ) : (
            values.map(([device, count]) => {
               const totalWindows = Math.abs(
                  (count * 100) / operatingSystems.length
               );
               return (
                  <div key={device} className="my-3">
                     <div className="flex items-center justify-between gap-3">
                        <p className="min-w-0 truncate text-sm font-medium text-slate-800">
                           {device}
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

export default Operating;

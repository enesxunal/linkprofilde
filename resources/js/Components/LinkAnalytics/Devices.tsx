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

const Devices = (props: Props) => {
   const { analytics, overview } = props;
   const devices: string[] = analytics.map((item) => item.device);

   const deviceCounted: DeviceCount = devices.reduce(
      (acc: DeviceCount, device: string) => {
         acc[device] = (acc[device] || 0) + 1;
         return acc;
      },
      {}
   );

   let values;
   if (overview) {
      values = Object.entries(deviceCounted).slice(0, 5);
   } else {
      values = Object.entries(deviceCounted);
   }

   return (
      <PanelCard title="Cihazlar">
         {values.length === 0 ? (
            <EmptyState
               title="Cihaz verisi yok"
               description="Henüz görüntülenecek cihaz kaydı bulunmuyor."
            />
         ) : (
            values.map(([device, count]) => {
               const totalWindows = Math.abs((count * 100) / devices.length);
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

export default Devices;

import { Progress } from "@material-tailwind/react";

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
      <div className="card p-6">
         <h6>İşletim Sistemleri</h6>
         {values.map(([device, count]) => {
            const totalWindows = Math.abs(
               (count * 100) / operatingSystems.length
            );
            return (
               <div key={device} className="my-3">
                  <div className="flex items-center justify-between">
                     <p>{device}</p>
                     <p>
                        <span className="text-sm">
                           {Math.round(totalWindows)}%
                        </span>
                        <span className="pl-4">{count}</span>
                     </p>
                  </div>
                  <Progress value={Math.round(totalWindows)} />
               </div>
            );
         })}
      </div>
   );
};

export default Operating;

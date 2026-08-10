import { Progress } from "@material-tailwind/react";

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

   return (
      <div className="card p-6">
         <h6>Yönlendirenler</h6>
         {values.map(([key, value]) => {
            if (
               (key === "Refer" && value > 0) ||
               (key === "Direct" && value > 0)
            ) {
               const totalReferer = Math.abs((value * 100) / referers.length);
               return (
                  <div key={key} className="my-3">
                     <div className="flex items-center justify-between">
                        <p>{key}</p>
                        <p>
                           <span className="text-sm">
                              {Math.round(totalReferer)}%
                           </span>
                           <span className="pl-4">{value}</span>
                        </p>
                     </div>
                     <Progress value={Math.round(totalReferer)} />
                  </div>
               );
            }
            return null;
         })}
      </div>
   );
};

export default Referrers;

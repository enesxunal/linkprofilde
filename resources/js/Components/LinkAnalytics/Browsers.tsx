import { Progress } from "@material-tailwind/react";

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
      <div className="card p-6">
         <h6>Tarayıcılar</h6>
         {values.map(([browser, count]) => {
            const totalWindows = Math.abs((count * 100) / browsers.length);
            return (
               <div key={browser} className="my-3">
                  <div className="flex items-center justify-between">
                     <p>{browser}</p>
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

export default Browsers;

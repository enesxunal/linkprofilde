import { Progress } from "@material-tailwind/react";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";

interface CountryCount {
   [country: string]: number;
}

interface Props {
   analytics: any[];
   overview?: boolean;
}
const Countries = (props: Props) => {
   const { analytics, overview } = props;

   const countries: string[] = [];
   analytics.forEach((item: any) => {
      const country = JSON.parse(item.ip).countryName;
      countries.push(country);
   });

   const countryCounted: CountryCount = countries.reduce(
      (acc: CountryCount, country: string) => {
         acc[country] = (acc[country] || 0) + 1;
         return acc;
      },
      {}
   );

   let values;
   if (overview) {
      values = Object.entries(countryCounted).slice(0, 5);
   } else {
      values = Object.entries(countryCounted);
   }

   return (
      <PanelCard title="Ülkeler">
         {values.length === 0 ? (
            <EmptyState
               title="Ülke verisi yok"
               description="Henüz görüntülenecek ziyaretçi ülkesi bulunmuyor."
            />
         ) : (
            values.map(([country, count]) => {
               const totalCountry = Math.abs((count * 100) / countries.length);

               return (
                  <div key={country} className="my-3">
                     <div className="flex items-center justify-between gap-3">
                        <p className="min-w-0 truncate text-sm font-medium text-slate-800">
                           {country === "undefined" ? "Belirsiz" : country}
                        </p>
                        <p className="shrink-0 text-sm text-slate-600">
                           <span>{Math.round(totalCountry)}%</span>
                           <span className="pl-4">{count}</span>
                        </p>
                     </div>
                     <Progress value={Math.round(totalCountry)} />
                  </div>
               );
            })
         )}
      </PanelCard>
   );
};

export default Countries;

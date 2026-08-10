import { Progress } from "@material-tailwind/react";

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
      <div className="card p-6">
         <h6>Ülkeler</h6>
         {values.map(([country, count]) => {
            const totalCountry = Math.abs((count * 100) / countries.length);

            return (
               <div key={country} className="my-3">
                  <div className="flex items-center justify-between">
                     <p>{country === 'undefined' ? 'Belirsiz' : country}</p>
                     <p>
                        <span className="text-sm">
                           {Math.round(totalCountry)}%
                        </span>
                        <span className="pl-4">{count}</span>
                     </p>
                  </div>
                  <Progress value={Math.round(totalCountry)} />
               </div>
            );
         })}
      </div>
   );
};

export default Countries;

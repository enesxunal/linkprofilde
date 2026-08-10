import { Progress } from "@material-tailwind/react";

interface Language {
   code: string;
   name: string;
}

interface LanguageCount {
   [language: string]: number;
}

interface Props {
   analytics: any[];
   languages: any[];
   overview?: boolean;
}

const Languages = (props: Props) => {
   const { analytics, overview, languages } = props;

   const lanCodes: string[] = analytics.map(
      (item) => JSON.parse(item.languages)[1]
   );

   const languagesCounted: LanguageCount = lanCodes.reduce(
      (acc: LanguageCount, code: string) => {
         acc[code] = (acc[code] || 0) + 1;
         return acc;
      },
      {}
   );

   function getLanName(languages: Language[], code: string): string {
      let lan_name = "";
      for (const lan of languages) {
         if (lan.code === code) {
            lan_name = lan.name;
            break;
         }
      }
      return lan_name;
   }

   const languageNames: LanguageCount = {};
   Object.entries(languagesCounted).forEach(([key, value]) => {
      const lanName = getLanName(languages, key);
      languageNames[lanName] = value;
   });

   let values;
   if (overview) {
      values = Object.entries(languageNames).slice(0, 5);
   } else {
      values = Object.entries(languageNames);
   }

   return (
      <div className="card p-6">
         <h6>Diller</h6>
         {values.map(([language, count]) => {
            const totalLanguages = Math.abs((count * 100) / lanCodes.length);
            return (
               <div key={language} className="my-3">
                  <div className="flex items-center justify-between">
                     <p>{language}</p>
                     <p>
                        <span className="text-sm">
                           {Math.round(totalLanguages)}%
                        </span>
                        <span className="pl-4">{count}</span>
                     </p>
                  </div>
                  <Progress value={Math.round(totalLanguages)} />
               </div>
            );
         })}
      </div>
   );
};

export default Languages;

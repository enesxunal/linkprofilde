interface Series {
   name: string;
   data: number[];
}

interface Props {
   height: number;
   data: Series[];
}

const MONTHS = [
   "Oca",
   "Şub",
   "Mar",
   "Nis",
   "May",
   "Haz",
   "Tem",
   "Ağu",
   "Eyl",
   "Eki",
   "Kas",
   "Ara",
];

const AreaChart = ({ height, data }: Props) => {
   const values = Array.isArray(data?.[0]?.data)
      ? data[0].data.map((value) => Number(value) || 0)
      : [];
   const normalized = Array.from({ length: 12 }, (_, index) => values[index] ?? 0);
   const max = Math.max(...normalized, 1);
   const hasData = normalized.some((value) => value > 0);

   return (
      <div style={{ minHeight: height }} className="flex flex-col justify-end px-4 pb-4 pt-5">
         {!hasData ? (
            <div className="flex flex-1 items-center justify-center text-sm text-slate-400">
               Henüz görüntülenme verisi yok.
            </div>
         ) : (
            <div className="flex flex-1 items-end gap-1.5 sm:gap-2" aria-label={data?.[0]?.name || "Aylık grafik"}>
               {normalized.map((value, index) => {
                  const percentage = Math.max((value / max) * 100, value > 0 ? 4 : 0);
                  return (
                     <div key={MONTHS[index]} className="flex min-w-0 flex-1 flex-col items-center justify-end gap-2">
                        <div className="group relative flex h-[220px] w-full items-end justify-center rounded-md bg-slate-50">
                           <div
                              className="w-full max-w-8 rounded-t-md bg-blue-500/85 transition-all group-hover:bg-blue-600"
                              style={{ height: `${percentage}%` }}
                           />
                           <span className="pointer-events-none absolute -top-7 hidden rounded-md bg-slate-900 px-2 py-1 text-[11px] font-medium text-white group-hover:block">
                              {value}
                           </span>
                        </div>
                        <span className="text-[10px] font-medium text-slate-400 sm:text-xs">
                           {MONTHS[index]}
                        </span>
                     </div>
                  );
               })}
            </div>
         )}
      </div>
   );
};

export default AreaChart;

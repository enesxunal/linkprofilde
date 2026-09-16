interface Series {
   name: string;
   data: number[];
}

interface Props {
   height: number;
   label: string[];
   data: Series[];
}

const LineChart = ({ height, label, data }: Props) => {
   const values = Array.isArray(data?.[0]?.data)
      ? data[0].data.map((value) => Number(value) || 0)
      : [];
   const count = Math.max(values.length, label.length, 1);
   const normalized = Array.from({ length: count }, (_, index) => values[index] ?? 0);
   const labels = Array.from({ length: count }, (_, index) => label[index] ?? "");
   const max = Math.max(...normalized, 1);
   const hasData = normalized.some((value) => value > 0);

   const width = 720;
   const chartHeight = 220;
   const paddingX = 24;
   const paddingTop = 18;
   const paddingBottom = 18;
   const usableWidth = width - paddingX * 2;
   const usableHeight = chartHeight - paddingTop - paddingBottom;

   const points = normalized.map((value, index) => {
      const x =
         count <= 1
            ? width / 2
            : paddingX + (index / (count - 1)) * usableWidth;
      const y = paddingTop + usableHeight - (value / max) * usableHeight;
      return { x, y, value };
   });
   const polyline = points.map((point) => `${point.x},${point.y}`).join(" ");

   return (
      <div style={{ minHeight: height }} className="px-3 pb-4 pt-4">
         {!hasData ? (
            <div className="flex h-[250px] items-center justify-center text-sm text-slate-400">
               Henüz görüntülenme verisi yok.
            </div>
         ) : (
            <>
               <div className="overflow-hidden rounded-lg bg-white">
                  <svg
                     viewBox={`0 0 ${width} ${chartHeight}`}
                     role="img"
                     aria-label={data?.[0]?.name || "Zaman serisi grafiği"}
                     className="h-[240px] w-full text-blue-600"
                     preserveAspectRatio="none"
                  >
                     {[0.25, 0.5, 0.75, 1].map((fraction) => {
                        const y = paddingTop + usableHeight * fraction;
                        return (
                           <line
                              key={fraction}
                              x1={paddingX}
                              y1={y}
                              x2={width - paddingX}
                              y2={y}
                              stroke="currentColor"
                              className="text-slate-100"
                              strokeWidth="1"
                           />
                        );
                     })}

                     <polyline
                        points={polyline}
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="4"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        vectorEffect="non-scaling-stroke"
                     />

                     {points.map((point, index) => (
                        <g key={`${point.x}-${index}`}>
                           <circle
                              cx={point.x}
                              cy={point.y}
                              r="5"
                              fill="white"
                              stroke="currentColor"
                              strokeWidth="3"
                              vectorEffect="non-scaling-stroke"
                           />
                           <title>{`${labels[index]}: ${point.value}`}</title>
                        </g>
                     ))}
                  </svg>
               </div>

               <div
                  className="mt-2 grid gap-1"
                  style={{ gridTemplateColumns: `repeat(${count}, minmax(0, 1fr))` }}
               >
                  {labels.map((item, index) => (
                     <span
                        key={`${item}-${index}`}
                        className="truncate text-center text-[10px] font-medium text-slate-400 sm:text-xs"
                        title={`${item}: ${normalized[index]}`}
                     >
                        {item}
                     </span>
                  ))}
               </div>
            </>
         )}
      </div>
   );
};

export default LineChart;

import { Progress } from "@material-tailwind/react";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";

export interface BreakdownItem {
   label: string;
   count: number;
   percent: number;
   code?: string | null;
}

interface Props {
   items: BreakdownItem[];
   title: string;
   emptyTitle: string;
   emptyDescription: string;
   limit?: number;
}

const BreakdownList = ({
   items,
   title,
   emptyTitle,
   emptyDescription,
   limit,
}: Props) => {
   const values = typeof limit === "number" ? items.slice(0, limit) : items;

   return (
      <PanelCard title={title}>
         {values.length === 0 ? (
            <EmptyState title={emptyTitle} description={emptyDescription} />
         ) : (
            values.map((item) => (
               <div key={item.label} className="my-3">
                  <div className="flex items-center justify-between gap-3">
                     <p className="min-w-0 truncate text-sm font-medium text-slate-800">
                        {item.label}
                     </p>
                     <p className="shrink-0 text-sm text-slate-600">
                        <span>{Math.round(item.percent)}%</span>
                        <span className="pl-4">{item.count}</span>
                     </p>
                  </div>
                  <Progress value={Math.min(100, Math.round(item.percent))} />
               </div>
            ))
         )}
      </PanelCard>
   );
};

export default BreakdownList;

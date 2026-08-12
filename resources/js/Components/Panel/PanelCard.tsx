import { FC, ReactNode } from "react";

interface Props {
   children: ReactNode;
   title?: string;
   description?: string;
   actions?: ReactNode;
   className?: string;
   bodyClassName?: string;
   noPadding?: boolean;
}

const PanelCard: FC<Props> = ({
   children,
   title,
   description,
   actions,
   className = "",
   bodyClassName = "",
   noPadding = false,
}) => {
   const hasHeader = Boolean(title || description || actions);

   return (
      <div
         className={`rounded-xl border border-slate-200 bg-white shadow-sm ${className}`}
      >
         {hasHeader && (
            <div className="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
               <div className="min-w-0">
                  {title && (
                     <h2 className="text-base font-semibold text-slate-900">
                        {title}
                     </h2>
                  )}
                  {description && (
                     <p className="mt-0.5 text-sm text-slate-600">
                        {description}
                     </p>
                  )}
               </div>
               {actions && (
                  <div className="flex shrink-0 flex-wrap items-center gap-2">
                     {actions}
                  </div>
               )}
            </div>
         )}
         <div className={`${noPadding ? "" : "p-5 sm:p-6"} ${bodyClassName}`}>
            {children}
         </div>
      </div>
   );
};

export default PanelCard;

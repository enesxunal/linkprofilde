import { FC, ReactNode } from "react";

interface Props {
   title: string;
   description?: string;
   actions?: ReactNode;
   breadcrumb?: ReactNode;
   className?: string;
}

const PageHeader: FC<Props> = ({
   title,
   description,
   actions,
   breadcrumb,
   className = "",
}) => {
   return (
      <div
         className={`flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between ${className}`}
      >
         <div className="min-w-0">
            {breadcrumb && <div className="mb-2">{breadcrumb}</div>}
            <h1 className="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
               {title}
            </h1>
            {description && (
               <p className="mt-1 text-sm text-slate-600">{description}</p>
            )}
         </div>
         {actions && (
            <div className="flex shrink-0 flex-wrap items-center gap-2">
               {actions}
            </div>
         )}
      </div>
   );
};

export default PageHeader;

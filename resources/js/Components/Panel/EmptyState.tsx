import { FC, ReactNode } from "react";

interface Props {
   title: string;
   description?: string;
   action?: ReactNode;
   icon?: ReactNode;
   className?: string;
}

const EmptyState: FC<Props> = ({
   title,
   description,
   action,
   icon,
   className = "",
}) => {
   return (
      <div
         className={`flex flex-col items-center justify-center px-6 py-12 text-center ${className}`}
      >
         {icon && (
            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-slate-50 text-slate-500">
               {icon}
            </div>
         )}
         <p className="text-base font-semibold text-slate-900">{title}</p>
         {description && (
            <p className="mt-1 max-w-sm text-sm text-slate-600">{description}</p>
         )}
         {action && <div className="mt-5">{action}</div>}
      </div>
   );
};

export default EmptyState;

import { FC, ReactNode } from "react";

type BadgeVariant = "default" | "info" | "success" | "warning" | "danger";

interface Props {
   children: ReactNode;
   variant?: BadgeVariant;
   className?: string;
}

const variantClasses: Record<BadgeVariant, string> = {
   default: "bg-slate-100 text-slate-700 border-slate-200",
   info: "bg-blue-50 text-blue-700 border-blue-100",
   success: "bg-green-50 text-green-700 border-green-100",
   warning: "bg-amber-50 text-amber-800 border-amber-100",
   danger: "bg-red-50 text-red-700 border-red-100",
};

const Badge: FC<Props> = ({
   children,
   variant = "default",
   className = "",
}) => {
   return (
      <span
         className={`inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium ${variantClasses[variant]} ${className}`}
      >
         {children}
      </span>
   );
};

export default Badge;

import { FC, ReactNode } from "react";

type AlertVariant = "info" | "warning" | "danger" | "success";

interface Props {
   children: ReactNode;
   variant?: AlertVariant;
   className?: string;
}

const variantClasses: Record<AlertVariant, string> = {
   info: "border-blue-200 bg-blue-50 text-blue-800",
   warning: "border-amber-200 bg-amber-50 text-amber-900",
   danger: "border-red-200 bg-red-50 text-red-700",
   success: "border-green-200 bg-green-50 text-green-800",
};

const AlertBanner: FC<Props> = ({
   children,
   variant = "info",
   className = "",
}) => {
   return (
      <div
         role="alert"
         className={`rounded-xl border px-4 py-3 text-sm ${variantClasses[variant]} ${className}`}
      >
         {children}
      </div>
   );
};

export default AlertBanner;

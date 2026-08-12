import { ReactNode, FC } from "react";
import { usePage } from "@inertiajs/react";
import MobileSidebar from "./MobileSidebar";
import DashboardNavbar from "./DashboardNavbar";
import { error, warning, success } from "@/utils/toast";
import { PageProps } from "@/types";
import AlertBanner from "@/Components/Panel/AlertBanner";

interface Props {
   children: ReactNode;
}
const Dashboard: FC<Props> = ({ children }) => {
   const { props } = usePage<PageProps>();

   if (props.flash.error) error(props.flash.error);
   if (props.flash.warning) warning(props.flash.warning);
   if (props.flash.success) success(props.flash.success);

   return (
      <main className="dashboard-shell flex h-screen overflow-hidden bg-slate-50">
         <MobileSidebar />
         <div className="flex min-w-0 flex-1 flex-col overflow-hidden">
            <DashboardNavbar />
            <div className="min-h-0 flex-1 overflow-y-auto overflow-x-hidden">
               <div className="mx-auto w-full max-w-[1200px] space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                  {props.next_payment && (
                     <AlertBanner variant="danger" className="text-center">
                        <p>
                           Yor subscription limit is over now. Please renew your
                           subscription or update your curren subscription plan.{" "}
                           <a
                              className="font-medium underline"
                              href={`/current-plan/selected/${props.auth.user.pricing_plan_id}?type=${props.auth.user.recurring}`}
                           >
                              Click here
                           </a>
                        </p>
                     </AlertBanner>
                  )}
                  {children}
               </div>
            </div>
         </div>
      </main>
   );
};

export default Dashboard;

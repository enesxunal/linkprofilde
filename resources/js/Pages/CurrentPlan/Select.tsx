import {
   Tab,
   Tabs,
   TabsBody,
   TabPanel,
   TabsHeader,
   Button,
} from "@material-tailwind/react";
import { ReactNode } from "react";
import { Head } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import Breadcrumb from "@/Components/Breadcrumb";
import Pricing from "@/Components/Icons/Pricing";
import { PageProps, PlanProps } from "@/types";
import BadgeCheck from "@/Components/Icons/BadgeCheck";
import PricingPlanCard from "@/Components/PricingPlan/PricingPlanCard";

interface Props extends PageProps {
   plans: PlanProps[];
}

const Select = (props: Props) => {
   const { auth, plans } = props;

   return (
      <>
         <Head title="Plan Seç" />
         <Breadcrumb Icon={Pricing} title="Plan Seç" />

         <div className="card p-3">
            <Tabs value="monthly">
               <TabsHeader
                  className="bg-transparent max-w-[200px] w-full mx-auto mt-4 mb-3"
                  indicatorProps={{ className: "bg-blue-500 text-white" }}
               >
                  <Tab
                     value="monthly"
                     className="py-2 transition-colors duration-300"
                     activeClassName="text-white"
                  >
                     Aylık
                  </Tab>
                  <Tab
                     value="yearly"
                     className="py-2 transition-colors duration-300"
                     activeClassName="text-white"
                  >
                     Yıllık
                  </Tab>
               </TabsHeader>
               <TabsBody>
                  <TabPanel value="monthly">
                     <div className="grid grid-cols-1 md:grid-cols-3 gap-7">
                        {plans.map((plan) => (
                           <PricingPlanCard
                              key={plan.id}
                              plan={plan}
                              type="Monthly"
                           />
                        ))}
                     </div>
                  </TabPanel>
                  <TabPanel value="yearly">
                     <div className="grid grid-cols-1 md:grid-cols-3 gap-7">
                        {plans.map((plan) => (
                           <PricingPlanCard
                              key={plan.id}
                              plan={plan}
                              type="Yearly"
                           />
                        ))}
                     </div>
                  </TabPanel>
               </TabsBody>
            </Tabs>
         </div>
      </>
   );
};

Select.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Select;

import { PageProps, PlanProps } from "@/types";
import BadgeCheck from "../Icons/BadgeCheck";
import { Button } from "@material-tailwind/react";
import { usePage } from "@inertiajs/react";
import BasicPlanSelect from "./BasicPlanSelect";

interface Props {
   plan: PlanProps;
   type: "Monthly" | "Yearly";
}

const PricingPlanCard = (props: Props) => {
   const page = usePage();
   const { plan, type } = props;
   const { auth } = page.props as PageProps;

   const features = [
      `${plan.biolinks} Profil Link Oluşturma`,
      `${plan.shortlinks} Kısa Link Oluşturma`,
      `${plan.qrcodes} QR Kod Oluşturma`,
      `${plan.themes} Temalara Erişim
      `,
      plan.custom_theme
         ? "Özel Tema Oluşturmaya İzin Ver"
         : "Özel Tema Oluşturmaya İzin Verilmez",
      `${plan.support} Hours Support`,
   ];

   let badgeStyle = "";
   if (plan.name === "BASIC") {
      badgeStyle = "bg-gray-100 text-gray-900";
   } else if (plan.name === "STANDARD") {
      badgeStyle = "bg-blue-100 text-blue-500";
   } else {
      badgeStyle = "bg-green-100 text-green-500";
   }

   return (
      <div className="card group">
         <div className="p-6 border-b-2 border-gray-300">
            <span
               className={`text-xs px-2 py-0.5 font-medium rounded-full ${badgeStyle}`}
            >
               {plan.name}
            </span>

            {plan.name === "BASIC" ? (
               <p className="font-medium text-gray-700 mt-3 mb-2">
                  <span className="text-[40px] font-bold text-gray-900">
                     Ücretsiz
                  </span>
               </p>
            ) : (
               <>
                  <p className="font-medium text-gray-700 mt-3 mb-2">
                     <span className="text-[40px] font-bold text-gray-900">
                        {type === "Monthly"
                           ? plan.monthly_price
                           : plan.yearly_price}
                     </span>
                     {type === "Monthly"
                        ? ` ${plan.currency} Aylık`
                        : ` ${plan.currency} Yıllık`}
                  </p>
               </>
            )}

            <p className="text-sm text-gray-700 mt-1">
            Bireysel tasarımcı ve geliştirici için.
            </p>
         </div>

         <div className="p-6">
            {features.map((item, ind) => (
               <div
                  key={ind}
                  className="flex items-center text-gray-700 mb-4 last:mb-0"
               >
                  <BadgeCheck className="w-4 h-4 mr-2 text-blue-500" />
                  <small>{item}</small>
               </div>
            ))}

            {auth.user.roles[0].name === "SUPER-ADMIN" ? (
               <Button
                  color="blue"
                  variant="gradient"
                  className="w-full mt-4 py-2.5 px-1 rounded-md font-medium capitalize text-sm hover:shadow-md"
               >
                 Plan Güncelle
               </Button>
            ) : (
               <>
                  {plan.name === "BASIC" ? (
                     <BasicPlanSelect id={plan.id} />
                  ) : (
                     <a
                        href={`/current-plan/selected/${plan.id}?type=${
                           type === "Monthly" ? "monthly" : "yearly"
                        }`}
                     >
                        <Button
                           color="blue"
                           variant="gradient"
                           className="w-full mt-4 py-2.5 px-1 rounded-md font-medium capitalize text-sm hover:shadow-md"
                        >
                           Plan Güncelle
                        </Button>
                     </a>
                  )}
               </>
            )}
         </div>
      </div>
   );
};

export default PricingPlanCard;

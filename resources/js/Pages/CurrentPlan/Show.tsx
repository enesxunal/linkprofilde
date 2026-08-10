import { ReactNode } from "react";
import Dashboard from "@/Layouts/Dashboard";
import { Head, Link } from "@inertiajs/react";
import Breadcrumb from "@/Components/Breadcrumb";
import Pricing from "@/Components/Icons/Pricing";
import { PageProps, PlanProps } from "@/types";
import ListCheck from "@/Components/Icons/ListCheck";
import { Button } from "@material-tailwind/react";
import BadgeCheck from "@/Components/Icons/BadgeCheck";

interface Props extends PageProps {
   plan: PlanProps;
}

const Show = (props: Props) => {
   const { auth, plan } = props;

   const features = [
      `${plan.biolinks} Profil Link Oluşturma`,     
      `${plan.shortlinks} Kısa Link Oluşturma`,
      `${plan.qrcodes} QR Kod Oluşturma`,
      `${plan.themes} Temalara Erişim`,
      plan.custom_theme
         ? "Özel Tema Oluşturulabilir"
         : "Özel Tema Oluşturulamaz",
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
      <>
         <Head title="Mevcut Plan" />
         <Breadcrumb Icon={Pricing} title="Mevcut Plan" />

         <div className="max-w-md mx-auto pt-6">
            <div className="card group relative outline outline-3 outline-blue-500">
               <div className="absolute -top-4 left-0 right-0 flex justify-center">
                  <span className="py-1.5 px-[11px] bg-blue-500 rounded-full text12 text-white">
                     Mevcut Plan
                  </span>
               </div>

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
                        {auth.user.recurring === "monthly" ? (
                           <p className="font-medium text-gray-700 mt-3 mb-2">
                              <span className="text-[40px] font-bold text-gray-900">
                                 {plan.monthly_price}
                              </span>
                              {` ${plan.currency} Monthly`}
                           </p>
                        ) : (
                           <p className="font-medium text-gray-700 mt-3 mb-2">
                              <span className="text-[40px] font-bold text-gray-900">
                                 {plan.yearly_price}
                              </span>
                              {` ${plan.currency} Yearly`}
                           </p>
                        )}
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
                     <Link href={route("plan.select")}>
                        <Button
                           color="blue"
                           variant="gradient"
                           className="w-full mt-4 py-2.5 px-1 rounded-md font-medium capitalize text-sm hover:shadow-md"
                        >
                           Plan Güncelle
                        </Button>
                     </Link>
                  )}
               </div>
            </div>
         </div>
      </>
   );
};

Show.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Show;

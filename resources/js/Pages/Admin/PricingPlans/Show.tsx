import {
   Tab,
   Tabs,
   Button,
   TabsBody,
   TabPanel,
   TabsHeader,
} from "@material-tailwind/react";
import { ReactNode } from "react";
import { Head, Link } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import Breadcrumb from "@/Components/Breadcrumb";
import Pricing from "@/Components/Icons/Pricing";
import { PageProps, PlanProps } from "@/types";
import BadgeCheck from "@/Components/Icons/BadgeCheck";

interface Props extends PageProps {
   plans: PlanProps[];
}

const Show = (props: Props) => {
   const { auth, plans } = props;

   return (
      <>
         <Head title="Fiyatlandırma Planları" />
         <Breadcrumb
            Icon={Pricing}
            title="Fiyatlandırma Planları"
            Component={
               <Link href="/admin/pricing-plans/create">
                  <Button
                     color="blue"
                     variant="gradient"
                     className="py-2.5 px-5 rounded-md font-medium capitalize text-sm hover:shadow-md"
                  >
                    Yeni Fiyat Planı Oluştur
                  </Button>
               </Link>
            }
         />

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
                     {plans.map((plan, ind) => {
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
                           <div key={ind} className="card group">
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
                                             {plan.monthly_price}
                                          </span>
                                          {` ${plan.currency} Monthly`}
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

                                 <Link
                                    href={`/admin/pricing-plans/update/${plan.id}`}
                                 >
                                    <Button
                                       color="blue"
                                       variant="gradient"
                                       className="w-full mt-4 py-2.5 px-1 rounded-md font-medium capitalize text-sm hover:shadow-md"
                                    >
                                       Planı Düzenle
                                    </Button>
                                 </Link>
                              </div>
                           </div>
                        );
                     })}
                  </div>
               </TabPanel>
               <TabPanel value="yearly">
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-7">
                     {plans.map((plan, ind) => {
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
                           <div key={ind} className="card group">
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
                                             {plan.yearly_price}
                                          </span>
                                          {` ${plan.currency} Yearly`}
                                       </p>
                                    </>
                                 )}

                                 <p className="text-sm text-gray-700 mt-1">
                                 Standart kullanım için standart plan.
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

                                 <Link
                                    href={`/admin/pricing-plans/update/${plan.id}`}
                                 >
                                    <Button
                                       color="blue"
                                       variant="gradient"
                                       className="w-full mt-4 py-2.5 px-1 rounded-md font-medium capitalize text-sm hover:shadow-md"
                                    >
                                       Planı Güncelle
                                    </Button>
                                 </Link>
                              </div>
                           </div>
                        );
                     })}
                  </div>
               </TabPanel>
            </TabsBody>
         </Tabs>
      </>
   );
};

Show.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Show;

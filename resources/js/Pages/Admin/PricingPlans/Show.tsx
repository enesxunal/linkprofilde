import { ReactNode } from "react";
import { Head, Link } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import { PageProps, PlanProps } from "@/types";
import BadgeCheck from "@/Components/Icons/BadgeCheck";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import Badge from "@/Components/Panel/Badge";
import {
   Tab,
   Tabs,
   TabsBody,
   TabPanel,
   TabsHeader,
} from "@material-tailwind/react";

interface Props extends PageProps {
   plans: PlanProps[];
}

const planBadge = (name: string) => {
   if (name === "BASIC") return "default" as const;
   if (name === "STANDARD") return "info" as const;
   return "success" as const;
};

const PlanCard = ({
   plan,
   period,
}: {
   plan: PlanProps;
   period: "monthly" | "yearly";
}) => {
   const features = [
      `${plan.biolinks} Profil Link Oluşturma`,
      `${plan.shortlinks} Kısa Link Oluşturma`,
      `${plan.qrcodes} QR Kod Oluşturma`,
      `${plan.themes} Temalara Erişim`,
      plan.custom_theme
         ? "Özel Tema Oluşturulabilir"
         : "Özel Tema Oluşturulamaz",
   ];

   const price =
      plan.name === "BASIC"
         ? "Ücretsiz"
         : period === "monthly"
         ? `${plan.monthly_price} ${plan.currency}`
         : `${plan.yearly_price} ${plan.currency}`;

   return (
      <PanelCard noPadding>
         <div className="border-b border-slate-200 p-6">
            <Badge variant={planBadge(plan.name)}>{plan.name}</Badge>
            <p className="mt-3 text-3xl font-bold tracking-tight text-slate-900">
               {price}
            </p>
            {plan.name !== "BASIC" && (
               <p className="mt-1 text-sm text-slate-500">
                  {period === "monthly" ? "Aylık" : "Yıllık"}
               </p>
            )}
            <p className="mt-2 text-sm text-slate-600">
               Bireysel kullanım için plan özellikleri.
            </p>
         </div>
         <div className="p-6">
            {features.map((item, ind) => (
               <div
                  key={ind}
                  className="mb-3 flex items-center text-sm text-slate-700 last:mb-0"
               >
                  <BadgeCheck className="mr-2 h-4 w-4 text-blue-600" />
                  {item}
               </div>
            ))}
            <Link
               href={`/admin/pricing-plans/update/${plan.id}`}
               className="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
            >
               Planı Düzenle
            </Link>
         </div>
      </PanelCard>
   );
};

const Show = (props: Props) => {
   const { plans } = props;

   return (
      <>
         <Head title="Fiyatlandırma Planları" />
         <PageHeader
            title="Fiyatlandırma Planları"
            description="Plan limitlerini ve fiyatları yönetin."
            actions={
               <Link
                  href="/admin/pricing-plans/create"
                  className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
               >
                  Yeni Fiyat Planı Oluştur
               </Link>
            }
         />

         <Tabs value="monthly">
            <TabsHeader
               className="mx-auto mb-4 w-full max-w-[220px] rounded-lg border border-slate-200 bg-slate-50 p-1"
               indicatorProps={{
                  className: "bg-white shadow-sm rounded-md",
               }}
            >
               <Tab
                  value="monthly"
                  className="rounded-md py-2 text-sm font-medium text-slate-600"
                  activeClassName="text-slate-900"
               >
                  Aylık
               </Tab>
               <Tab
                  value="yearly"
                  className="rounded-md py-2 text-sm font-medium text-slate-600"
                  activeClassName="text-slate-900"
               >
                  Yıllık
               </Tab>
            </TabsHeader>
            <TabsBody>
               <TabPanel value="monthly" className="px-0">
                  <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                     {plans.map((plan, ind) => (
                        <PlanCard key={ind} plan={plan} period="monthly" />
                     ))}
                  </div>
               </TabPanel>
               <TabPanel value="yearly" className="px-0">
                  <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                     {plans.map((plan, ind) => (
                        <PlanCard key={ind} plan={plan} period="yearly" />
                     ))}
                  </div>
               </TabPanel>
            </TabsBody>
         </Tabs>
      </>
   );
};

Show.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Show;

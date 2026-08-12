import { ReactNode } from "react";
import Dashboard from "@/Layouts/Dashboard";
import { Head, Link } from "@inertiajs/react";
import { PageProps, PlanProps } from "@/types";
import BadgeCheck from "@/Components/Icons/BadgeCheck";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import Badge from "@/Components/Panel/Badge";

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

   const badgeVariant =
      plan.name === "BASIC"
         ? ("default" as const)
         : plan.name === "STANDARD"
         ? ("info" as const)
         : ("success" as const);

   const priceLabel =
      plan.name === "BASIC"
         ? "Ücretsiz"
         : auth.user.recurring === "monthly"
         ? `${plan.monthly_price} ${plan.currency}`
         : `${plan.yearly_price} ${plan.currency}`;

   return (
      <>
         <Head title="Mevcut Plan" />
         <PageHeader
            title="Mevcut Plan"
            description="Aktif abonelik planınız ve limitleriniz."
         />

         <div className="mx-auto max-w-md">
            <PanelCard
               noPadding
               className="relative ring-2 ring-blue-600"
            >
               <div className="absolute -top-3 left-0 right-0 flex justify-center">
                  <Badge variant="info" className="bg-blue-600 text-white border-blue-600">
                     Mevcut Plan
                  </Badge>
               </div>

               <div className="border-b border-slate-200 p-6 pt-8">
                  <Badge variant={badgeVariant}>{plan.name}</Badge>
                  <p className="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                     {priceLabel}
                  </p>
                  {plan.name !== "BASIC" && (
                     <p className="mt-1 text-sm text-slate-500">
                        {auth.user.recurring === "monthly" ? "Aylık" : "Yıllık"}
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

                  {auth.user.roles[0].name === "SUPER-ADMIN" ? (
                     <button
                        type="button"
                        className="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                     >
                        Plan Güncelle
                     </button>
                  ) : (
                     <Link
                        href={route("plan.select")}
                        className="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                     >
                        Plan Güncelle
                     </Link>
                  )}
               </div>
            </PanelCard>
         </div>
      </>
   );
};

Show.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Show;

import Dashboard from "@/Layouts/Dashboard";
import { Head, Link, router } from "@inertiajs/react";
import Delete from "@/Components/Icons/Delete";
import EditFill from "@/Components/Icons/EditPen";
import { CustomPageProps } from "@/types";
import { ReactNode } from "react";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";
import Page from "@/Components/Icons/Page";

const Show = ({ custom_pages }: { custom_pages: CustomPageProps[] }) => {
   return (
      <>
         <Head title="Sayfa Yönetimi" />
         <PageHeader
            title="Sayfa Yönetimi"
            description="Özel sayfaları oluşturun ve düzenleyin."
            actions={
               <Link
                  href={route("custom-page.create")}
                  className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
               >
                  Yeni Sayfa Oluştur
               </Link>
            }
         />

         {custom_pages.length === 0 ? (
            <PanelCard>
               <EmptyState
                  icon={<Page className="h-6 w-6" />}
                  title="Özel sayfa yok"
                  description="İlk özel sayfanızı oluşturarak başlayın."
                  action={
                     <Link
                        href={route("custom-page.create")}
                        className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                     >
                        Yeni Sayfa Oluştur
                     </Link>
                  }
               />
            </PanelCard>
         ) : (
            <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
               {custom_pages.map((item) => (
                  <PanelCard key={item.id}>
                     <div className="mb-4 flex items-center gap-2">
                        <Link
                           href={route("custom-page.update", item.id)}
                           className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100"
                        >
                           <EditFill className="h-4 w-4" />
                        </Link>
                        <button
                           type="button"
                           className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100"
                           onClick={() =>
                              router.delete(
                                 route("custom-page.delete", item.id)
                              )
                           }
                        >
                           <Delete className="h-4 w-4" />
                        </button>
                     </div>
                     <p className="text-base font-semibold text-slate-900">
                        {item.name}
                     </p>
                     <p className="mt-1 text-sm text-slate-500">{item.route}</p>
                  </PanelCard>
               ))}
            </div>
         )}
      </>
   );
};

Show.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Show;

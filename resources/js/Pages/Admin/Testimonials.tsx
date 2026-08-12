import { ReactNode } from "react";
import { Head } from "@inertiajs/react";
import { TestimonialProps } from "@/types";
import Dashboard from "@/Layouts/Dashboard";
import CreateTestimonial from "@/Components/Testimonial/CreateTestimonial";
import DeleteByInertia from "@/Components/DeleteByInertia";
import Delete from "@/Components/Icons/Delete";
import EditTestimonial from "@/Components/Testimonial/EditTestimonial";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";
import Chat from "@/Components/Icons/Chat";

interface Props {
   testimonials: TestimonialProps[];
}

const Testimonials = ({ testimonials }: Props) => {
   return (
      <>
         <Head title="Müşteri Yorumları" />
         <PageHeader
            title="Müşteri Yorumları"
            description="Ana sayfada gösterilen müşteri yorumlarını yönetin."
            actions={<CreateTestimonial />}
         />

         {testimonials.length === 0 ? (
            <PanelCard>
               <EmptyState
                  icon={<Chat className="h-6 w-6" />}
                  title="Henüz yorum yok"
                  description="İlk müşteri yorumunu ekleyerek başlayın."
                  action={<CreateTestimonial />}
               />
            </PanelCard>
         ) : (
            <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
               {testimonials.map((item) => (
                  <PanelCard key={item.id} noPadding bodyClassName="relative p-6 pt-14 text-center">
                     <div className="absolute right-3 top-3 flex items-center gap-2">
                        <EditTestimonial testimonial={item} />
                        <DeleteByInertia
                           apiPath={`/admin/testimonials/delete/${item.id}`}
                           Component={
                              <button
                                 type="button"
                                 className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100"
                              >
                                 <Delete className="h-4 w-4" />
                              </button>
                           }
                        />
                     </div>
                     <img
                        src={`/${item.thumbnail}`}
                        className="absolute left-1/2 top-0 h-20 w-20 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white object-cover shadow-sm"
                        alt=""
                     />
                     <p className="mt-2 text-sm text-slate-600">
                        {item.testimonial}
                     </p>
                     <div className="my-4 border-t border-slate-200" />
                     <p className="text-base font-semibold text-blue-700">
                        {item.name}
                     </p>
                     <p className="text-sm text-slate-500">{item.title}</p>
                  </PanelCard>
               ))}
            </div>
         )}
      </>
   );
};

Testimonials.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Testimonials;

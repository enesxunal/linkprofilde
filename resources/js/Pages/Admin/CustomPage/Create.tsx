import { useState } from "react";
import Dashboard from "@/Layouts/Dashboard";
import Input from "@/Components/Input";
import { Head, useForm } from "@inertiajs/react";
import PageHeader from "@/Components/Panel/PageHeader";
import NativeRichTextEditor from "@/Components/NativeRichTextEditor";

const Create = () => {
   const [validRoute, setValidRoute] = useState(true);

   const { data, setData, post, errors, clearErrors, processing } = useForm({
      name: "",
      route: "",
      content: "",
   });

   const onHandleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
      const { name, value } = event.target;

      if (name === "route") {
         const normalized = value.toLowerCase().replace(/\s+/g, "-");
         setData(name, normalized);

         if (normalized.length > 0) {
            setValidRoute(/^[a-z]+(-[a-z]+)*$/.test(normalized));
         } else {
            setValidRoute(true);
         }
      } else {
         setData("name", value);
      }
   };

   const submit = (e: React.FormEvent<HTMLFormElement>) => {
      e.preventDefault();
      if (!validRoute || processing) return;

      clearErrors();
      post(route("custom-page.store"));
   };

   return (
      <>
         <Head title="Özel Sayfa Oluştur" />
         <PageHeader
            title="Özel Sayfa Oluştur"
            description="Kurumsal içerik, bilgilendirme veya kampanya sayfası oluşturun."
         />

         <div className="card mx-auto w-full max-w-[1200px]">
            <div className="border-b border-slate-200 px-5 pb-4 pt-5 sm:px-6">
               <p className="text-lg font-semibold text-slate-900">Yeni Sayfa</p>
               <p className="mt-1 text-sm text-slate-500">
                  Sayfa adresi /app/sayfa-yolu biçiminde yayınlanır.
               </p>
            </div>
            <form onSubmit={submit} className="p-5 sm:p-6">
               <div className="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                  <Input
                     type="text"
                     fullWidth
                     name="name"
                     value={data.name}
                     error={errors.name}
                     placeholder="Örn: Hakkımızda"
                     onChange={onHandleChange}
                     label="Sayfa Adı"
                     required
                  />

                  <Input
                     type="text"
                     fullWidth
                     name="route"
                     value={data.route}
                     error={
                        errors.route ||
                        (!validRoute
                           ? "Sayfa yolu yalnızca küçük harf ve tire içerebilir."
                           : "")
                     }
                     placeholder="hakkimizda"
                     onChange={onHandleChange}
                     label="Sayfa Yolu"
                     required
                  />
               </div>

               <div>
                  <label className="mb-2 flex items-center text-sm font-medium text-slate-700">
                     Sayfa İçeriği <span className="ml-1 text-red-600">*</span>
                  </label>
                  <NativeRichTextEditor
                     value={data.content}
                     onChange={(html) => setData("content", html)}
                     error={errors.content}
                  />
               </div>

               <div className="mt-6 flex justify-end">
                  <button
                     type="submit"
                     disabled={!validRoute || processing || !data.content.trim()}
                     className="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                  >
                     {processing ? "Oluşturuluyor…" : "Sayfayı Oluştur"}
                  </button>
               </div>
            </form>
         </div>
      </>
   );
};

Create.layout = (page: React.ReactNode) => <Dashboard children={page} />;

export default Create;

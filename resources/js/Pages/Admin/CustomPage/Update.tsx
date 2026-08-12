import { useState } from "react";
import "katex/dist/katex.min.css";
import "react-quill/dist/quill.snow.css";
import Dashboard from "@/Layouts/Dashboard";
import Input from "@/Components/Input";
import { Head, useForm } from "@inertiajs/react";
import ReactQuill from "react-quill";
import { formats } from "@/utils/utils";
import CustomToolbar from "@/Components/CustomToolbar";
import { CustomPageProps } from "@/types";
import PageHeader from "@/Components/Panel/PageHeader";
import katex from "katex";
window.katex = katex;

const Update = ({ custom_page }: { custom_page: CustomPageProps }) => {
   const [validRoute, setValidRoute] = useState(true);
   const modules = { toolbar: { container: "#toolbar" } };

   const { data, setData, put, errors, clearErrors } = useForm({
      name: custom_page.name ?? "",
      route: custom_page.route ?? "",
      content: custom_page.content ?? "",
   });

   const onHandleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
      const { name, value } = event.target;

      if (name === "route") {
         setData(name, value);

         if (value.length > 0) {
            const regex = /^[a-z]+(-[a-z]+)*$/;
            const isValidInput = regex.test(value);

            setValidRoute(isValidInput);
         } else {
            setValidRoute(true);
         }
      } else {
         setData(name as "name" | "content", value);
      }
   };

   const submit = (e: React.FormEvent<HTMLFormElement>) => {
      e.preventDefault();
      if (validRoute) {
         clearErrors();
         put(route("custom-page.save", custom_page.id));
      }
   };

   return (
      <>
         <Head title="Özel Sayfa Güncelle" />
         <PageHeader
            title="Özel Sayfa Güncelle"
            description="Özel sayfa içeriğini ve rotasını düzenleyin."
         />

         <div className="card mx-auto w-full max-w-[1200px]">
            <div className="border-b border-slate-200 px-5 pb-4 pt-5 sm:px-6">
               <p className="text-lg font-semibold text-slate-900">
                  Sayfayı Güncelle
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
                     placeholder="Sayfa adı"
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
                        errors.route ?? !validRoute
                           ? "Route yalnızca küçük harf ve tire (-) içerebilir"
                           : ""
                     }
                     placeholder="ornek-sayfa"
                     onChange={onHandleChange}
                     label="Route"
                     required
                  />
               </div>

               <div>
                  <label className="mb-1.5 flex w-full items-center text-sm font-medium text-slate-700">
                     <span className="mr-1">Sayfa İçeriği</span>
                     <span className="text-red-600">*</span>
                  </label>
                  <div className="rounded-lg border border-slate-200">
                     <CustomToolbar />
                     <ReactQuill
                        modules={modules}
                        formats={formats}
                        value={data.content}
                        onChange={(html) => setData("content", html)}
                        className="page-create border-0"
                     />
                  </div>
               </div>

               <button
                  type="submit"
                  disabled={!validRoute}
                  className="mt-8 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
               >
                  Sayfayı Güncelle
               </button>
            </form>
         </div>
      </>
   );
};

Update.layout = (page: React.ReactNode) => <Dashboard children={page} />;

export default Update;

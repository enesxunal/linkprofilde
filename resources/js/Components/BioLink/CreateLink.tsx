import Input from "../Input";
import { FormEventHandler, useEffect, useState } from "react";
import { Dialog } from "@material-tailwind/react";
import { useForm } from "@inertiajs/react";

const CreateLink = () => {
   const [open, setOpen] = useState(false);

   const handleOpen = () => {
      setOpen((prev) => !prev);
   };

   const { data, setData, post, errors, reset, wasSuccessful, clearErrors } =
      useForm({
         link_name: "",
         link_type: "biolink",
         url_name: "",
      });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const submit: FormEventHandler = async (e) => {
      clearErrors();
      e.preventDefault();

      post("/bio-links/create");
   };

   useEffect(() => {
      if (wasSuccessful) {
         reset();
         handleOpen();
      }
   }, [wasSuccessful]);

   return (
      <>
         <button
            type="button"
            onClick={handleOpen}
            className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
         >
            Link Oluştur
         </button>

         <Dialog
            size="sm"
            open={open}
            handler={handleOpen}
            className="mx-4 max-h-[calc(100vh-80px)] overflow-y-auto rounded-xl border border-slate-200 bg-white p-6 text-slate-800 shadow-sm sm:mx-0"
         >
            <div className="flex items-center justify-between mb-6">
               <p className="text-xl font-medium">Yeni Link Oluştur</p>
               <span
                  onClick={handleOpen}
                  className="text-3xl leading-none cursor-pointer"
               >
                  ×
               </span>
            </div>

            <form onSubmit={submit}>
               <div className="mb-4">
                  <Input
                     type="text"
                     name="link_name"
                     label="Kısa Link Adı"
                     value={data.link_name}
                     error={errors.link_name}
                     onChange={onHandleChange}
                     placeholder="Kısa Link Adını Giriniz "
                     fullWidth
                     required
                  />
               </div>
               <div className="mb-4">
                  <Input
                     type="text"
                     name="url_name"
                     label="Link Kullanıcı Adı"
                     value={data.url_name}
                     error={errors.url_name}
                     onChange={onHandleChange}
                     placeholder="Benzersiz bir kullanıcı adı giriniz"
                     fullWidth
                     required
                  />
               </div>

               <div className="mt-4 flex justify-end gap-2">
                  <button
                     type="button"
                     onClick={handleOpen}
                     className="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                  >
                     İptal
                  </button>
                  <button
                     type="submit"
                     className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                  >
                     Kaydet
                  </button>
               </div>
            </form>
         </Dialog>
      </>
   );
};

export default CreateLink;

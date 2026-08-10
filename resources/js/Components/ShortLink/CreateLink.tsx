import Input from "../Input";
import { FormEventHandler, useEffect, useState } from "react";
import { Button, Dialog } from "@material-tailwind/react";
import { useForm } from "@inertiajs/react";

const CreateLink = () => {
   const [open, setOpen] = useState(false);

   const handleOpen = () => {
      setOpen((prev) => !prev);
   };

   const { data, setData, post, errors, reset, wasSuccessful, clearErrors } =
      useForm({
         link_name: "",
         link_slug: "",
         external_url: "",
         link_type: "shortlink",
      });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const submit: FormEventHandler = async (e) => {
      clearErrors();
      e.preventDefault();

      post("/short-links/create");
   };

   useEffect(() => {
      if (wasSuccessful) {
         reset();
         handleOpen();
      }
   }, [wasSuccessful]);

   return (
      <>
         <Button
            color="blue"
            variant="gradient"
            onClick={handleOpen}
            className="py-2.5 px-5 rounded-md font-medium capitalize text-sm hover:shadow-md flex"
         >
           Kısa Link Oluştur
         </Button>

         <Dialog
            size="sm"
            open={open}
            handler={handleOpen}
            className="p-6 max-h-[calc(100vh-80px)] overflow-y-auto text-gray-800"
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
                     placeholder="Lüttfen kısa link adınızı giriniz"
                     fullWidth
                     required
                  />
               </div>
               <div className="mb-4">
                  <Input
                     type="text"
                     name="link_slug"
                     label="Bağlantı"
                     value={data.link_slug}
                     error={errors.link_slug}
                     onChange={onHandleChange}
                     placeholder="Lütfen benzersiz bir isim giriniz"
                     fullWidth
                  />
               </div>
               <div className="mb-4">
                  <Input
                     type="url"
                     name="external_url"
                     label="External URL"
                     value={data.external_url}
                     error={errors.external_url}
                     onChange={onHandleChange}
                     placeholder="Kısa olacak harici URL'yi girin"
                     fullWidth
                     required
                  />
               </div>

               <div className="flex justify-end mt-4">
                  <Button
                     color="red"
                     variant="text"
                     onClick={handleOpen}
                     className="py-2 font-medium capitalize text-base mr-2"
                  >
                     <span>Çıkış</span>
                  </Button>
                  <Button
                     type="submit"
                     color="blue"
                     variant="gradient"
                     className="py-2 font-medium capitalize text-base"
                  >
                     <span>Değişiklikleri Kaydet</span>
                  </Button>
               </div>
            </form>
         </Dialog>
      </>
   );
};

export default CreateLink;

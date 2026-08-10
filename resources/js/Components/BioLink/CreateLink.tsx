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
         <Button
            color="blue"
            variant="gradient"
            onClick={handleOpen}
            className="py-2.5 px-5 rounded-md font-medium capitalize text-sm hover:shadow-md"
         >
            Link Oluştur
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

               <div className="flex justify-end mt-4">
                  <Button
                     color="red"
                     variant="text"
                     onClick={handleOpen}
                     className="py-2 font-medium capitalize text-base mr-2"
                  >
                     <span>Çıkış Yap</span>
                  </Button>
                  <Button
                     type="submit"
                     color="blue"
                     variant="gradient"
                     className="py-2 font-medium capitalize text-base"
                  >
                     <span>Değişikleri Kaydet</span>
                  </Button>
               </div>
            </form>
         </Dialog>
      </>
   );
};

export default CreateLink;

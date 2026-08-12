import Input from "../Input";
import { FormEventHandler, useEffect, useState } from "react";
import { Avatar, Dialog } from "@material-tailwind/react";
import { useForm } from "@inertiajs/react";
import UserCircle from "../Icons/UserCircle";
import TextArea from "../TextArea";

const CreateTestimonial = () => {
   const [open, setOpen] = useState(false);
   const [imageUrl, setImageUrl] = useState<any>();

   const handleOpen = () => {
      setOpen((prev) => !prev);
   };

   const { data, setData, post, errors, wasSuccessful, clearErrors } = useForm({
      name: "",
      title: "",
      testimonial: "",
      thumbnail: null,
   });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const submit: FormEventHandler = async (e) => {
      clearErrors();
      e.preventDefault();

      post("/admin/testimonials/add");
   };

   const handleImageChange = (e: any) => {
      const files = e.target.files;
      if (files && files[0]) {
         setData("thumbnail", files[0]);
         setImageUrl(URL.createObjectURL(files[0]));
      }
   };

   useEffect(() => {
      if (wasSuccessful) {
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
            Yorum Ekle
         </button>

         <Dialog
            size="sm"
            open={open}
            handler={handleOpen}
            className="mx-4 max-h-[calc(100vh-80px)] overflow-y-auto rounded-xl border border-slate-200 bg-white p-6 text-slate-800 shadow-sm sm:mx-0"
         >
            <div className="mb-6 flex items-center justify-between">
               <p className="text-lg font-semibold text-slate-900">
                  Yeni Müşteri Yorumu
               </p>
               <button
                  type="button"
                  onClick={handleOpen}
                  className="text-2xl leading-none text-slate-400 hover:text-slate-700"
                  aria-label="Kapat"
               >
                  ×
               </button>
            </div>

            <form onSubmit={submit}>
               <div className="mb-4 flex flex-col items-center">
                  {imageUrl ? (
                     <Avatar
                        size="xs"
                        alt="thumbnail"
                        src={imageUrl}
                        variant="circular"
                        className="h-[120px] w-[120px]"
                     />
                  ) : (
                     <UserCircle className="h-[120px] w-[120px] text-slate-400" />
                  )}
                  <div className="mt-4 flex items-center">
                     <label
                        htmlFor="formFileSm"
                        className="cursor-pointer rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                     >
                        Fotoğraf Seç
                     </label>
                     <input
                        hidden
                        required
                        type="file"
                        onChange={handleImageChange}
                        id="formFileSm"
                     />
                  </div>
                  <small className="py-4 text-slate-500">
                     JPG, JPEG, PNG — maksimum 2MB
                  </small>
                  {errors.thumbnail && (
                     <p className="text-sm text-red-600">{errors.thumbnail}</p>
                  )}
               </div>

               <div className="mb-4">
                  <Input
                     type="text"
                     name="name"
                     label="İsim"
                     value={data.name}
                     error={errors.name}
                     onChange={onHandleChange}
                     placeholder="Yorum yapan kişinin adı"
                     fullWidth
                     required
                  />
               </div>
               <div className="mb-4">
                  <Input
                     type="text"
                     name="title"
                     label="Ünvan"
                     value={data.title}
                     error={errors.title}
                     onChange={onHandleChange}
                     placeholder="Ünvan veya rol"
                     fullWidth
                     required
                  />
               </div>
               <div className="mb-4">
                  <TextArea
                     rows={3}
                     cols={10}
                     name="testimonial"
                     label="Yorum"
                     value={data.testimonial}
                     error={errors.testimonial}
                     onChange={onHandleChange}
                     placeholder="Müşteri yorumunu girin"
                     maxLength={180}
                     fullWidth
                     required
                  />
               </div>

               <div className="flex justify-end gap-2 pt-4">
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
                     Oluştur
                  </button>
               </div>
            </form>
         </Dialog>
      </>
   );
};

export default CreateTestimonial;

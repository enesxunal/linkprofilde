import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import { useState, ChangeEvent, FormEvent } from "react";
import { Avatar } from "@material-tailwind/react";
import UserCircle from "@/Components/Icons/UserCircle";
import { AppSettingProps } from "@/types";
import TextArea from "../TextArea";

const AppSettings = (props: { app: AppSettingProps }) => {
   const { title, logo, description, copyright } = props.app;
   const [imageUrl, setImageUrl] = useState(
      `/${logo}` === "/null" ? null : `/${logo}`
   );

   const { data, setData, post, errors, clearErrors } = useForm({
      title,
      logo: null,
      description,
      copyright,
   });

   const onHandleChange = (
      event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
   ) => {
      const target = event.target as HTMLInputElement;
      setData({
         ...data,
         [target.name]: target.value,
      });
   };

   const submit = (e: FormEvent) => {
      e.preventDefault();
      clearErrors();
      post(route("settings.app"));
   };

   const handleImageChange = (e: any) => {
      const files = e.target.files;
      if (files && files[0]) {
         setData("logo", files[0]);
         setImageUrl(URL.createObjectURL(files[0]));
      }
   };

   return (
      <div className="card mx-auto w-full max-w-[1000px]">
         <div className="border-b border-slate-200 px-5 pb-4 pt-5 sm:px-6">
            <p className="text-lg font-semibold text-slate-900">
               Genel Uygulama Ayarları
            </p>
            <p className="mt-0.5 text-sm text-slate-600">
               Logo, başlık, açıklama ve telif bilgilerini yönetin.
            </p>
         </div>

         <form onSubmit={submit} className="p-5 sm:p-6">
            <div className="grid grid-cols-1 gap-7">
               <div className="flex flex-col md:flex-row">
                  <p className="mb-1.5 w-full max-w-[164px] text-sm font-medium text-slate-700">
                     Uygulama Logosu
                  </p>
                  <div>
                     {imageUrl ? (
                        <Avatar
                           src={imageUrl}
                           alt="logo"
                           size="xs"
                           variant="circular"
                           className="h-[100px] w-[100px]"
                        />
                     ) : (
                        <UserCircle className="h-[100px] w-[100px] text-slate-400" />
                     )}
                     <div className="mt-2 flex flex-wrap items-center gap-3">
                        <label
                           htmlFor="formFileSm"
                           className="cursor-pointer rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50"
                        >
                           Fotoğraf Seç
                        </label>
                        <input
                           hidden
                           id="formFileSm"
                           type="file"
                           onChange={handleImageChange}
                        />
                        <small className="text-slate-500">
                           JPG, JPEG, PNG — maksimum 2MB
                        </small>
                     </div>
                     {errors.logo && (
                        <p className="mt-1 text-sm text-red-600">
                           {errors.logo}
                        </p>
                     )}
                  </div>
               </div>
               <Input
                  required
                  flexLabel
                  fullWidth
                  type="text"
                  name="title"
                  label="Uygulama Başlığı"
                  value={data.title}
                  error={errors.title}
                  onChange={onHandleChange}
                  placeholder="Uygulama başlığını girin"
               />

               <Input
                  type="text"
                  name="copyright"
                  label="Telif Metni"
                  value={data.copyright}
                  error={errors.copyright}
                  onChange={onHandleChange}
                  placeholder="Footer’da görünen telif metni"
                  fullWidth
                  flexLabel
                  required
               />

               <TextArea
                  rows={3}
                  cols={10}
                  name="description"
                  label="Uygulama Açıklaması"
                  value={data.description}
                  error={errors.description}
                  onChange={onHandleChange}
                  placeholder="Footer’da görünen açıklama metni"
                  fullWidth
                  flexLabel
                  required
               />
            </div>

            <div className="mt-7 flex items-center md:pl-[164px]">
               <button
                  type="submit"
                  className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
               >
                  Değişiklikleri Kaydet
               </button>
            </div>
         </form>
      </div>
   );
};

export default AppSettings;

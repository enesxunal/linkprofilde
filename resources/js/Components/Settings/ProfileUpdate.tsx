import Input from "@/Components/Input";
import { useState, FormEventHandler } from "react";
import { useForm, usePage } from "@inertiajs/react";
import UserCircle from "@/Components/Icons/UserCircle";
import { Avatar } from "@material-tailwind/react";
import { PageProps } from "@/types";

const ProfileUpdate = () => {
   const { props } = usePage<PageProps>();
   const { name, phone, image } = props.auth.user;
   const [imageUrl, setImageUrl] = useState(
      `/${image}` === "/null" ? null : `/${image}`
   );

   const { data, setData, post, errors, clearErrors } = useForm({
      name: name,
      phone: phone,
      image: null,
   });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const submit: FormEventHandler = (e) => {
      e.preventDefault();
      clearErrors();
      post("/settings/profile");
   };

   const handleImageChange = (e: any) => {
      const files = e.target.files;
      if (files && files[0]) {
         setData("image", files[0]);
         setImageUrl(URL.createObjectURL(files[0]));
      }
   };

   return (
      <div className="card mx-auto w-full max-w-[1000px]">
         <div className="border-b border-slate-200 px-5 pb-4 pt-5 sm:px-6">
            <p className="text-lg font-semibold text-slate-900">Profili Düzenle</p>
            <p className="mt-0.5 text-sm text-slate-600">
               Ad, telefon ve profil fotoğrafınızı güncelleyin.
            </p>
         </div>
         <form onSubmit={submit} className="p-5 sm:p-6">
            <div className="mb-8 flex flex-col md:flex-row">
               <p className="mb-1.5 w-full max-w-[164px] text-sm font-medium text-slate-700">
                  Profil Fotoğrafı
               </p>
               <div>
                  {imageUrl ? (
                     <Avatar
                        src={imageUrl}
                        alt="profile"
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
                  {errors.image && (
                     <p className="mt-1 text-sm text-red-600">{errors.image}</p>
                  )}
               </div>
            </div>

            <div className="mb-6">
               <Input
                  fullWidth
                  type="text"
                  name="name"
                  value={data.name}
                  error={errors.name}
                  placeholder="Adınız ve soyadınız"
                  onChange={onHandleChange}
                  label="Ad Soyad"
                  flexLabel
                  required
               />
            </div>

            <div className="mb-6">
               <Input
                  fullWidth
                  type="text"
                  name="phone"
                  value={data.phone}
                  error={errors.phone}
                  placeholder="Telefon numaranız"
                  onChange={onHandleChange}
                  label="Telefon"
                  flexLabel
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

export default ProfileUpdate;

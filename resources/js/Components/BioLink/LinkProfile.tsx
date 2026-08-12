import axios from "axios";
import { LinkProps } from "@/types";
import UserCircle from "@/Components/Icons/UserCircle";
import Camera from "@/Components/Icons/Camera";
import Input from "@/Components/Input";
import TextArea from "@/Components/TextArea";
import { useForm } from "@inertiajs/react";
import { error } from "@/utils/toast";
import { FormEventHandler, ChangeEvent, useState } from "react";

interface Props {
   link: LinkProps;
   setLink: (state: any) => void;
}

const LinkProfile = (props: Props) => {
   const { link, setLink } = props;
   const { thumbnail, link_name, short_bio } = link;
   const [imageUrl, setImageUrl] = useState(thumbnail ? `/${thumbnail}` : null);

   const { data, setData } = useForm({
      thumbnail: null,
      link_name: link_name || "",
      short_bio: short_bio || "",
   });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const handleImageChange = (e: ChangeEvent<HTMLInputElement>): void => {
      const files = e.target.files;
      if (files && files[0]) {
         setData("thumbnail", files[0] as any);
         setImageUrl(URL.createObjectURL(files[0]));
      }
   };

   const submit: FormEventHandler = async (e) => {
      e.preventDefault();
      const formData: any = new FormData();
      formData.append("thumbnail", data.thumbnail);
      formData.append("link_name", data.link_name);
      formData.append("short_bio", data.short_bio);

      const res = await axios.post(
         `/bio-links/customize/update-profile/${link.id}`,
         formData
      );

      if (res.data.error) {
         error(res.data.error);
      } else if (res.data.success) {
         setLink(res.data.link);
      }
   };

   return (
      <form
         onSubmit={submit}
         className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
      >
         <div className="mb-4">
            <h2 className="text-base font-semibold text-slate-900">Profil</h2>
            <p className="mt-0.5 text-sm text-slate-600">
               Fotoğraf, ad ve kısa bio bilgilerinizi güncelleyin.
            </p>
         </div>
         <div className="mb-6 flex flex-col items-center gap-6 md:flex-row">
            <div className="flex w-full max-w-[120px] items-center justify-center">
               <div className="relative">
                  {imageUrl ? (
                     <img
                        src={`${imageUrl}`}
                        alt="linkdrop"
                        className="h-[120px] w-[120px] rounded-full object-cover"
                     />
                  ) : (
                     <UserCircle className="w-full" />
                  )}
                  <label
                     htmlFor="linkProfile"
                     className="absolute right-1.5 top-1.5 cursor-pointer"
                  >
                     <Camera className="h-6 w-6 text-blue-500" />
                  </label>
                  <input
                     hidden
                     type="file"
                     name="thumbnail"
                     onChange={handleImageChange}
                     id="linkProfile"
                  ></input>
               </div>
            </div>
            <div className="w-full min-w-0">
               <div className="mb-4">
                  <Input
                     type="text"
                     label="Link Adı"
                     name="link_name"
                     value={data.link_name}
                     onChange={onHandleChange}
                     fullWidth
                     required
                  />
               </div>
               <TextArea
                  rows={4}
                  cols={3}
                  label="Kısa Bio"
                  name="short_bio"
                  value={data.short_bio}
                  onChange={onHandleChange}
                  maxLength={200}
                  fullWidth
               />
            </div>
         </div>

         <button
            type="submit"
            className="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
         >
            Kaydet
         </button>
      </form>
   );
};

export default LinkProfile;

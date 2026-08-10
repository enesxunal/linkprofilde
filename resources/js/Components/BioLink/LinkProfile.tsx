import axios from "axios";
import { LinkProps } from "@/types";
import UserCircle from "@/Components/Icons/UserCircle";
import Camera from "@/Components/Icons/Camera";
import Input from "@/Components/Input";
import TextArea from "@/Components/TextArea";
import { useForm } from "@inertiajs/react";
import { error } from "@/utils/toast";
import { FormEventHandler, ChangeEvent, useState } from "react";
import { Button } from "@material-tailwind/react";

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
      <form onSubmit={submit} className="card p-6 mb-7">
         <div className="flex flex-col md:flex-row  items-center gap-6 mb-6">
            <div className="max-w-[120px] w-full flex items-center justify-center">
               <div className="relative">
                  {imageUrl ? (
                     <img
                        src={`${imageUrl}`}
                        alt="linkdrop"
                        className="w-[120px] h-[120px] object-cover rounded-full"
                     />
                  ) : (
                     <UserCircle className="w-full" />
                  )}
                  <label
                     htmlFor="linkProfile"
                     className="absolute top-1.5 right-1.5 cursor-pointer"
                  >
                     <Camera className="text-blue-500 w-6 h-6" />
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
            <div className="w-full">
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

         <Button
            type="submit"
            color="blue"
            variant="gradient"
            className="py-2 w-full font-medium hover:shadow-md capitalize text-base"
         >
            Değişiklikleri Kaydet
         </Button>
      </form>
   );
};

export default LinkProfile;

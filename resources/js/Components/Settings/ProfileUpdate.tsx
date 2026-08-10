import Input from "@/Components/Input";
import { useState, FormEventHandler } from "react";
import { useForm, usePage } from "@inertiajs/react";
import UserCircle from "@/Components/Icons/UserCircle";
import { Avatar, Button } from "@material-tailwind/react";
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
      <div className="card max-w-[1000px] w-full mx-auto mb-7">
         <div className="px-7 pt-7 pb-4 border-b border-b-gray-200">
            <p className="text18 font-bold text-gray-900">Edit Profile</p>
         </div>
         <form onSubmit={submit} className="p-7">
            <div className="flex flex-col md:flex-row mb-8">
               <p className="max-w-[164px] w-full font-medium text-gray-500 mb-1">
                  Profile Picture
               </p>
               <div>
                  {imageUrl ? (
                     <Avatar
                        src={imageUrl}
                        alt="item-1"
                        size="xs"
                        variant="circular"
                        className="h-[100px] w-[100px]"
                     />
                  ) : (
                     <UserCircle className="h-[100px] w-[100px] text-blue-gray-500" />
                  )}
                  <div className="mt-1 flex items-center">
                     <label
                        htmlFor="formFileSm"
                        className="text-xs font-medium text-gray-900 px-2.5 py-1.5 border border-gray-700 bg-gray-100 whitespace-nowrap"
                     >
                        Choose Photo
                     </label>
                     <input
                        hidden
                        id="formFileSm"
                        type="file"
                        onChange={handleImageChange}
                     />
                     <small className="ml-3 text-gray-500">
                        JPG, JPEG, PNG, SVG File, Maximum 2MB
                     </small>
                  </div>
                  {errors.image && (
                     <p className="text-sm text-red-500 mt-1">{errors.image}</p>
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
                  placeholder="Your full name"
                  onChange={onHandleChange}
                  label="Full Name"
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
                  placeholder="Your phone number"
                  onChange={onHandleChange}
                  label="Phone"
                  flexLabel
               />
            </div>

            <div className="flex items-center mt-7 md:pl-[164px]">
               <Button
                  type="submit"
                  color="blue"
                  variant="gradient"
                  className="py-2.5 px-5 rounded-md font-medium capitalize text-sm hover:shadow-md"
               >
                  Save Changes
               </Button>
            </div>
         </form>
      </div>
   );
};

export default ProfileUpdate;

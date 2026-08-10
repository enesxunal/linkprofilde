import axios from "axios";
import Input from "../Input";
import EditPen from "../Icons/EditPen";
import { useForm } from "@inertiajs/react";
import { FormEventHandler, useState } from "react";
import { LinkProps, PaginationProps } from "@/types";
import { Button, Dialog, IconButton } from "@material-tailwind/react";
import { error, success } from "@/utils/toast";

interface Props {
   link: LinkProps;
   links: PaginationProps;
   setLinks: (res: PaginationProps) => void;
}

const EditLink = (props: Props) => {
   const { link, links, setLinks } = props;
   const [open, setOpen] = useState(false);

   const handleOpen = () => {
      setOpen((prev) => !prev);
   };

   const { data, setData } = useForm({
      link_name: link.link_name,
      link_type: "shortlink",
      external_url: link.external_url || "",
   });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const [errors, setErrors] = useState({
      link_name: null,
      external_url: null,
   });

   const submit: FormEventHandler = async (e) => {
      e.preventDefault();
      setErrors({
         link_name: null,
         external_url: null,
      });

      try {
         const res = await axios.patch(`/short-links/update/${link.id}`, data);
         if (res.data.error) {
            error(res.data.error);
         } else if (res.data.success && res.data.link) {
            handleOpen();
            success(res.data.success);

            const updatedLinks = links.data.map((item) => {
               return item.id === res.data.link.id
                  ? { ...item, link_name: res.data.link.link_name }
                  : item;
            });

            setLinks({
               ...links,
               data: updatedLinks,
            });
         }
      } catch (error: any) {
         const { link_name, external_url } = error.response.data.errors;
         if (link_name)
            setErrors((prev: any) => ({ ...prev, link_name: link_name[0] }));
         if (external_url)
            setErrors((prev: any) => ({
               ...prev,
               external_url: external_url[0],
            }));
      }
   };

   return (
      <>
         <IconButton
            variant="text"
            color="white"
            onClick={handleOpen}
            className="w-7 h-7 rounded-full bg-blue-50 hover:bg-blue-50 active:bg-blue-50 text-blue-500"
         >
            <EditPen className="h-4 w-4" />
         </IconButton>

         <Dialog
            size="sm"
            open={open}
            handler={handleOpen}
            className="p-6 max-h-[calc(100vh-80px)] overflow-y-auto text-gray-800"
         >
            <div className="flex items-center justify-between mb-6">
               <p className="text-xl font-medium">Link Güncelle</p>
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
                     placeholder="Enter your bio link name"
                     fullWidth
                     required
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
                     placeholder="Enter external url that will be short"
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
                     <span>Cancel</span>
                  </Button>
                  <Button
                     type="submit"
                     color="blue"
                     variant="gradient"
                     className="py-2 font-medium capitalize text-base"
                  >
                     <span>Save Changes</span>
                  </Button>
               </div>
            </form>
         </Dialog>
      </>
   );
};

export default EditLink;

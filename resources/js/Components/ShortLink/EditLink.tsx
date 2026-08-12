import axios from "axios";
import Input from "../Input";
import EditPen from "../Icons/EditPen";
import { useForm } from "@inertiajs/react";
import { FormEventHandler, useEffect, useState } from "react";
import { LinkProps, PaginationProps } from "@/types";
import { Dialog } from "@material-tailwind/react";
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

   const [errors, setErrors] = useState({
      link_name: null,
      external_url: null,
   });

   useEffect(() => {
      setData({
         link_name: link.link_name,
         link_type: "shortlink",
         external_url: link.external_url || "",
      });
      setErrors({
         link_name: null,
         external_url: null,
      });
   }, [link.id]);

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

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
         <button
            type="button"
            onClick={handleOpen}
            className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100"
            aria-label="Linki düzenle"
         >
            <EditPen className="h-4 w-4" />
         </button>

         <Dialog
            size="sm"
            open={open}
            handler={handleOpen}
            className="mx-4 max-h-[calc(100vh-80px)] overflow-y-auto rounded-xl border border-slate-200 bg-white p-6 text-slate-800 shadow-sm sm:mx-0"
         >
            <div className="mb-6 flex items-center justify-between">
               <p className="text-lg font-semibold text-slate-900">
                  Linki Güncelle
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
               <div className="mb-4">
                  <Input
                     type="text"
                     name="link_name"
                     label="Kısa Link Adı"
                     value={data.link_name}
                     error={errors.link_name}
                     onChange={onHandleChange}
                     placeholder="Kısa link adını girin"
                     fullWidth
                     required
                  />
               </div>
               <div className="mb-4">
                  <Input
                     type="url"
                     name="external_url"
                     label="Harici URL"
                     value={data.external_url}
                     error={errors.external_url}
                     onChange={onHandleChange}
                     placeholder="Kısaltılacak harici URL"
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

export default EditLink;

import axios from "axios";
import { useState } from "react";
import { error } from "@/utils/toast";
import { LinkItemProps } from "@/types";
import { Dialog } from "@material-tailwind/react";
import Delete from "../Icons/Delete";

interface Props {
   block: LinkItemProps;
   setLink: (state: any) => void;
}

const DeleteBlock = (props: Props) => {
   const { block, setLink } = props;
   const [open, setOpen] = useState(false);

   const handleOpen = () => {
      setOpen((prev) => !prev);
   };

   const deleteHandler = async () => {
      const res = await axios.delete(
         `/bio-links/customize/block/delete/${block.id}`
      );

      if (res.data.success) {
         setOpen(false);
         setLink(res.data.link);
      } else if (res.data.error) {
         error(res.data.error);
      }
   };

   return (
      <>
         <button
            type="button"
            onClick={handleOpen}
            className="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50"
            aria-label="Bloğu sil"
         >
            <Delete className="h-4 w-4" />
         </button>

         <Dialog
            size="xs"
            open={open}
            handler={handleOpen}
            className="mx-4 max-h-[calc(100vh-80px)] overflow-y-auto rounded-xl border border-slate-200 bg-white px-6 py-8 text-slate-800 shadow-sm sm:mx-0"
         >
            <p className="mb-8 text-center text-lg font-semibold text-red-600">
               Silmek istediğinize emin misiniz?
            </p>
            <div className="flex flex-wrap items-center justify-center gap-3">
               <button
                  type="button"
                  onClick={handleOpen}
                  className="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
               >
                  İptal
               </button>
               <button
                  type="button"
                  className="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                  onClick={deleteHandler}
               >
                  Sil
               </button>
            </div>
         </Dialog>
      </>
   );
};

export default DeleteBlock;

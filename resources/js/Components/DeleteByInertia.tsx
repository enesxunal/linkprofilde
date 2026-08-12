import { useState } from "react";
import { router } from "@inertiajs/react";
import { Dialog } from "@material-tailwind/react";

interface Props {
   apiPath: string;
   Component: any;
}

const DeleteByInertia = (props: Props) => {
   const { apiPath, Component } = props;
   const [open, setOpen] = useState(false);

   const handleOpen = () => {
      setOpen((prev) => !prev);
   };

   const modifiedComponent = (
      <Component.type {...Component.props} onClick={handleOpen} />
   );

   const deleteHandler = () => {
      handleOpen();
      router.delete(apiPath);
   };

   return (
      <>
         {modifiedComponent}

         <Dialog
            size="xs"
            open={open}
            handler={handleOpen}
            className="mx-4 max-h-[calc(100vh-80px)] overflow-y-auto rounded-xl border border-slate-200 bg-white p-6 text-slate-800 shadow-sm sm:mx-0"
         >
            <h2 className="text-center text-lg font-semibold text-slate-900">
               Silmek istediğinize emin misiniz?
            </h2>
            <p className="mt-2 text-center text-sm text-slate-600">
               Bu işlem geri alınamaz.
            </p>
            <div className="mt-6 flex items-center justify-center gap-3">
               <button
                  type="button"
                  onClick={handleOpen}
                  className="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
               >
                  İptal
               </button>
               <button
                  type="button"
                  onClick={deleteHandler}
                  className="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
               >
                  Sil
               </button>
            </div>
         </Dialog>
      </>
   );
};

export default DeleteByInertia;

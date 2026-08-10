import { useState } from "react";
import { router } from "@inertiajs/react";
import { Button, Dialog } from "@material-tailwind/react";

const BasicPlanSelect = (props: { id: number }) => {
   const [open, setOpen] = useState(false);

   const handleOpen = () => {
      setOpen((prev) => !prev);
   };

   const basicPlanHandler = () => {
      handleOpen();
      router.post(route("plan.basic-plan", props.id));
   };

   return (
      <>
         <Button
            color="blue"
            variant="gradient"
            onClick={handleOpen}
            className="w-full mt-4 py-2.5 px-1 rounded-md font-medium capitalize text-sm hover:shadow-md"
         >
            Planı Güncelle
         </Button>

         <Dialog
            size="xs"
            open={open}
            handler={handleOpen}
            className="px-6 py-10 max-h-[calc(100vh-80px)] overflow-y-auto text-gray-800"
         >
            <h6 className="text-red-500 text-center text-xl mb-10">
            Mevcut fiyatlandırma planınızı temel planla değiştireceğinizden emin misiniz?
            </h6>
            <div className="flex items-center justify-center">
               <Button
                  color="blue"
                  variant="gradient"
                  onClick={handleOpen}
                  className="py-2 font-medium capitalize text-base mr-6"
               >
                  <span>Çıkış</span>
               </Button>
               <Button
                  color="red"
                  variant="gradient"
                  className="py-2 font-medium capitalize text-base"
                  onClick={basicPlanHandler}
               >
                  <span>Kabul ediyorum.</span>
               </Button>
            </div>
         </Dialog>
      </>
   );
};

export default BasicPlanSelect;

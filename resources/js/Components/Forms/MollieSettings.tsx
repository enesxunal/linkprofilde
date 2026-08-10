import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import { Button } from "@material-tailwind/react";
import Switch from "@/Components/Switch";
import { PaymentProps } from "@/types";

const MollieSettings = (props: { mollie: PaymentProps }) => {
   const { active, key } = props.mollie;
   const { data, setData, patch, errors, clearErrors } = useForm({
      allow_mollie: active,
      mollie_key: key,
   });

   const onHandleChange = (
      event: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
   ) => {
      const target = event.target as HTMLInputElement;

      setData({
         ...data,
         [target.name]:
            target.type === "checkbox" ? target.checked : target.value,
      });
   };

   const submit = (e: React.FormEvent) => {
      e.preventDefault();
      clearErrors();
      patch(route("payment.mollie"));
   };

   return (
      <div className="card max-w-[1000px] w-full mx-auto mt-7">
         <div className="px-7 pt-7 pb-4 border-b border-b-gray-200">
            <p className="text18 font-bold text-gray-900">
               Mollie Payment Gateway
            </p>
         </div>

         <form onSubmit={submit} className="p-7">
            <div className="mb-7 md:pl-[164px]">
               <Switch
                  switchId="mollie"
                  name="allow_mollie"
                  label="Allow Mollie Payment Gateway"
                  onChange={onHandleChange}
                  defaultChecked={data.allow_mollie}
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="mollie_key"
                  value={data.mollie_key}
                  error={errors.mollie_key}
                  placeholder="Enter mollie key"
                  onChange={onHandleChange}
                  label="Mollie Key"
                  flexLabel
                  required
               />
            </div>

            <div className="flex items-center mt-6 md:pl-[164px]">
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

export default MollieSettings;

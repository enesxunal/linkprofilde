import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import { Button } from "@/Components/MaterialLite";
import Switch from "@/Components/Switch";
import { PaymentProps } from "@/types";

const PaystackSettings = (props: { paystack: PaymentProps }) => {
   const { active, key, secret } = props.paystack;
   const { data, setData, patch, errors, clearErrors } = useForm({
      allow_paystack: active,
      paystack_key: key,
      paystack_secret: secret,
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
      patch(route("payment.paystack"));
   };

   return (
      <div className="card max-w-[1000px] w-full mx-auto mt-7">
         <div className="px-5 pt-5 pb-4 sm:px-6 border-b border-slate-200">
            <p className="text-lg font-semibold text-slate-900">
               Paystack Payment Gateway
            </p>
         </div>

         <form onSubmit={submit} className="p-5 sm:p-6">
            <div className="mb-7 md:pl-[164px]">
               <Switch
                  switchId="paystack"
                  name="allow_paystack"
                  label="Allow Paystack Payment Gateway"
                  onChange={onHandleChange}
                  defaultChecked={data.allow_paystack}
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="paystack_key"
                  value={data.paystack_key}
                  error={errors.paystack_key}
                  placeholder="Enter paystack key"
                  onChange={onHandleChange}
                  label="Paystack Key"
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="paystack_secret"
                  value={data.paystack_secret}
                  error={errors.paystack_secret}
                  placeholder="Paystack secret girin"
                  onChange={onHandleChange}
                  label="Paystack Secret"
                  flexLabel
                  required
               />
            </div>

            <div className="flex items-center mt-6 md:pl-[164px]">
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

export default PaystackSettings;

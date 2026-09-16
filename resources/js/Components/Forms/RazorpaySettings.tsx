import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import Switch from "@/Components/Switch";
import { Button } from "@/Components/MaterialLite";
import { PaymentProps } from "@/types";

const RazorpaySettings = (props: { razorpay: PaymentProps }) => {
   const { active, key, secret } = props.razorpay;
   const { data, setData, patch, errors, clearErrors } = useForm({
      allow_razorpay: active,
      razorpay_key: key,
      razorpay_secret: secret,
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
      patch(route("payment.razorpay"));
   };

   return (
      <div className="card max-w-[1000px] w-full mx-auto mt-7">
         <div className="px-5 pt-5 pb-4 sm:px-6 border-b border-slate-200">
            <p className="text-lg font-semibold text-slate-900">
               Razorpay Payment Gateway
            </p>
         </div>
         <form onSubmit={submit} className="p-5 sm:p-6">
            <div className="mb-7 md:pl-[164px]">
               <Switch
                  switchId="razorpay"
                  name="allow_razorpay"
                  label="Allow Razorpay Payment Gateway"
                  defaultChecked={data.allow_razorpay}
                  onChange={onHandleChange}
               />
            </div>
            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="razorpay_key"
                  value={data.razorpay_key}
                  error={errors.razorpay_key}
                  placeholder="Razorpay API key girin"
                  onChange={onHandleChange}
                  label="Razorpay Api Key"
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="razorpay_secret"
                  value={data.razorpay_secret}
                  error={errors.razorpay_secret}
                  placeholder="Razorpay API secret girin"
                  onChange={onHandleChange}
                  label="Razorpay Api Secret"
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

export default RazorpaySettings;

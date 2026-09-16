import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import { Button } from "@/Components/MaterialLite";
import Switch from "@/Components/Switch";
import { PaymentProps } from "@/types";

const PaypalSettings = (props: { paypal: PaymentProps }) => {
   const { active, key, secret } = props.paypal;
   const { data, setData, patch, errors, clearErrors } = useForm({
      allow_paypal: active,
      paypal_client_id: key,
      paypal_client_secret: secret,
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
      patch(route("payment.paypal"));
   };

   return (
      <div className="card max-w-[1000px] w-full mx-auto mt-7">
         <div className="px-5 pt-5 pb-4 sm:px-6 border-b border-slate-200">
            <p className="text-lg font-semibold text-slate-900">
               Paypal Payment Gateway
            </p>
         </div>

         <form onSubmit={submit} className="p-5 sm:p-6">
            <div className="mb-7 md:pl-[164px]">
               <Switch
                  switchId="paypal"
                  name="allow_paypal"
                  label="Allow Paypal Payment Gateway"
                  onChange={onHandleChange}
                  defaultChecked={data.allow_paypal}
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="paypal_client_id"
                  value={data.paypal_client_id}
                  error={errors.paypal_client_id}
                  placeholder="Enter paypal client id"
                  onChange={onHandleChange}
                  label="Paypal Client Id"
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="paypal_client_secret"
                  value={data.paypal_client_secret}
                  error={errors.paypal_client_secret}
                  placeholder="PayPal client secret girin"
                  onChange={onHandleChange}
                  label="Paypal Client Secret"
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

export default PaypalSettings;

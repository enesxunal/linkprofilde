import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import Switch from "@/Components/Switch";
import { Button } from "@material-tailwind/react";
import { PaymentProps } from "@/types";

const StripeSettings = (props: { stripe: PaymentProps }) => {
   const { active, key, secret } = props.stripe;

   const { data, setData, patch, errors, clearErrors } = useForm({
      allow_stripe: active,
      stripe_key: key,
      stripe_secret: secret,
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
      patch(route("payment.stripe"));
   };

   return (
      <div className="card max-w-[1000px] w-full mx-auto">
         <div className="px-7 pt-7 pb-4 border-b border-b-gray-200">
            <p className="text18 font-bold text-gray-900">
               Stripe Payment Gateway
            </p>
         </div>
         <form onSubmit={submit} className="p-7">
            <div className="mb-7 md:pl-[164px]">
               <Switch
                  switchId="stripe"
                  name="allow_stripe"
                  label="Allow Stripe Payment Gateway"
                  defaultChecked={data.allow_stripe}
                  onChange={onHandleChange}
               />
            </div>
            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="stripe_key"
                  value={data.stripe_key}
                  error={errors.stripe_key}
                  placeholder="Enter your stripe api key"
                  onChange={onHandleChange}
                  label="Stripe Api Key"
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="stripe_secret"
                  value={data.stripe_secret}
                  error={errors.stripe_secret}
                  placeholder="Enter your stripe api secret"
                  onChange={onHandleChange}
                  label="Stripe Api Secret"
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

export default StripeSettings;

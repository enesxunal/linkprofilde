import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import Switch from "@/Components/Switch";
import { Button } from "@material-tailwind/react";
import { PaymentProps } from "@/types";

const ToslaSettings = (props: { tosla: PaymentProps }) => {
   const { active, key: merchantId, secret } = props.tosla;

   const { data, setData, patch, errors, clearErrors } = useForm({
      allow_tosla: active,
      tosla_merchant_id: merchantId ?? "",
      tosla_secret_key: secret ?? "",
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
      patch(route("payment.tosla"));
   };

   return (
      <div className="card max-w-[1000px] w-full mx-auto">
         <div className="px-7 pt-7 pb-4 border-b border-b-gray-200">
            <p className="text18 font-bold text-gray-900">
               Tosla Ödeme
            </p>
            <p className="text-sm text-gray-500 mt-1">
               Merchant ID ve Secret Key bilgilerini Tosla panelinden alabilirsiniz.
            </p>
         </div>
         <form onSubmit={submit} className="p-7">
            <div className="mb-7 md:pl-[164px]">
               <Switch
                  switchId="tosla"
                  name="allow_tosla"
                  label="Tosla ödeme yöntemini aktif et"
                  defaultChecked={data.allow_tosla}
                  onChange={onHandleChange}
               />
            </div>
            <div className="mb-7">
               <Input
                  fullWidth
                  type="text"
                  name="tosla_merchant_id"
                  value={data.tosla_merchant_id}
                  error={errors.tosla_merchant_id}
                  placeholder="Merchant ID (Mağaza Kimliği)"
                  onChange={onHandleChange}
                  label="Merchant ID"
                  flexLabel
                  required
               />
            </div>
            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="tosla_secret_key"
                  value={data.tosla_secret_key}
                  error={errors.tosla_secret_key}
                  placeholder="Secret Key (Güvenlik Anahtarı)"
                  onChange={onHandleChange}
                  label="Secret Key"
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
                  Kaydet
               </Button>
            </div>
         </form>
      </div>
   );
};

export default ToslaSettings;

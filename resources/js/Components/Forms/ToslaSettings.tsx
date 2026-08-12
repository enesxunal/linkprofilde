import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import Switch from "@/Components/Switch";
import { PaymentProps } from "@/types";

const ToslaSettings = (props: { tosla: PaymentProps }) => {
   const { active, client_id, api_user } = props.tosla;

   const { data, setData, patch, errors, clearErrors } = useForm({
      allow_tosla: active,
      tosla_client_id: client_id ?? "",
      tosla_api_user: api_user ?? "",
      tosla_api_pass: "",
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
         <div className="px-5 pt-5 pb-4 sm:px-6 border-b border-slate-200">
            <p className="text-lg font-semibold text-slate-900">
               Tosla Ödeme
            </p>
            <p className="mt-1 text-sm text-slate-500">
               ClientId, ApiUser ve ApiPass bilgilerini Tosla panelinden alabilirsiniz.
            </p>
         </div>
         <form onSubmit={submit} className="p-5 sm:p-6">
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
                  name="tosla_client_id"
                  value={data.tosla_client_id}
                  error={errors.tosla_client_id}
                  placeholder="ClientId"
                  onChange={onHandleChange}
                  label="ClientId"
                  flexLabel
                  required
               />
            </div>
            <div className="mb-7">
               <Input
                  fullWidth
                  type="text"
                  name="tosla_api_user"
                  value={data.tosla_api_user}
                  error={errors.tosla_api_user}
                  placeholder="ApiUser"
                  onChange={onHandleChange}
                  label="ApiUser"
                  flexLabel
                  required
               />
            </div>
            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="tosla_api_pass"
                  value={data.tosla_api_pass}
                  error={errors.tosla_api_pass}
                  placeholder="Değiştirmek için yeni ApiPass girin"
                  onChange={onHandleChange}
                  label="ApiPass"
                  flexLabel
               />
            </div>
            <div className="flex items-center mt-6 md:pl-[164px]">
               <button
                  type="submit"
                  className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
               >
                  Kaydet
               </button>
            </div>
         </form>
      </div>
   );
};

export default ToslaSettings;

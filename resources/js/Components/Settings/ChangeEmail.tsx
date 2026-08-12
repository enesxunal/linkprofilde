import Input from "@/Components/Input";
import { PageProps } from "@/types";
import { useForm, usePage } from "@inertiajs/react";
import { FormEventHandler } from "react";

const ChangeEmail = () => {
   const { props } = usePage<PageProps>();
   const { email } = props.auth.user;

   const { data, setData, post, errors, clearErrors } = useForm({
      current_email: email,
      new_email: "",
   });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const submit: FormEventHandler = (e) => {
      e.preventDefault();
      clearErrors();
      post(route("change.email"));
   };

   return (
      <div className="card mx-auto w-full max-w-[1000px]">
         <div className="border-b border-slate-200 px-5 pb-4 pt-5 sm:px-6">
            <p className="text-lg font-semibold text-slate-900">E-posta Değiştir</p>
            <p className="mt-0.5 text-sm text-slate-600">
               Yeni e-posta adresinizi doğrulamak için bir bağlantı gönderilir.
            </p>
         </div>
         <form onSubmit={submit} className="p-5 sm:p-6">
            <div className="mb-7">
               <Input
                  fullWidth
                  type="email"
                  name="current_email"
                  value={data.current_email}
                  error={errors.current_email}
                  placeholder="Mevcut e-posta adresiniz"
                  onChange={onHandleChange}
                  label="Mevcut E-posta"
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="email"
                  name="new_email"
                  value={data.new_email}
                  error={errors.new_email}
                  placeholder="Yeni e-posta adresiniz"
                  onChange={onHandleChange}
                  label="Yeni E-posta"
                  flexLabel
                  required
               />
            </div>

            <div className="mt-6 flex items-center md:pl-[164px]">
               <button
                  type="submit"
                  className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
               >
                  Doğrulama Bağlantısı Gönder
               </button>
            </div>
         </form>
      </div>
   );
};

export default ChangeEmail;

import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import { FormEventHandler } from "react";

const ChangePassword = () => {
   const { data, setData, post, errors, clearErrors } = useForm({
      current_password: "",
      password: "",
      password_confirmation: "",
   });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const submit: FormEventHandler = (e) => {
      e.preventDefault();
      clearErrors();
      post(route("password.change"));
   };

   return (
      <div className="card mx-auto w-full max-w-[1000px]">
         <div className="border-b border-slate-200 px-5 pb-4 pt-5 sm:px-6">
            <p className="text-lg font-semibold text-slate-900">Şifre Değiştir</p>
            <p className="mt-0.5 text-sm text-slate-600">
               Hesap güvenliğiniz için güçlü bir şifre kullanın.
            </p>
         </div>
         <form onSubmit={submit} className="p-5 sm:p-6">
            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="current_password"
                  label="Mevcut Şifre"
                  value={data.current_password}
                  error={errors.current_password}
                  placeholder="Mevcut şifrenizi girin"
                  onChange={onHandleChange}
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="password"
                  label="Yeni Şifre"
                  value={data.password}
                  error={errors.password}
                  placeholder="Yeni şifrenizi girin"
                  onChange={onHandleChange}
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="password_confirmation"
                  value={data.password_confirmation}
                  placeholder="Yeni şifreyi tekrar girin"
                  onChange={onHandleChange}
                  label="Şifre Tekrar"
                  flexLabel
                  required
               />
            </div>

            <div className="mt-6 flex items-center md:pl-[164px]">
               <button
                  type="submit"
                  className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
               >
                  Şifreyi Güncelle
               </button>
            </div>
         </form>
      </div>
   );
};

export default ChangePassword;

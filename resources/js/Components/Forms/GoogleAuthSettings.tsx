import React from "react";
import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import Switch from "@/Components/Switch";
import { SocialLoginProps } from "@/types";

const GoogleAuthSettings = (props: { google: SocialLoginProps }) => {
   const { active, client_id, client_secret, redirect_url } = props.google;
   const boolValue: boolean = !!parseInt(active);

   const { data, setData, patch, errors, clearErrors } = useForm({
      google_login_allow: boolValue,
      google_client_id: client_id,
      google_client_secret: client_secret,
      google_redirect: redirect_url,
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
      patch(route("settings.google"));
   };

   return (
      <div className="card mx-auto w-full max-w-[1000px]">
         <div className="border-b border-slate-200 px-5 pb-4 pt-5 sm:px-6">
            <p className="text-lg font-semibold text-slate-900">
               Google Giriş Ayarları
            </p>
            <p className="mt-0.5 text-sm text-slate-600">
               Google OAuth istemci bilgilerini yönetin.
            </p>
         </div>

         <form onSubmit={submit} className="p-5 sm:p-6">
            <div className="mb-7 md:pl-[164px]">
               <Switch
                  switchId="google"
                  name="google_login_allow"
                  label="Google ile girişi aktif et"
                  onChange={onHandleChange}
                  defaultChecked={data.google_login_allow}
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="google_client_id"
                  value={data.google_client_id}
                  error={errors.google_client_id}
                  placeholder="Google Client ID"
                  onChange={onHandleChange}
                  label="Google Client ID"
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="google_client_secret"
                  value={data.google_client_secret}
                  error={errors.google_client_secret}
                  placeholder="Google Client Secret"
                  onChange={onHandleChange}
                  label="Google Client Secret"
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  type="text"
                  fullWidth
                  name="google_redirect"
                  value={data.google_redirect}
                  error={errors.google_redirect}
                  placeholder="Google Redirect URL"
                  onChange={onHandleChange}
                  label="Google Redirect URL"
                  flexLabel
                  required
               />
            </div>

            <div className="mt-6 flex items-center md:pl-[164px]">
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

export default GoogleAuthSettings;

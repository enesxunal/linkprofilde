import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import InputDropdown from "@/Components/InputDropdown";
import { SMTPProps } from "@/types";

const SMTPSettings = (props: { smtp: SMTPProps }) => {
   const {
      host,
      port,
      username,
      sender_email,
      sender_name,
      encryption,
   } = props.smtp;

   const { data, setData, patch, errors, clearErrors } = useForm({
      host: host ?? "",
      port: port,
      encryption: encryption,
      username: username ?? "",
      password: "",
      from_address: sender_email ?? "",
      from_name: sender_name ?? "",
      admin_email: "",
   });

   const onHandleChange = (
      event: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
   ) => {
      const target = event.target as HTMLInputElement;
      setData({
         ...data,
         [target.name]: target.value,
      });
   };

   const submit = (e: React.FormEvent) => {
      e.preventDefault();
      clearErrors();
      patch(route("settings.smtp"));
   };

   return (
      <div className="card mx-auto w-full max-w-[1000px]">
         <div className="border-b border-slate-200 px-5 pb-4 pt-5 sm:px-6">
            <p className="text-lg font-semibold text-slate-900">SMTP Ayarları</p>
            <p className="mt-1 text-sm text-amber-700">
               SMTP bilgileri olmadan yeni kullanıcılar kayıt olamayabilir.
            </p>
         </div>

         <form onSubmit={submit} className="p-5 sm:p-6">
            <div className="grid grid-cols-1 gap-7">
               <InputDropdown
                  required
                  flexLabel
                  fullWidth
                  name="mailer"
                  label="Mailer"
                  defaultValue="smtp"
                  itemList={[{ key: "SMTP", value: "smtp" }]}
                  onChange={(e: any) => setData("encryption", e.value)}
               />

               <Input
                  type="password"
                  name="host"
                  label="SMTP Host"
                  value={data.host}
                  error={errors.host}
                  onChange={onHandleChange}
                  placeholder="SMTP sunucu adresi"
                  fullWidth
                  flexLabel
                  required
               />

               <Input
                  type="number"
                  name="port"
                  label="SMTP Port"
                  value={data.port as any}
                  error={errors.port}
                  onChange={onHandleChange}
                  placeholder="SMTP port"
                  fullWidth
                  flexLabel
                  required
               />

               <Input
                  type="password"
                  name="username"
                  label="SMTP Kullanıcı Adı"
                  value={data.username}
                  error={errors.username}
                  onChange={onHandleChange}
                  placeholder="SMTP kullanıcı adı"
                  fullWidth
                  flexLabel
                  required
               />

               <Input
                  type="password"
                  name="password"
                  label="SMTP Şifre"
                  value={data.password}
                  error={errors.password}
                  onChange={onHandleChange}
                  placeholder="Değiştirmek istemiyorsanız boş bırakın"
                  fullWidth
                  flexLabel
                  autoComplete="new-password"
               />

               <Input
                  type="text"
                  name="from_address"
                  label="Gönderen E-posta"
                  value={data.from_address}
                  error={errors.from_address}
                  onChange={onHandleChange}
                  placeholder="Gönderen e-posta adresi"
                  fullWidth
                  flexLabel
                  required
               />

               <Input
                  type="text"
                  name="from_name"
                  label="Gönderen Adı"
                  value={data.from_name}
                  error={errors.from_name}
                  onChange={onHandleChange}
                  placeholder="Gönderen adı"
                  fullWidth
                  flexLabel
                  required
               />

               <InputDropdown
                  required
                  flexLabel
                  fullWidth
                  name="encryption"
                  label="SMTP Şifreleme"
                  error={errors.encryption}
                  defaultValue="tls"
                  itemList={[
                     { key: "TLS", value: "tls" },
                     { key: "SSL", value: "ssl" },
                  ]}
                  onChange={(e: any) => setData("encryption", e.value)}
               />

               <Input
                  type="email"
                  name="admin_email"
                  label="Admin E-posta"
                  value={data.admin_email}
                  error={errors.admin_email}
                  onChange={onHandleChange}
                  placeholder="Bağlantı testi için admin e-posta"
                  fullWidth
                  flexLabel
                  required
               />
            </div>

            <div className="mt-7 flex items-center md:pl-[164px]">
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

export default SMTPSettings;

import Dashboard from "@/Layouts/Dashboard";
import { Head, router } from "@inertiajs/react";
import { Button } from "@/Components/MaterialLite";
import { ReactNode, useState } from "react";

const VerifyEmail = () => {
   const [sending, setSending] = useState(false);

   const resend = () => {
      if (sending) return;
      setSending(true);
      router.post(route("verification.send"), {}, {
         preserveScroll: true,
         onFinish: () => setSending(false),
      });
   };

   return (
      <>
         <Head title="E-posta Doğrula" />
         <div className="mt-10 flex items-center justify-center">
            <div className="card w-full max-w-[600px] p-5 sm:p-6">
               <h1 className="text-lg font-semibold text-slate-900">
                  E-posta adresinizi doğrulayın
               </h1>
               <p className="mt-2 text-sm leading-6 text-slate-600">
                  Kayıtlı e-posta adresinize bir doğrulama bağlantısı gönderdik.
                  Panel özelliklerini kullanabilmek için e-postanızı kontrol edip
                  bağlantıya tıklayın.
               </p>

               <Button
                  onClick={resend}
                  disabled={sending}
                  className="mt-6 w-full rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
               >
                  {sending ? "Gönderiliyor..." : "Doğrulama Bağlantısını Tekrar Gönder"}
               </Button>
            </div>
         </div>
      </>
   );
};

VerifyEmail.layout = (page: ReactNode) => <Dashboard children={page} />;

export default VerifyEmail;

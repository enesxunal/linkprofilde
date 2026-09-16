import Dashboard from "@/Layouts/Dashboard";
import { Head, useForm } from "@inertiajs/react";
import { FormEvent, ReactNode } from "react";

const ConfirmPassword = () => {
   const { data, setData, post, processing, errors, reset } = useForm({
      password: "",
   });

   const submit = (event: FormEvent) => {
      event.preventDefault();
      post("/confirm-password", {
         preserveScroll: true,
         onFinish: () => reset("password"),
      });
   };

   return (
      <>
         <Head title="Şifreyi Doğrula" />
         <div className="mx-auto mt-10 w-full max-w-lg">
            <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
               <h1 className="text-lg font-semibold text-slate-900">
                  Şifrenizi doğrulayın
               </h1>
               <p className="mt-2 text-sm leading-6 text-slate-600">
                  Bu işlem güvenlik açısından hassastır. Devam etmek için mevcut
                  şifrenizi tekrar girin.
               </p>

               <form onSubmit={submit} className="mt-6">
                  <label
                     htmlFor="confirm-password"
                     className="mb-1.5 block text-sm font-medium text-slate-700"
                  >
                     Mevcut şifre
                  </label>
                  <input
                     id="confirm-password"
                     type="password"
                     value={data.password}
                     onChange={(event) => setData("password", event.target.value)}
                     autoComplete="current-password"
                     autoFocus
                     required
                     className="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                  />
                  {errors.password ? (
                     <p className="mt-2 text-sm text-red-600">{errors.password}</p>
                  ) : null}

                  <button
                     type="submit"
                     disabled={processing}
                     className="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                  >
                     {processing ? "Doğrulanıyor..." : "Şifreyi Doğrula"}
                  </button>
               </form>
            </div>
         </div>
      </>
   );
};

ConfirmPassword.layout = (page: ReactNode) => <Dashboard children={page} />;

export default ConfirmPassword;

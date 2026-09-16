import { useState } from "react";
import { PageProps } from "@/types";
import MenuIcon from "@/Components/Icons/Menu";
import Expand from "@/Components/Icons/Expand";
import { Link, router, usePage } from "@inertiajs/react";
import UserCircle from "@/Components/Icons/UserCircle";
import { setOpenSidenav, setMobileSidenav } from "@/context/AppContext";
import { useAppContext } from "@/hooks/useAppContext";
import Globe from "@/Components/Icons/Globe";

const DashboardNavbar = () => {
   const { props } = usePage<PageProps>();
   const user = props.auth.user;
   const [state, dispatch] = useAppContext();
   const { openSidenav, mobileSidenav } = state;
   const [isFullscreen, setIsFullscreen] = useState(false);

   const logout = () => {
      router.post("/logout", {}, { onSuccess: () => (window.location.href = "/") });
   };

   const handleFullscreenToggle = async () => {
      try {
         if (!document.fullscreenElement) {
            await document.documentElement.requestFullscreen();
            setIsFullscreen(true);
         } else {
            await document.exitFullscreen();
            setIsFullscreen(false);
         }
      } catch {
         setIsFullscreen(Boolean(document.fullscreenElement));
      }
   };

   const languageClass = (lang: string) =>
      `block w-full rounded-lg px-3 py-2 text-left text-sm transition-colors ${
         props.translate.locale === lang
            ? "bg-blue-50 font-semibold text-blue-700"
            : "text-slate-700 hover:bg-slate-50"
      }`;

   return (
      <header className="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
         <div className="flex items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <div className="flex min-w-0 items-center gap-2">
               <button
                  type="button"
                  className="hidden h-9 w-9 items-center justify-center rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-100 lg:inline-flex"
                  onClick={() => setOpenSidenav(dispatch, !openSidenav)}
                  aria-label="Kenar çubuğunu aç/kapat"
               >
                  <MenuIcon />
               </button>
               <button
                  type="button"
                  className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-100 lg:hidden"
                  onClick={() => setMobileSidenav(dispatch, !mobileSidenav)}
                  aria-label="Menüyü aç"
               >
                  <MenuIcon />
               </button>
               <div className="hidden min-w-0 sm:block">
                  <p className="truncate text-sm font-medium text-slate-500">Panel</p>
               </div>
            </div>

            <div className="flex items-center gap-1">
               <button
                  type="button"
                  onClick={handleFullscreenToggle}
                  className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-100"
                  aria-label={isFullscreen ? "Tam ekrandan çık" : "Tam ekran"}
               >
                  <Expand className="h-5 w-5" />
               </button>

               <details className="group relative">
                  <summary className="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                     <Globe className="h-5 w-5" />
                     <span className="sr-only">Dil seç</span>
                  </summary>
                  <div className="absolute right-0 top-11 z-50 min-w-[150px] rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
                     <button
                        type="button"
                        onClick={() => router.get("/lang/tr")}
                        className={languageClass("tr")}
                     >
                        Türkçe
                     </button>
                     <button
                        type="button"
                        onClick={() => router.get("/lang/fr")}
                        className={languageClass("fr")}
                     >
                        Français
                     </button>
                     <button
                        type="button"
                        onClick={() => router.get("/lang/de")}
                        className={languageClass("de")}
                     >
                        Deutsch
                     </button>
                  </div>
               </details>

               <details className="group relative ml-1">
                  <summary className="flex cursor-pointer list-none items-center justify-center rounded-full [&::-webkit-details-marker]:hidden">
                     {user?.image ? (
                        <img
                           src={`/${user.image}`}
                           alt={`${user.name || "Kullanıcı"} profil fotoğrafı`}
                           className="h-9 w-9 rounded-full border border-slate-200 object-cover"
                        />
                     ) : (
                        <UserCircle className="h-9 w-9 text-slate-400" />
                     )}
                     <span className="sr-only">Kullanıcı menüsü</span>
                  </summary>
                  <div className="absolute right-0 top-11 z-50 min-w-[170px] rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
                     {user?.name ? (
                        <div className="border-b border-slate-100 px-3 py-2">
                           <p className="max-w-[180px] truncate text-xs font-semibold text-slate-900">
                              {user.name}
                           </p>
                           <p className="mt-0.5 max-w-[180px] truncate text-[11px] text-slate-500">
                              {user.email}
                           </p>
                        </div>
                     ) : null}
                     <a
                        href="/"
                        className="mt-1 block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"
                     >
                        Anasayfa
                     </a>
                     <Link
                        href="/settings"
                        className="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"
                     >
                        Profil Ayarları
                     </Link>
                     <button
                        type="button"
                        onClick={logout}
                        className="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                     >
                        Çıkış Yap
                     </button>
                  </div>
               </details>
            </div>
         </div>
      </header>
   );
};

export default DashboardNavbar;

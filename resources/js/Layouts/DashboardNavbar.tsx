import {
   Menu,
   Avatar,
   ListItem,
   MenuList,
   MenuItem,
   MenuHandler,
} from "@material-tailwind/react";
import axios from "axios";
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

   const logout = async () => {
      const res = await axios.post("/logout");
      if (res.status === 200) window.location.href = "/";
   };

   const handleFullscreenToggle = () => {
      if (!isFullscreen) {
         document.documentElement.requestFullscreen();
      } else {
         document.exitFullscreen();
      }
      setIsFullscreen(!isFullscreen);
   };

   const lanSelect = (lang: string): boolean => {
      if (props.translate.locale === lang) {
         return true;
      }
      return false;
   };

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
                  <p className="truncate text-sm font-medium text-slate-500">
                     Panel
                  </p>
               </div>
            </div>

            <div className="flex items-center gap-1">
               <button
                  type="button"
                  onClick={handleFullscreenToggle}
                  className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-100"
                  aria-label="Tam ekran"
               >
                  <Expand className="h-5 w-5" />
               </button>

               <Menu placement="bottom-end">
                  <MenuHandler>
                     <button
                        type="button"
                        className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-100"
                        aria-label="Dil seç"
                     >
                        <Globe className="h-5 w-5" />
                     </button>
                  </MenuHandler>

                  <MenuList className="min-w-[140px] rounded-xl border border-slate-200 p-1 shadow-sm">
                     <ListItem
                        selected={lanSelect("tr")}
                        onClick={() => router.get("/lang/tr")}
                        className="rounded-lg py-2 text-sm"
                     >
                        Türkçe
                     </ListItem>
                     <ListItem
                        selected={lanSelect("fr")}
                        onClick={() => router.get("/lang/fr")}
                        className="rounded-lg py-2 text-sm"
                     >
                        Français
                     </ListItem>
                     <ListItem
                        selected={lanSelect("de")}
                        onClick={() => router.get("/lang/de")}
                        className="rounded-lg py-2 text-sm"
                     >
                        Deutsch
                     </ListItem>
                  </MenuList>
               </Menu>

               <Menu placement="bottom-end">
                  <MenuHandler>
                     <button
                        type="button"
                        className="ml-1 inline-flex items-center justify-center"
                        aria-label="Kullanıcı menüsü"
                     >
                        {user && user.image ? (
                           <Avatar
                              src={`/${user.image}`}
                              alt="user"
                              size="xs"
                              variant="circular"
                              className="h-9 w-9 cursor-pointer"
                           />
                        ) : (
                           <UserCircle className="h-9 w-9 cursor-pointer text-slate-400" />
                        )}
                     </button>
                  </MenuHandler>

                  <MenuList className="min-w-[140px] rounded-xl border border-slate-200 p-1 shadow-sm">
                     <MenuItem className="rounded-lg text-sm">
                        <a href="/">Anasayfa</a>
                     </MenuItem>
                     <MenuItem className="rounded-lg text-sm">
                        <Link href="/settings">Profil</Link>
                     </MenuItem>
                     <MenuItem
                        className="rounded-lg text-sm"
                        onClick={logout}
                     >
                        Çıkış Yap
                     </MenuItem>
                  </MenuList>
               </Menu>
            </div>
         </div>
      </header>
   );
};

export default DashboardNavbar;

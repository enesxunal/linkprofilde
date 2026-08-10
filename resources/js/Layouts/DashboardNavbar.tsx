import {
   Menu,
   Navbar,
   Avatar,
   List,
   ListItem,
   MenuList,
   MenuItem,
   IconButton,
   MenuHandler,
   Card,
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
   // localStorage.setItem("locale", "en");
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
      <Navbar
         fullWidth
         blurred={false}
         color="white"
         className="rounded-lg transition-all sticky top-4 md:top-5 z-40 !shadow-box px-4 py-3"
      >
         <div className="flex justify-between gap-6 md:flex-row md:items-center">
            <div className="capitalize">
               <IconButton
                  variant="text"
                  color="blue-gray"
                  className="hidden lg:block"
                  onClick={() => setOpenSidenav(dispatch, !openSidenav)}
               >
                  <MenuIcon />
               </IconButton>
               <IconButton
                  variant="text"
                  color="blue-gray"
                  className="block lg:hidden"
                  onClick={() => setMobileSidenav(dispatch, !mobileSidenav)}
               >
                  <MenuIcon />
               </IconButton>
            </div>

            <div className="flex items-center">
               <IconButton
                  onClick={handleFullscreenToggle}
                  variant="text"
                  color="blue-gray"
                  className="rounded-full"
               >
                  <Expand className="h-[22px] w-[22px]" />
               </IconButton>

               <Menu placement="bottom-end">
                  <MenuHandler>
                     <IconButton
                        variant="text"
                        color="blue-gray"
                        className="rounded-full mr-2"
                     >
                        <Globe className="h-6 w-6 text-gray-700 cursor-pointer" />
                     </IconButton>
                  </MenuHandler>

                  <MenuList className="min-w-[140px]">
                     <ListItem
                        selected={lanSelect("tr")}
                        onClick={() => router.get("/lang/tr")}
                        className="py-2"
                     >
                        Türkçe
                     </ListItem>
                     <ListItem
                        selected={lanSelect("fr")}
                        onClick={() => router.get("/lang/fr")}
                        className="py-2"
                     >
                        Français
                     </ListItem>
                     <ListItem
                        selected={lanSelect("de")}
                        onClick={() => router.get("/lang/de")}
                        className="py-2"
                     >
                        Deutsch
                     </ListItem>
                  </MenuList>
               </Menu>

               <Menu placement="bottom-end">
                  <MenuHandler>
                     <div>
                        {user && user.image ? (
                           <Avatar
                              src={`/${user.image}`}
                              alt="item-1"
                              size="xs"
                              variant="circular"
                              className="h-9 w-9 lg:mr-1 cursor-pointer"
                           />
                        ) : (
                           <UserCircle className="h-10 w-10 text-blue-gray-500 lg:m-1 cursor-pointer" />
                        )}
                     </div>
                  </MenuHandler>

                  <MenuList className="min-w-[140px]">
                     <MenuItem>
                        <a href="/">Anasayfa</a>
                     </MenuItem>
                     <MenuItem>
                        <Link href="/settings">Profil</Link>
                     </MenuItem>
                     <MenuItem onClick={logout}>Çıkış Yap</MenuItem>
                  </MenuList>
               </Menu>
            </div>
         </div>
      </Navbar>
   );
};

export default DashboardNavbar;

import axios from "axios";
import { PageProps } from "@/types";
import SimpleBar from "simplebar-react";
import { useEffect } from "react";
import { router, usePage } from "@inertiajs/react";
import { useAppContext } from "@/hooks/useAppContext";
import LeftArrow from "@/Components/Icons/LeftArrow";
import { setMobileSidenav } from "@/context/AppContext";
import Globe from "@/Components/Icons/Globe";
import icons from "@/Components/Icons";

const isActivePath = (menuPath: string, currentUrl: string): boolean => {
   if (!menuPath || menuPath === "/logout") {
      return false;
   }

   const path = currentUrl.split("?")[0];

   if (path === menuPath) {
      return true;
   }

   return path.startsWith(`${menuPath}/`);
};

const MobileSidebar = () => {
   const { url, props } = usePage<PageProps>();
   const { user } = props.auth;
   const { logo, title } = props.app;
   const [state, dispatch] = useAppContext();
   const { mobileSidenav, openSidenav } = state;

   useEffect(() => {
      if (mobileSidenav) {
         setMobileSidenav(dispatch, false);
      }
      // Close mobile drawer on route change
      // eslint-disable-next-line react-hooks/exhaustive-deps
   }, [url]);

   useEffect(() => {
      if (!mobileSidenav) {
         return;
      }

      const previous = document.body.style.overflow;
      document.body.style.overflow = "hidden";

      return () => {
         document.body.style.overflow = previous;
      };
   }, [mobileSidenav]);

   const logout = async () => {
      const res = await axios.post("/logout");
      if (res.status === 200) window.location.href = "/";
   };

   const closeMobile = () => setMobileSidenav(dispatch, false);

   const desktopOpen = openSidenav;
   const mobileOpen = mobileSidenav;

   return (
      <>
         {mobileOpen && (
            <button
               type="button"
               aria-label="Menüyü kapat"
               className="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-[1px] lg:hidden"
               onClick={closeMobile}
            />
         )}

         <aside
            className={`relative z-50 h-full shrink-0 overflow-hidden transition-[width] duration-300 ${
               desktopOpen ? "w-[240px]" : "w-0"
            }`}
         >
            <div
               className={`h-full overflow-x-hidden border-r border-slate-200 bg-white transition-transform duration-300 ${
                  mobileOpen
                     ? "fixed left-0 top-0 z-50 w-[240px] translate-x-0 shadow-lg lg:static lg:translate-x-0 lg:shadow-none"
                     : desktopOpen
                     ? "w-[240px]"
                     : "pointer-events-none w-[240px] -translate-x-full lg:pointer-events-none lg:w-0 lg:border-0"
               } ${
                  !desktopOpen && !mobileOpen
                     ? "lg:overflow-hidden"
                     : ""
               }`}
            >
               <div className="flex items-center justify-between px-4 pb-2 pt-5 lg:px-5">
                  <a href="/" className="flex min-w-0 items-center gap-2.5">
                     <img
                        src={`/${logo}`}
                        alt={title}
                        className="h-8 w-8 rounded-lg object-cover"
                     />
                     <p className="truncate text-base font-bold tracking-tight text-slate-900">
                        {title}
                     </p>
                  </a>
                  {mobileOpen && (
                     <button
                        type="button"
                        onClick={closeMobile}
                        className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 lg:hidden"
                        aria-label="Menüyü kapat"
                     >
                        <LeftArrow />
                     </button>
                  )}
               </div>

               {(desktopOpen || mobileOpen) && (
                  <SimpleBar style={{ height: "calc(100vh - 66px)" }}>
                     <nav className="px-3 py-4 lg:px-4">
                        {props.translate.sidebar.map(
                           ({ role, title: sectionTitle, pages }, key) => {
                              if (
                                 user?.roles?.[0]?.name !== "SUPER-ADMIN" &&
                                 role === "SUPER-ADMIN"
                              ) {
                                 return null;
                              }

                              return (
                                 <ul
                                    key={key}
                                    className="mb-6 flex flex-col gap-1 last:mb-0"
                                 >
                                    <li className="mb-2 px-3">
                                       <span className="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                          {sectionTitle}
                                       </span>
                                    </li>
                                    {pages.map(({ icon, name, path }) => {
                                       let Icon = Globe;
                                       const entries = Object.entries(icons);

                                       for (const [entryKey, value] of entries) {
                                          if (entryKey === icon) {
                                             Icon = value;
                                          }
                                       }

                                       const active = isActivePath(path, url);
                                       const isLogout = path === "/logout";

                                       if (isLogout) {
                                          return (
                                             <li key={name}>
                                                <button
                                                   type="button"
                                                   onClick={logout}
                                                   className="flex w-full items-center rounded-lg px-3 py-2 text-left text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 hover:text-slate-900"
                                                >
                                                   <Icon className="h-4 w-4 shrink-0 text-slate-500" />
                                                   <span className="ml-3 truncate capitalize">
                                                      {name}
                                                   </span>
                                                </button>
                                             </li>
                                          );
                                       }

                                       return (
                                          <li key={name}>
                                             <button
                                                type="button"
                                                onClick={() => {
                                                   closeMobile();
                                                   router.get(path);
                                                }}
                                                className={`relative flex w-full items-center rounded-lg px-3 py-2 text-left text-sm font-medium transition-colors ${
                                                   active
                                                      ? "bg-blue-50 text-blue-700"
                                                      : "text-slate-700 hover:bg-slate-50 hover:text-slate-900"
                                                }`}
                                             >
                                                {active && (
                                                   <span
                                                      aria-hidden="true"
                                                      className="absolute left-0 top-1/2 h-5 w-0.5 -translate-y-1/2 rounded-full bg-blue-600"
                                                   />
                                                )}
                                                <Icon
                                                   className={`h-4 w-4 shrink-0 ${
                                                      active
                                                         ? "text-blue-600"
                                                         : "text-slate-500"
                                                   }`}
                                                />
                                                <span className="ml-3 truncate capitalize">
                                                   {name}
                                                </span>
                                             </button>
                                          </li>
                                       );
                                    })}
                                 </ul>
                              );
                           }
                        )}
                     </nav>
                  </SimpleBar>
               )}
            </div>
         </aside>
      </>
   );
};

export default MobileSidebar;

import {
   Menu,
   MenuItem,
   MenuList,
   MenuHandler,
} from "@material-tailwind/react";
import SimpleBar from "simplebar-react";
import { router } from "@inertiajs/react";
import { PaginationProps } from "@/types";
import { buildPaginatorUrl, withPerPage } from "./paginatorQuery";

interface Props {
   className: string;
   paginationInfo: PaginationProps;
}

const pageButtonClass =
   "inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50";

const TablePagination = (props: Props) => {
   const {
      per_page,
      last_page,
      current_page,
      prev_page_url,
      next_page_url,
      last_page_url,
      first_page_url,
   } = props.paginationInfo;

   let dropdownList = [];
   if (last_page > 0) {
      for (let i = 1; i <= last_page; i++) {
         dropdownList.push({
            key: `${i}`,
            value: i,
         });
      }
   } else {
      dropdownList.push({
         key: "1",
         value: 1,
      });
   }

   const gotoPage = (e: number) => {
      router.get(buildPaginatorUrl(props.paginationInfo, e, per_page));
   };

   const gotoRoute = (url: string) => {
      router.get(withPerPage(url, per_page));
   };

   const menuItem = (e: number) => {
      return `rounded-md text-center py-1.5 text-sm ${
         current_page === e ? "bg-blue-50 text-blue-700" : "text-slate-700"
      }`;
   };

   return (
      <div className={`${props.className}`}>
         <div className="mb-4 flex items-center justify-center md:hidden">
            <span className="mr-1 text-sm text-slate-600">
               Sayfa{" "}
               <strong className="text-slate-900">
                  {current_page} / {last_page}
               </strong>
            </span>
            <span className="text-sm text-slate-500">| Sayfaya Git:</span>
            <div className="ml-3 w-[60px]">
               <Menu placement="bottom-end">
                  <MenuHandler>
                     <button
                        type="button"
                        className="h-8 w-[60px] rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:border-blue-500"
                     >
                        {current_page}
                     </button>
                  </MenuHandler>
                  <MenuList className="max-h-[200px] min-w-[60px] overflow-hidden rounded-xl border border-slate-200 p-1 shadow-sm">
                     <SimpleBar style={{ maxHeight: "198px" }}>
                        {dropdownList.map((item) => (
                           <MenuItem
                              key={item.key}
                              value={item.value}
                              onClick={() => gotoPage(item.value)}
                              className={menuItem(item.value)}
                           >
                              {item.value}
                           </MenuItem>
                        ))}
                     </SimpleBar>
                  </MenuList>
               </Menu>
            </div>
         </div>

         <div className="flex flex-wrap items-center justify-center gap-2">
            <button
               type="button"
               disabled={!prev_page_url}
               onClick={() => gotoRoute(first_page_url)}
               className={pageButtonClass}
            >
               « Başa
            </button>

            <button
               type="button"
               disabled={!prev_page_url}
               onClick={() => gotoRoute(prev_page_url)}
               className={pageButtonClass}
            >
               Geri
            </button>

            <div className="hidden items-center md:flex">
               <span className="mr-1 text-sm text-slate-600">
                  Sayfa{" "}
                  <strong className="text-slate-900">
                     {current_page} / {last_page}
                  </strong>
               </span>
               <span className="text-sm text-slate-500">| Sayfaya Git:</span>
               <div className="ml-3 w-[60px]">
                  <Menu placement="bottom-end">
                     <MenuHandler>
                        <button
                           type="button"
                           className="h-8 w-[60px] rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:border-blue-500"
                        >
                           {current_page}
                        </button>
                     </MenuHandler>
                     <MenuList className="max-h-[200px] min-w-[60px] overflow-hidden rounded-xl border border-slate-200 p-1 shadow-sm">
                        <SimpleBar style={{ maxHeight: "198px" }}>
                           {dropdownList.map((item) => (
                              <MenuItem
                                 key={item.key}
                                 value={item.value}
                                 onClick={() => gotoPage(item.value)}
                                 className={menuItem(item.value)}
                              >
                                 {item.value}
                              </MenuItem>
                           ))}
                        </SimpleBar>
                     </MenuList>
                  </Menu>
               </div>
            </div>

            <button
               type="button"
               disabled={!next_page_url}
               onClick={() => gotoRoute(next_page_url)}
               className={pageButtonClass}
            >
               İleri
            </button>

            <button
               type="button"
               disabled={!next_page_url}
               onClick={() => gotoRoute(last_page_url)}
               className={pageButtonClass}
            >
               Son »
            </button>
         </div>
      </div>
   );
};

export default TablePagination;

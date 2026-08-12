import {
   Menu,
   MenuItem,
   MenuList,
   MenuHandler,
} from "@material-tailwind/react";
import SimpleBar from "simplebar-react";
import ArrowDown from "../Icons/ArrowDown";
import { router } from "@inertiajs/react";
import { PaginationProps } from "@/types";
import { buildPaginatorUrl } from "./paginatorQuery";

interface Props {
   className?: string;
   pageData: PaginationProps;
   dropdownList: number[];
}

const TablePageSize = (props: Props) => {
   const { pageData, dropdownList, className } = props;
   const { per_page, current_page } = pageData;

   const gotoPage = (current: number, size: number) => {
      router.get(buildPaginatorUrl(pageData, current, size));
   };

   return (
      <div className={`relative ${className ?? ""}`}>
         <span className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <ArrowDown className="h-3 w-3 text-slate-500" />
         </span>
         <Menu placement="bottom-end">
            <MenuHandler>
               <button
                  type="button"
                  className="h-10 w-[72px] rounded-lg border border-slate-200 px-3 text-left text-sm font-medium text-slate-700 hover:border-blue-500"
               >
                  {per_page}
               </button>
            </MenuHandler>
            <MenuList className="max-h-[200px] min-w-[72px] overflow-hidden rounded-xl border border-slate-200 p-1 shadow-sm">
               <SimpleBar style={{ maxHeight: "198px" }}>
                  {dropdownList.map((item) => (
                     <MenuItem
                        key={item}
                        value={item}
                        onClick={() => gotoPage(current_page, item)}
                        className={`rounded-md text-center text-sm ${
                           per_page === item
                              ? "bg-slate-50 text-blue-700"
                              : "text-slate-700"
                        }`}
                     >
                        {item}
                     </MenuItem>
                  ))}
               </SimpleBar>
            </MenuList>
         </Menu>
      </div>
   );
};

export default TablePageSize;

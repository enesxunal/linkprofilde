import axios from "axios";
import Search from "@/Components/Icons/Search";
import TablePageSize from "@/Components/Table/TablePageSize";
import debounce from "@/utils/debounce";
import { PaginationProps } from "@/types";
import { ReactNode } from "react";

interface Props {
   data: PaginationProps;
   title: string;
   component?: ReactNode;
   globalSearch: boolean;
   tablePageSizes: number[];
   setSearchData: (res: PaginationProps) => void;
   searchPath?: string;
   /** Arama isteğine eklenecek ek parametreler (örn. suspicious=1) */
   extraSearchParams?: Record<string, string | number>;
}

const TableNav = (props: Props) => {
   const {
      data,
      title,
      component,
      globalSearch,
      tablePageSizes,
      setSearchData,
      searchPath,
      extraSearchParams,
   } = props;

   const searchHandler = debounce(async (e: any) => {
      const query = e.target.value;
      const params: Record<string, string> = {
         page: "1",
         per_page: String(data.per_page),
         value: query,
      };
      if (extraSearchParams) {
         Object.entries(extraSearchParams).forEach(([k, v]) => {
            params[k] = String(v);
         });
      }
      const res = await axios.get(
         `${searchPath}?${new URLSearchParams(params)}`
      );
      setSearchData(res.data);
   }, 300);

   return (
      <div className="p-7 md:flex items-center justify-between">
         {title && (
            <p className="mb-4 md:mb-0 text18 font-bold text-gray-900">
               {title}
            </p>
         )}
         <div className="flex justify-end items-center">
            {globalSearch && (
               <div className="w-full md:max-w-[260px] relative">
                  <input
                     type="text"
                     placeholder="Arama Yap"
                     onChange={searchHandler}
                     className="h-10 pl-12 pr-4 py-[15px] border border-gray-200 rounded-md w-full focus:ring-0 focus:outline-0 focus:border-blue-500 text-sm font-normal text-gray-500"
                  />
                  <Search className="absolute w-4 h-4 top-3 left-4 text-gray-700 z-10" />
               </div>
            )}

            <TablePageSize
               pageData={data}
               dropdownList={tablePageSizes}
               className="ml-3"
            />

            {component && component}
         </div>
      </div>
   );
};

export default TableNav;

import axios from "axios";
import Search from "@/Components/Icons/Search";
import TablePageSize from "@/Components/Table/TablePageSize";
import debounce from "@/utils/debounce";
import { PaginationProps } from "@/types";
import { ReactNode, useEffect, useMemo, useRef } from "react";

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

   const abortRef = useRef<AbortController | null>(null);
   const seqRef = useRef(0);
   const latestRef = useRef({
      data,
      setSearchData,
      searchPath,
      extraSearchParams,
   });
   latestRef.current = {
      data,
      setSearchData,
      searchPath,
      extraSearchParams,
   };

   useEffect(() => {
      return () => {
         abortRef.current?.abort();
      };
   }, []);

   const searchHandler = useMemo(
      () =>
         debounce(async (e: any) => {
            const {
               data: pageData,
               setSearchData: applySearch,
               searchPath: path,
               extraSearchParams: extra,
            } = latestRef.current;

            abortRef.current?.abort();
            const controller = new AbortController();
            abortRef.current = controller;
            const seq = ++seqRef.current;

            const query = e.target.value;
            const params: Record<string, string> = {
               page: "1",
               per_page: String(pageData.per_page),
               value: query,
            };
            if (extra) {
               Object.entries(extra).forEach(([k, v]) => {
                  params[k] = String(v);
               });
            }

            try {
               const res = await axios.get(
                  `${path}?${new URLSearchParams(params)}`,
                  { signal: controller.signal }
               );
               if (seq !== seqRef.current) {
                  return;
               }
               if (res.data && Array.isArray(res.data.data)) {
                  applySearch(res.data);
               }
            } catch (err: any) {
               if (
                  err?.code === "ERR_CANCELED" ||
                  err?.name === "CanceledError" ||
                  axios.isCancel?.(err)
               ) {
                  return;
               }
            }
         }, 300),
      []
   );

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

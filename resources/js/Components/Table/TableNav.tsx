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
      <div className="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
         {title && (
            <p className="text-base font-semibold text-slate-900">{title}</p>
         )}
         <div className="flex flex-wrap items-center justify-end gap-3">
            {globalSearch && (
               <div className="relative w-full md:max-w-[260px]">
                  <input
                     type="text"
                     placeholder="Arama Yap"
                     onChange={searchHandler}
                     className="h-10 w-full rounded-lg border border-slate-200 py-2 pl-10 pr-3 text-sm font-normal text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                  />
                  <Search className="absolute left-3 top-1/2 z-10 h-4 w-4 -translate-y-1/2 text-slate-400" />
               </div>
            )}

            <TablePageSize
               pageData={data}
               dropdownList={tablePageSizes}
            />

            {component && component}
         </div>
      </div>
   );
};

export default TableNav;

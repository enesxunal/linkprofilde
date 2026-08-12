import {
   Menu,
   Button,
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
      return `text-center py-1 ${current_page === e && "bg-blue-50"}`;
   };

   return (
      <div className={`${props.className}`}>
         <div className="flex md:hidden items-center justify-center mb-4">
            <span className="mr-1">
               Sayfa{" "}
               <strong>
                  {current_page} of {last_page}
               </strong>
            </span>
            <span>|Sayfaya Git:</span>
            <div className="w-[60px] ml-3">
               <Menu placement="bottom-end">
                  <MenuHandler>
                     <Button
                        ripple={false}
                        variant="text"
                        color="white"
                        className="p-0 w-[60px] h-8 rounded-md text-gray-700 border border-gray-200 hover:border-blue-500"
                     >
                        {current_page}
                     </Button>
                  </MenuHandler>
                  <MenuList className="max-h-[200px] min-w-[60px] p-0 overflow-hidden">
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

         <div className="flex items-center justify-center">
            <Button
               color="white"
               variant="text"
               disabled={!prev_page_url}
               onClick={() => gotoRoute(first_page_url)}
               className="active:bg-blue-500 hover:bg-blue-600/90 bg-blue-500 font-medium capitalize rounded-md py-2 px-3"
            >
               {"<<Başa Dön"}
            </Button>

            <Button
               variant="text"
               color="white"
               disabled={!prev_page_url}
               onClick={() => gotoRoute(prev_page_url)}
               className="active:bg-blue-500 hover:bg-blue-600/90 bg-blue-500 font-medium capitalize rounded-md py-2 px-3 mx-3"
            >
               Geri
            </Button>

            <div className="hidden md:flex items-center">
               <span className="mr-1">
                  Sayfa{" "}
                  <strong>
                     {current_page} of {last_page}
                  </strong>
               </span>
               <span>| Sayfaya Git:</span>
               <div className="w-[60px] ml-3">
                  <Menu placement="bottom-end">
                     <MenuHandler>
                        <Button
                           ripple={false}
                           variant="text"
                           color="white"
                           className="p-0 w-[60px] h-8 rounded-md text-gray-700 border border-gray-200 hover:border-blue-500"
                        >
                           {current_page}
                        </Button>
                     </MenuHandler>
                     <MenuList className="max-h-[200px] min-w-[60px] p-0 overflow-hidden">
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

            <Button
               variant="text"
               color="white"
               disabled={!next_page_url}
               onClick={() => gotoRoute(next_page_url)}
               className="active:bg-blue-500 hover:bg-blue-600/90 bg-blue-500 font-medium capitalize rounded-md py-2 px-3 mx-3"
            >
               İleri
            </Button>

            <Button
               variant="text"
               color="white"
               disabled={!next_page_url}
               onClick={() => gotoRoute(last_page_url)}
               className="active:bg-blue-500 hover:bg-blue-600/90 bg-blue-500 font-medium capitalize rounded-md py-2 px-3"
            >
               {"Son>>"}
            </Button>
         </div>
      </div>
   );
};

export default TablePagination;

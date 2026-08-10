import { Head, router } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import UsersIcon from "@/Components/Icons/Users";
import { usersHead } from "@/utils/table-head";
import Breadcrumb from "@/Components/Breadcrumb";
import { useTable, useSortBy } from "react-table";
import TableNav from "@/Components/Table/TableNav";
import TableHead from "@/Components/Table/TableHead";
import { ReactNode, useMemo, useState } from "react";
import UserCircle from "@/Components/Icons/UserCircle";
import { PageProps, PaginationProps, UserProps } from "@/types";
import TablePagination from "@/Components/Table/TablePagination";
import UpdateUser from "@/Components/Admin/UpdateUser";

interface Props extends PageProps {
   users: PaginationProps;
   suspiciousOnly?: boolean;
}

const Users = (props: Props) => {
   const [users, setUsers] = useState(props.users);
   const suspiciousOnly = props.suspiciousOnly ?? false;
   const data = useMemo(() => users.data, [users]);
   const columns = useMemo(() => usersHead, []);

   const { rows, getTableProps, getTableBodyProps, headerGroups, prepareRow } =
      useTable({ columns, data }, useSortBy);

   return (
      <>
         <Head title="Tüm Kullanıcılar" />
         <Breadcrumb Icon={UsersIcon} title="Tüm Kullanıcılar" />

         <div className="card">
            <TableNav
               title={suspiciousOnly ? "Şüpheli Hesaplar" : "Tüm Kayıtlar"}
               data={users}
               globalSearch={true}
               setSearchData={setUsers}
               tablePageSizes={[10, 15, 20, 25]}
               searchPath="/admin/users/search"
               extraSearchParams={suspiciousOnly ? { suspicious: 1 } : undefined}
            />
            <div className="px-7 pb-2 flex gap-2">
               {suspiciousOnly ? (
                  <button
                     type="button"
                     onClick={() => router.get("/admin/users")}
                     className="text-sm text-blue-600 hover:underline"
                  >
                     ← Tümünü göster
                  </button>
               ) : (
                  <button
                     type="button"
                     onClick={() =>
                        router.get("/admin/users", { suspicious: "1" })
                     }
                     className="text-sm px-3 py-1 rounded bg-amber-100 text-amber-800 hover:bg-amber-200"
                  >
                     Şüpheli hesapları listele
                  </button>
               )}
            </div>

            <div className="overflow-x-auto">
               <table {...getTableProps()} className="w-full min-w-[1000px]">
                  <thead>
                     <TableHead headerGroups={headerGroups} />
                  </thead>
                  <tbody {...getTableBodyProps()}>
                     {rows.map((row) => {
                        prepareRow(row);
                        return (
                           <tr
                              {...row.getRowProps()}
                              className="border-b border-gray-200 dark:border-neutral-500"
                           >
                              {row.cells.map((cell) => {
                                 const { row, column } = cell;
                                 const { image, status }: any = row.original;

                                 return (
                                    <td
                                       {...cell.getCellProps()}
                                       className="px-7 py-[18px] text-start last:text-end text-gray-700 "
                                    >
                                       {column.id === "photo" ? (
                                          <>
                                             {image ? (
                                                <img
                                                   src={image}
                                                   className="w-10 h-10 rounded-full"
                                                ></img>
                                             ) : (
                                                <UserCircle className="w-10 h-10 text-gray-600" />
                                             )}
                                          </>
                                       ) : column.id === "status" ? (
                                          <div className="">
                                             <span className="text-sm w-12 py-0.5 px-2 font-medium bg-gray-100 rounded">
                                                {status}
                                             </span>
                                          </div>
                                       ) : column.id === "action" ? (
                                          <div className="flex justify-end items-center">
                                             <UpdateUser
                                                user={row.original as UserProps}
                                                users={users}
                                                setUsers={setUsers}
                                             />
                                          </div>
                                       ) : (
                                          <span
                                             className={`text-sm font-medium`}
                                          >
                                             {cell.render("Cell")}
                                          </span>
                                       )}
                                    </td>
                                 );
                              })}
                           </tr>
                        );
                     })}
                  </tbody>
               </table>
            </div>

            <TablePagination paginationInfo={users} className="p-7" />
         </div>
      </>
   );
};

Users.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Users;

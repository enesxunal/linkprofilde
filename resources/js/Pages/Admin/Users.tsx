import { Head, router } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import { usersHead } from "@/utils/table-head";
import { useTable, useSortBy } from "react-table";
import TableNav from "@/Components/Table/TableNav";
import TableHead from "@/Components/Table/TableHead";
import { ReactNode, useEffect, useMemo, useState } from "react";
import UserCircle from "@/Components/Icons/UserCircle";
import { PageProps, PaginationProps, UserProps } from "@/types";
import TablePagination from "@/Components/Table/TablePagination";
import UpdateUser from "@/Components/Admin/UpdateUser";
import { getTableRowId } from "@/utils/table-row";
import { pageChange } from "@/utils/utils";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import Badge from "@/Components/Panel/Badge";
import EmptyState from "@/Components/Panel/EmptyState";
import UsersIcon from "@/Components/Icons/Users";

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
      useTable({ columns, data, getRowId: getTableRowId }, useSortBy);

   useEffect(() => {
      const change = pageChange(props.users, users);
      if (change) {
         setUsers(props.users);
      }
   }, [props.users]);

   return (
      <>
         <Head title="Tüm Kullanıcılar" />
         <PageHeader
            title="Tüm Kullanıcılar"
            description="Kayıtlı hesapları görüntüleyin ve durumlarını yönetin."
            actions={
               suspiciousOnly ? (
                  <button
                     type="button"
                     onClick={() => router.get("/admin/users")}
                     className="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                  >
                     ← Tümünü göster
                  </button>
               ) : (
                  <button
                     type="button"
                     onClick={() =>
                        router.get("/admin/users", { suspicious: "1" })
                     }
                     className="inline-flex items-center rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-800 hover:bg-amber-100"
                  >
                     Şüpheli hesapları listele
                  </button>
               )
            }
         />

         <PanelCard noPadding>
            <TableNav
               title={suspiciousOnly ? "Şüpheli Hesaplar" : "Tüm Kayıtlar"}
               data={users}
               globalSearch={true}
               setSearchData={setUsers}
               tablePageSizes={[10, 15, 20, 25]}
               searchPath="/admin/users/search"
               extraSearchParams={suspiciousOnly ? { suspicious: 1 } : undefined}
            />

            {rows.length === 0 ? (
               <EmptyState
                  icon={<UsersIcon className="h-6 w-6" />}
                  title="Kullanıcı bulunamadı"
                  description={
                     suspiciousOnly
                        ? "Şüpheli hesap kriterlerine uyan kayıt yok."
                        : "Arama kriterlerinize uygun kullanıcı yok."
                  }
               />
            ) : (
               <div className="overflow-x-auto">
                  <table {...getTableProps()} className="w-full min-w-[1000px]">
                     <thead>
                        <TableHead headerGroups={headerGroups} />
                     </thead>
                     <tbody {...getTableBodyProps()}>
                        {rows.map((row) => {
                           prepareRow(row);
                           const recordId = (row.original as UserProps).id;
                           return (
                              <tr
                                 {...row.getRowProps()}
                                 key={recordId}
                                 className="border-b border-slate-100 hover:bg-slate-50/70"
                              >
                                 {row.cells.map((cell) => {
                                    const { row: cellRow, column } = cell;
                                    const { image, status }: any =
                                       cellRow.original;

                                    return (
                                       <td
                                          {...cell.getCellProps()}
                                          className="px-4 py-3.5 text-start text-slate-700 last:text-end sm:px-6"
                                       >
                                          {column.id === "photo" ? (
                                             <>
                                                {image ? (
                                                   <img
                                                      src={image}
                                                      className="h-10 w-10 rounded-full object-cover"
                                                      alt=""
                                                   />
                                                ) : (
                                                   <UserCircle className="h-10 w-10 text-slate-400" />
                                                )}
                                             </>
                                          ) : column.id === "status" ? (
                                             <Badge
                                                variant={
                                                   status === "active"
                                                      ? "success"
                                                      : status === "banned" ||
                                                        status === "inactive"
                                                      ? "danger"
                                                      : "default"
                                                }
                                             >
                                                {status}
                                             </Badge>
                                          ) : column.id === "action" ? (
                                             <div className="flex items-center justify-end">
                                                <UpdateUser
                                                   key={recordId}
                                                   user={
                                                      cellRow.original as UserProps
                                                   }
                                                   users={users}
                                                   setUsers={setUsers}
                                                />
                                             </div>
                                          ) : (
                                             <span className="text-sm font-medium">
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
            )}

            <TablePagination
               paginationInfo={users}
               className="border-t border-slate-200 px-5 py-4 sm:px-6"
            />
         </PanelCard>
      </>
   );
};

Users.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Users;

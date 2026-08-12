import { Head } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import { useTable, useSortBy } from "react-table";
import TableNav from "@/Components/Table/TableNav";
import TableHead from "@/Components/Table/TableHead";
import { ReactNode, useMemo, useState } from "react";
import { PageProps, PaginationProps } from "@/types";
import { subscriptionsHead } from "@/utils/table-head";
import TablePagination from "@/Components/Table/TablePagination";
import { parseISO, format } from "date-fns";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";
import IdCard from "@/Components/Icons/IdCard";

interface Props extends PageProps {
   subscriptions: PaginationProps;
}

const Subscriptions = (props: Props) => {
   const [subscriptions, setSubscriptions] = useState(props.subscriptions);
   const data = useMemo(() => subscriptions.data, [subscriptions]);
   const columns = useMemo(() => subscriptionsHead, []);

   const { rows, getTableProps, getTableBodyProps, headerGroups, prepareRow } =
      useTable({ columns, data }, useSortBy);

   const stringToDate = (str: string) => {
      const time = format(parseISO(str), "hh:mm aa");
      const date = format(parseISO(str), "dd MMM, yyyy");
      return { date, time };
   };

   return (
      <>
         <Head title="Tüm Abonelikler" />
         <PageHeader
            title="Abonelik Geçmişi"
            description="Ödeme ve abonelik kayıtlarını görüntüleyin."
         />

         <PanelCard noPadding>
            <TableNav
               title="Abonelikler"
               data={subscriptions}
               globalSearch={true}
               setSearchData={setSubscriptions}
               tablePageSizes={[10, 15, 20, 25]}
               searchPath="/admin/subscriptions/search"
            />

            {rows.length === 0 ? (
               <EmptyState
                  icon={<IdCard className="h-6 w-6" />}
                  title="Abonelik bulunamadı"
                  description="Arama kriterlerinize uygun abonelik kaydı yok."
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
                           return (
                              <tr
                                 {...row.getRowProps()}
                                 className="border-b border-slate-100 hover:bg-slate-50/70"
                              >
                                 {row.cells.map((cell) => {
                                    const { row: cellRow, column } = cell;
                                    const {
                                       total_price,
                                       currency,
                                       created_at,
                                    }: any = cellRow.original;

                                    const { date, time } =
                                       stringToDate(created_at);

                                    return (
                                       <td
                                          {...cell.getCellProps()}
                                          className="px-4 py-3.5 text-start text-sm font-medium text-slate-700 last:text-end sm:px-6"
                                       >
                                          {column.id === "price" ? (
                                             <p>{`${total_price} ${currency}`}</p>
                                          ) : column.id === "created" ? (
                                             <p>
                                                <span>{date}</span>
                                                <br />
                                                <span className="text-xs text-slate-500">
                                                   {time}
                                                </span>
                                             </p>
                                          ) : (
                                             <span>{cell.render("Cell")}</span>
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
               paginationInfo={subscriptions}
               className="border-t border-slate-200 px-5 py-4 sm:px-6"
            />
         </PanelCard>
      </>
   );
};

Subscriptions.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Subscriptions;

import Dashboard from "@/Layouts/Dashboard";
import Delete from "@/Components/Icons/Delete";
import { Head, router } from "@inertiajs/react";
import { qrCodesHead } from "@/utils/table-head";
import { useTable, useSortBy } from "react-table";
import TableNav from "@/Components/Table/TableNav";
import { PageProps, PaginationProps } from "@/types";
import TableHead from "@/Components/Table/TableHead";
import { ReactNode, useMemo, useState, useEffect } from "react";
import TablePagination from "@/Components/Table/TablePagination";
import QRCodeDownloader2 from "@/Components/QRCode/QRCodeDownloader2";
import DeleteByInertia from "@/Components/DeleteByInertia";
import QRcode from "@/Components/Icons/QRcode";
import { parseISO, format } from "date-fns";
import { pageChange } from "@/utils/utils";
import LimitWarning from "@/Components/LimitWarning";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";

interface Props extends PageProps {
   qrcodes: PaginationProps;
   limit: boolean | string;
}

const Show = (props: Props) => {
   const [qrcodes, setQRcodes] = useState(props.qrcodes);
   const data = useMemo(() => qrcodes.data, [qrcodes]);
   const columns = useMemo(() => qrCodesHead, []);

   const { rows, getTableProps, getTableBodyProps, headerGroups, prepareRow } =
      useTable({ columns, data }, useSortBy);

   const stringToDate = (str: string) => {
      const time = format(parseISO(str), "hh:mm aa");
      const date = format(parseISO(str), "dd MMM, yyyy");
      return { date, time };
   };

   useEffect(() => {
      const change = pageChange(props.qrcodes, qrcodes);
      if (change) {
         setQRcodes(props.qrcodes);
      }
   }, [props]);

   return (
      <>
         <Head title="QR Kodları" />
         <PageHeader
            title="QR Kodları"
            description="Oluşturduğunuz QR kodlarını yönetin."
            actions={
               <button
                  type="button"
                  onClick={() => router.get("/qrcodes/create")}
                  className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
               >
                  QR Kod Oluştur
               </button>
            }
         />
         <LimitWarning limit={props.limit} />

         <PanelCard noPadding>
            <TableNav
               data={qrcodes}
               globalSearch={false}
               setSearchData={setQRcodes}
               tablePageSizes={[10, 15, 20, 25]}
               title="Tüm QR Kodları"
            />

            {rows.length === 0 ? (
               <EmptyState
                  icon={<QRcode className="h-6 w-6" />}
                  title="QR kod bulunamadı"
                  description="Henüz oluşturulmuş QR kod yok."
               />
            ) : (
               <div className="overflow-x-auto">
                  <table {...getTableProps()} className="w-full min-w-[800px]">
                     <thead>
                        <TableHead justifyHead headerGroups={headerGroups} />
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
                                    const { row, column } = cell;
                                    const {
                                       id,
                                       name,
                                       link,
                                       link_id,
                                       project,
                                       img_data,
                                       project_id,
                                       created_at,
                                    }: any = row.original;
                                    const { date, time } =
                                       stringToDate(created_at);

                                    return (
                                       <td
                                          {...cell.getCellProps()}
                                          className="px-4 py-3.5 text-start text-sm text-slate-700 last:text-end sm:px-6"
                                       >
                                          {column.id === "qrcode" ? (
                                             <img
                                                src={img_data}
                                                className="w-10 h-10"
                                                alt=""
                                             />
                                          ) : column.id === "name" ? (
                                             <p className="text-sm">
                                                {name && name.trim()
                                                   ? name
                                                   : "—"}
                                             </p>
                                          ) : column.id === "project" ? (
                                             <p className="text-sm flex justify-center">
                                                {project && project_id ? (
                                                   project.project_name
                                                ) : (
                                                   <span>—</span>
                                                )}
                                             </p>
                                          ) : column.id === "link" ? (
                                             <p className="text-sm text-center">
                                                {link && link_id
                                                   ? link.link_name
                                                   : "—"}
                                             </p>
                                          ) : column.id === "action" ? (
                                             <div className="flex justify-end items-center">
                                                <QRCodeDownloader2
                                                   imageBlogData={img_data}
                                                />
                                                <DeleteByInertia
                                                   apiPath={`/qrcodes/delete/${id}`}
                                                   Component={
                                                      <button
                                                         type="button"
                                                         className="ml-2 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100"
                                                      >
                                                         <Delete className="h-4 w-4" />
                                                      </button>
                                                   }
                                                />
                                             </div>
                                          ) : (
                                             column.id === "created" && (
                                                <p className="text-sm text-center">
                                                   <span className="font-medium">
                                                      {date}
                                                   </span>
                                                   <br />
                                                   <span className="text-xs text-slate-500">
                                                      {time}
                                                   </span>
                                                </p>
                                             )
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
               paginationInfo={qrcodes}
               className="border-t border-slate-200 px-5 py-4 sm:px-6"
            />
         </PanelCard>
      </>
   );
};

Show.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Show;

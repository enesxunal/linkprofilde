import Dashboard from "@/Layouts/Dashboard";
import Delete from "@/Components/Icons/Delete";
import { Head, Link, router } from "@inertiajs/react";
import { qrCodesHead } from "@/utils/table-head";
import { useTable, useSortBy } from "react-table";
import TableNav from "@/Components/Table/TableNav";
import { PageProps, PaginationProps, QRCodeProps } from "@/types";
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
import Badge from "@/Components/Panel/Badge";

interface Props extends PageProps {
   qrcodes: PaginationProps;
   limit: boolean | string;
}

const truncate = (value: string, max = 48) => {
   if (value.length <= max) return value;
   return `${value.slice(0, max)}…`;
};

const destinationSummary = (row: QRCodeProps) => {
   if (!row.is_dynamic) {
      return row.content ? truncate(String(row.content)) : "—";
   }

   if (row.destination_type === "external") {
      return row.destination_url
         ? truncate(String(row.destination_url))
         : "—";
   }

   const link = row.destination_link;
   if (link) {
      const label = link.link_name || link.url_name || "";
      return label ? truncate(String(label)) : "—";
   }

   return "—";
};

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
                  <table {...getTableProps()} className="w-full min-w-[960px]">
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
                                    const { row: tableRow, column } = cell;
                                    const original =
                                       tableRow.original as QRCodeProps;
                                    const {
                                       id,
                                       name,
                                       img_data,
                                       created_at,
                                       is_dynamic,
                                       is_active,
                                    } = original;
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
                                                className="h-10 w-10"
                                                alt=""
                                             />
                                          ) : column.id === "name" ? (
                                             <div className="space-y-1">
                                                <p className="text-sm font-medium text-slate-900">
                                                   {name && String(name).trim()
                                                      ? name
                                                      : "—"}
                                                </p>
                                                {is_dynamic ? (
                                                   <Badge variant="info">
                                                      Dinamik
                                                   </Badge>
                                                ) : (
                                                   <Badge variant="default">
                                                      Statik (Eski)
                                                   </Badge>
                                                )}
                                             </div>
                                          ) : column.id === "destination" ? (
                                             <p
                                                className="max-w-[220px] truncate text-sm"
                                                title={destinationSummary(
                                                   original
                                                )}
                                             >
                                                {destinationSummary(original)}
                                             </p>
                                          ) : column.id === "status" ? (
                                             <div className="flex justify-center">
                                                {is_dynamic ? (
                                                   <Badge
                                                      variant={
                                                         is_active !== false
                                                            ? "success"
                                                            : "warning"
                                                      }
                                                   >
                                                      {is_active !== false
                                                         ? "Aktif"
                                                         : "Pasif"}
                                                   </Badge>
                                                ) : (
                                                   <span className="text-slate-400">
                                                      —
                                                   </span>
                                                )}
                                             </div>
                                          ) : column.id === "action" ? (
                                             <div className="flex items-center justify-end gap-1">
                                                {is_dynamic ? (
                                                   <Link
                                                      href={`/qrcodes/${id}/destination`}
                                                      className="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100"
                                                   >
                                                      Hedefi Düzenle
                                                   </Link>
                                                ) : null}
                                                <QRCodeDownloader2
                                                   imageBlogData={img_data}
                                                />
                                                <DeleteByInertia
                                                   apiPath={`/qrcodes/delete/${id}`}
                                                   Component={
                                                      <button
                                                         type="button"
                                                         className="ml-1 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100"
                                                      >
                                                         <Delete className="h-4 w-4" />
                                                      </button>
                                                   }
                                                />
                                             </div>
                                          ) : (
                                             column.id === "created" && (
                                                <p className="text-center text-sm">
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

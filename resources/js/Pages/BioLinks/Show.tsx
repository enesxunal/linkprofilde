import { pageChange } from "@/utils/utils";
import Dashboard from "@/Layouts/Dashboard";
import Delete from "@/Components/Icons/Delete";
import LinkIcon from "@/Components/Icons/Link";
import { useTable, useSortBy } from "react-table";
import { bioLinksHead } from "@/utils/table-head";
import TableNav from "@/Components/Table/TableNav";
import TableHead from "@/Components/Table/TableHead";
import EditLink from "@/Components/BioLink/EditLink";
import { Head, Link, router } from "@inertiajs/react";
import ChartLineUp from "@/Components/Icons/ChartLineUp";
import CreateLink from "@/Components/BioLink/CreateLink";
import DeleteByInertia from "@/Components/DeleteByInertia";
import { Button } from "@material-tailwind/react";
import { LinkProps, PageProps, PaginationProps } from "@/types";
import TablePagination from "@/Components/Table/TablePagination";
import { ReactNode, useMemo, useState, useEffect, useRef } from "react";
import { QRCode } from "react-qrcode-logo";
import LimitWarning from "@/Components/LimitWarning";
import { getTableRowId } from "@/utils/table-row";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";

interface Props extends PageProps {
   links: PaginationProps;
   limit: boolean | string;
}

const safePagination: PaginationProps = {
   data: [],
   current_page: 1,
   per_page: 10,
   total: 0,
   last_page: 1,
   first_page_url: "",
   last_page_url: "",
   links: [],
   next_page_url: null,
   prev_page_url: null,
   path: "",
   from: null,
   to: null,
};

const Show = (props: Props) => {
   const [links, setLinks] = useState(props.links ?? safePagination);
   const data = useMemo(() => (links?.data && Array.isArray(links.data) ? links.data : []), [links]);
   const columns = useMemo(() => bioLinksHead, []);
   const [copied, setCopied] = useState<number | null>(null);
   const [createQR, setCreateQR] = useState({
      link_id: null,
      link_url: null,
   });

   const { rows, getTableProps, getTableBodyProps, headerGroups, prepareRow } =
      useTable({ columns, data, getRowId: getTableRowId }, useSortBy);

   const handleCopy = (id: number, url_name: number) => {
      const baseUrl = props.ziggy?.url ?? window.location.origin;
      navigator.clipboard
         .writeText(`${baseUrl}/${url_name}`)
         .then(() => setCopied(id))
         .catch((err) => setCopied(null));
   };

   useEffect(() => {
      if (copied) {
         setTimeout(() => {
            setCopied(null);
         }, 1000);
      }
   }, [copied]);

   useEffect(() => {
      if (props.links && links && props.links !== links) {
         const change = pageChange(props.links, links);
         if (change) setLinks(props.links);
      }
   }, [props]);

   const qrCodeRef: any = useRef(null);
   useEffect(() => {
      if (createQR.link_id && createQR.link_url) {
         const qrCode = qrCodeRef.current.canvas.current.toDataURL();
         // console.log(`${props.ziggy.url}/${createQR.link_url}`);
         if (qrCode) {
            router.post("/qrcodes/create/link-qr", {
               qr_code: qrCode,
               qr_type: "link_qr",
               link_id: createQR.link_id,
               content: `${props.ziggy.url}/${createQR.link_url}`,
            });
         }
         setTimeout(
            () =>
               setCreateQR({
                  link_id: null,
                  link_url: null,
               }),
            500
         );
      }
   }, [createQR]);

   return (
      <>
         <Head title="Profil Linkleri" />
         <PageHeader
            title="Profiller"
            description="Bio link profillerinizi yönetin."
            actions={<CreateLink />}
         />
         <LimitWarning limit={props.limit} />

         {createQR.link_id && createQR.link_url && (
            <div className="absolute invisible">
               <QRCode
                  ref={qrCodeRef}
                  value={`${props.ziggy?.url ?? window.location.origin}/${createQR.link_url}`}
               />
            </div>
         )}

         <PanelCard noPadding>
            <TableNav
               data={links ?? safePagination}
               globalSearch={true}
               setSearchData={setLinks}
               tablePageSizes={[10, 15, 20, 25]}
               searchPath="/bio-links/search"
               title="Profil"
            />

            {rows.length === 0 ? (
               <EmptyState
                  icon={<LinkIcon className="h-6 w-6" />}
                  title="Profil bulunamadı"
                  description="Arama kriterlerinize uygun profil yok."
               />
            ) : (
               <div className="overflow-x-auto">
                  <table {...getTableProps()} className="w-full min-w-[1000px]">
                     <thead>
                        <TableHead justifyHead headerGroups={headerGroups} />
                     </thead>
                     <tbody {...getTableBodyProps()}>
                        {(rows ?? []).map((row) => {
                           prepareRow(row);
                           const cells = row.cells ?? [];
                           const recordId = (row.original as LinkProps).id;
                           return (
                              <tr
                                 {...row.getRowProps()}
                                 key={recordId}
                                 className="border-b border-slate-100 hover:bg-slate-50/70"
                              >
                                 {cells.map((cell) => {
                                    const { row, column } = cell;
                                    const { id, url_name, visited_count, qrcode }: any =
                                       row.original;

                                    return (
                                       <td
                                          {...cell.getCellProps()}
                                          className="px-4 py-3.5 text-start text-sm text-slate-700 last:text-end sm:px-6"
                                       >
                                          {column.id === "customize" ? (
                                             <div className="text-center">
                                                <Link
                                                   href={`/bio-links/customize/${id}`}
                                                   className="rounded-lg bg-blue-50 px-2.5 py-1 text-sm font-medium text-blue-600 hover:bg-blue-100"
                                                >
                                                   Özelleştir
                                                </Link>
                                             </div>
                                          ) : column.id === "visit" ? (
                                             <div className="text-center">
                                                <a
                                                   target="_blank"
                                                   href={`/${url_name}`}
                                                   className="rounded-lg bg-green-50 px-2.5 py-1 text-sm font-medium text-green-600 hover:bg-green-100"
                                                >
                                                   Linki Gör
                                                </a>
                                             </div>
                                          ) : column.id === "view" ? (
                                             <div className="flex justify-center">
                                                <Link
                                                   href={`/link/analytics/${id}`}
                                                   className="inline-flex items-center justify-center rounded-lg bg-slate-50 px-2.5 py-1 text-sm text-slate-700 hover:bg-slate-100"
                                                >
                                                   <ChartLineUp className="text-slate-600" />
                                                   <span className="ml-1">
                                                      {visited_count ?? 0}
                                                   </span>
                                                </Link>
                                             </div>
                                          ) : column.id === "qrcode" ? (
                                             <div className="flex justify-center">
                                                {qrcode ? (
                                                   <img
                                                      className="w-10 h-10 rounded-sm"
                                                      src={qrcode.img_data}
                                                      alt=""
                                                   />
                                                ) : (
                                                   <Button
                                                      variant="text"
                                                      color="white"
                                                      onClick={() =>
                                                         setCreateQR({
                                                            link_id: id,
                                                            link_url: url_name,
                                                         })
                                                      }
                                                      className="flex items-center justify-center whitespace-nowrap rounded-lg bg-slate-50 px-2.5 py-1 text-sm font-medium capitalize text-slate-700 hover:bg-slate-100 active:bg-slate-100"
                                                   >
                                                      QR Oluştur
                                                   </Button>
                                                )}
                                             </div>
                                          ) : column.id === "copy" ? (
                                             <div className="flex justify-center">
                                                <Button
                                                   variant="text"
                                                   color="white"
                                                   onClick={() =>
                                                      handleCopy(id, url_name)
                                                   }
                                                   className="flex items-center justify-center whitespace-nowrap rounded-lg bg-slate-50 px-2.5 py-1 text-sm font-medium capitalize text-slate-700 hover:bg-slate-100 active:bg-slate-100"
                                                >
                                                   {copied === id
                                                      ? "Kopyalandı"
                                                      : "Kopyala"}
                                                </Button>
                                             </div>
                                          ) : column.id === "action" ? (
                                             <div className="flex justify-end items-center">
                                                <EditLink
                                                   key={recordId}
                                                   links={links}
                                                   setLinks={setLinks}
                                                   link={row.original as LinkProps}
                                                />

                                                <DeleteByInertia
                                                   apiPath={`/bio-links/delete/${id}`}
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
                                             <span
                                                className={`text-sm text-slate-700 ${
                                                   column.id === "name" &&
                                                   "font-bold"
                                                }`}
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
            )}

            <TablePagination
               paginationInfo={links ?? safePagination}
               className="border-t border-slate-200 px-5 py-4 sm:px-6"
            />
         </PanelCard>
      </>
   );
};

Show.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Show;

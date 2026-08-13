import Dashboard from "@/Layouts/Dashboard";
import { Head, Link, router } from "@inertiajs/react";
import LinkIcon from "@/Components/Icons/Link";
import Delete from "@/Components/Icons/Delete";
import { useTable, useSortBy } from "react-table";
import TableNav from "@/Components/Table/TableNav";
import { shortLinksHead } from "@/utils/table-head";
import TableHead from "@/Components/Table/TableHead";
import EditLink from "@/Components/ShortLink/EditLink";
import ChartLineUp from "@/Components/Icons/ChartLineUp";
import { Button } from "@material-tailwind/react";
import { LinkProps, PageProps, PaginationProps } from "@/types";
import { ReactNode, useEffect, useMemo, useState, useRef } from "react";
import TablePagination from "@/Components/Table/TablePagination";
import CreateLink from "@/Components/ShortLink/CreateLink";
import DeleteByInertia from "@/Components/DeleteByInertia";
import { pageChange } from "@/utils/utils";
import { QRCode } from "react-qrcode-logo";
import LimitWarning from "@/Components/LimitWarning";
import { getTableRowId } from "@/utils/table-row";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";
import axios from "axios";

interface Props extends PageProps {
   links: PaginationProps;
   limit: boolean | string;
}

const Show = (props: Props) => {
   const [links, setLinks] = useState(props.links);
   const data = useMemo(() => links.data, [links]);
   const columns = useMemo(() => shortLinksHead, []);
   const [copied, setCopied] = useState<number | null>(null);
   const [createQR, setCreateQR] = useState<{
      qr_id: number | null;
      public_url: string | null;
   }>({
      qr_id: null,
      public_url: null,
   });
   const [creatingLinkId, setCreatingLinkId] = useState<number | null>(null);

   const { rows, getTableProps, getTableBodyProps, headerGroups, prepareRow } =
      useTable({ columns, data, getRowId: getTableRowId }, useSortBy);

   const handleCopy = (id: number, url_name: number) => {
      navigator.clipboard
         .writeText(`${props.ziggy.url}/${url_name}`)
         .then(() => setCopied(id))
         .catch(() => setCopied(null));
   };

   useEffect(() => {
      if (copied) {
         setTimeout(() => {
            setCopied(null);
         }, 1000);
      }
   }, [copied]);

   useEffect(() => {
      const change = pageChange(props.links, links);
      if (change) {
         setLinks(props.links);
      }
   }, [props]);

   const qrCodeRef: any = useRef(null);

   const startCreateQR = async (linkId: number) => {
      if (creatingLinkId) return;
      setCreatingLinkId(linkId);
      try {
         const prep = await axios.post("/qrcodes/prepare/link-qr", {
            link_id: linkId,
            qr_type: "link_qr",
         });
         const publicUrl = prep.data.public_url;
         const qrId = prep.data.id;
         // Must be a string URL — never pass objects into QR value.
         if (typeof publicUrl !== "string" || !qrId) {
            throw new Error("invalid prepare response");
         }
         setCreateQR({ qr_id: qrId, public_url: publicUrl });
      } catch {
         setCreatingLinkId(null);
      }
   };

   useEffect(() => {
      if (!createQR.qr_id || !createQR.public_url) return;

      const finalize = async () => {
         await new Promise<void>((resolve) => {
            requestAnimationFrame(() => {
               requestAnimationFrame(() => {
                  setTimeout(() => resolve(), 50);
               });
            });
         });

         try {
            const canvas = qrCodeRef.current?.canvas?.current;
            const qrCode = canvas?.toDataURL?.();
            if (
               qrCode &&
               typeof createQR.public_url === "string" &&
               !createQR.public_url.includes("[object Object]")
            ) {
               await axios.post(`/qrcodes/${createQR.qr_id}/finalize`, {
                  qr_code: qrCode,
               });
               router.reload({ only: ["links", "limit"] });
            }
         } finally {
            setCreateQR({ qr_id: null, public_url: null });
            setCreatingLinkId(null);
         }
      };

      void finalize();
   }, [createQR]);

   return (
      <>
         <Head title="Kısa Linkler" />
         <PageHeader
            title="Kısa Linkler"
            description="Kısa linklerinizi oluşturun ve yönetin."
            actions={<CreateLink />}
         />
         <LimitWarning limit={props.limit} />

         {createQR.qr_id &&
         createQR.public_url &&
         typeof createQR.public_url === "string" ? (
            <div className="absolute invisible">
               <QRCode ref={qrCodeRef} value={createQR.public_url} />
            </div>
         ) : null}

         <PanelCard noPadding>
            <TableNav
               data={links}
               globalSearch={true}
               setSearchData={setLinks}
               tablePageSizes={[10, 15, 20, 25]}
               searchPath={"/short-links/search"}
               title="Tüm Kısa Linkler"
            />

            {rows.length === 0 ? (
               <EmptyState
                  icon={<LinkIcon className="h-6 w-6" />}
                  title="Kısa link bulunamadı"
                  description="Arama kriterlerinize uygun kısa link yok."
               />
            ) : (
               <div className="overflow-x-auto">
                  <table {...getTableProps()} className="w-full min-w-[1000px]">
                     <thead>
                        <TableHead justifyHead headerGroups={headerGroups} />
                     </thead>
                     <tbody {...getTableBodyProps()}>
                        {rows.map((row) => {
                           prepareRow(row);
                           const recordId = (row.original as LinkProps).id;
                           return (
                              <tr
                                 {...row.getRowProps()}
                                 key={recordId}
                                 className="border-b border-slate-100 hover:bg-slate-50/70"
                              >
                                 {row.cells.map((cell) => {
                                    const { row, column } = cell;
                                    const {
                                       id,
                                       qrcode,
                                       visited,
                                       url_name,
                                       link_name,
                                    }: any = row.original;

                                    return (
                                       <td
                                          {...cell.getCellProps()}
                                          className="px-4 py-3.5 text-start text-sm text-slate-700 last:text-end sm:px-6"
                                       >
                                          {column.id === "url" ? (
                                             <a
                                                target="_blank"
                                                href={`${props.ziggy.url}/${url_name}`}
                                                className="text-sm font-medium underline"
                                             >
                                                {`${props.ziggy.url}/${url_name}`}
                                             </a>
                                          ) : column.id === "name" ? (
                                             <p className="text-center text-sm font-medium">
                                                {link_name}
                                             </p>
                                          ) : column.id === "view" ? (
                                             <div className="flex justify-center">
                                                <Link
                                                   href={`/link/analytics/${id}`}
                                                   className="inline-flex items-center justify-center rounded-lg bg-slate-50 px-2.5 py-1 text-sm text-slate-700 hover:bg-slate-100"
                                                >
                                                   <ChartLineUp className="text-slate-600" />
                                                   <span className="ml-1">
                                                      {visited.length}
                                                   </span>
                                                </Link>
                                             </div>
                                          ) : column.id === "qrcode" ? (
                                             <div className="flex justify-center">
                                                {qrcode ? (
                                                   <img
                                                      className="h-10 w-10 rounded-sm"
                                                      src={qrcode.img_data}
                                                      alt=""
                                                   />
                                                ) : (
                                                   <Button
                                                      variant="text"
                                                      color="white"
                                                      disabled={
                                                         creatingLinkId === id
                                                      }
                                                      onClick={() =>
                                                         startCreateQR(id)
                                                      }
                                                      className="flex items-center justify-center whitespace-nowrap rounded-lg bg-slate-50 px-2.5 py-1 text-sm font-medium capitalize text-slate-700 hover:bg-slate-100 active:bg-slate-100"
                                                   >
                                                      {creatingLinkId === id
                                                         ? "Oluşturuluyor..."
                                                         : "Qr Oluştur"}
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
                                             <div className="flex items-center justify-end">
                                                <EditLink
                                                   key={recordId}
                                                   links={links}
                                                   setLinks={setLinks}
                                                   link={
                                                      row.original as LinkProps
                                                   }
                                                />

                                                <DeleteByInertia
                                                   apiPath={`/short-links/delete/${id}`}
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
                                          ) : null}
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
               paginationInfo={links}
               className="border-t border-slate-200 px-5 py-4 sm:px-6"
            />
         </PanelCard>
      </>
   );
};

Show.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Show;

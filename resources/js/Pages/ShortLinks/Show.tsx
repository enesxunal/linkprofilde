import Dashboard from "@/Layouts/Dashboard";
import { Head, Link, router } from "@inertiajs/react";
import LinkIcon from "@/Components/Icons/Link";
import Delete from "@/Components/Icons/Delete";
import Breadcrumb from "@/Components/Breadcrumb";
import { useTable, useSortBy } from "react-table";
import TableNav from "@/Components/Table/TableNav";
import { shortLinksHead } from "@/utils/table-head";
import TableHead from "@/Components/Table/TableHead";
import EditLink from "@/Components/ShortLink/EditLink";
import ChartLineUp from "@/Components/Icons/ChartLineUp";
import { Button, IconButton } from "@material-tailwind/react";
import { LinkProps, PageProps, PaginationProps } from "@/types";
import { ReactNode, useEffect, useMemo, useState, useRef } from "react";
import TablePagination from "@/Components/Table/TablePagination";
import CreateLink from "@/Components/ShortLink/CreateLink";
import DeleteByInertia from "@/Components/DeleteByInertia";
import { pageChange } from "@/utils/utils";
import { QRCode } from "react-qrcode-logo";
import LimitWarning from "@/Components/LimitWarning";
import { getTableRowId } from "@/utils/table-row";

interface Props extends PageProps {
   links: PaginationProps;
   limit: boolean | string;
}

const Show = (props: Props) => {
   const [links, setLinks] = useState(props.links);
   const data = useMemo(() => links.data, [links]);
   const columns = useMemo(() => shortLinksHead, []);
   const [copied, setCopied] = useState<number | null>(null);
   const [createQR, setCreateQR] = useState({
      link_id: null,
      link_url: null,
   });

   const { rows, getTableProps, getTableBodyProps, headerGroups, prepareRow } =
      useTable({ columns, data, getRowId: getTableRowId }, useSortBy);

   const handleCopy = (id: number, url_name: number) => {
      navigator.clipboard
         .writeText(`${props.ziggy.url}/${url_name}`)
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
      const change = pageChange(props.links, links);
      if (change) {
         setLinks(props.links);
      }
   }, [props]);

   const qrCodeRef: any = useRef(null);
   useEffect(() => {
      if (createQR.link_id && createQR.link_url) {
         const qrCode = qrCodeRef.current.canvas.current.toDataURL();
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
         <Head title="Kısa Linkler" />
         <Breadcrumb
            Icon={LinkIcon}
            title="Kısa Linkler"
            Component={<CreateLink />}
         />
         <LimitWarning limit={props.limit} />

         {createQR && (
            <div className="absolute invisible">
               <QRCode
                  ref={qrCodeRef}
                  value={`${props.ziggy.url}/${createQR}`}
               />
            </div>
         )}

         <div className="card">
            <TableNav
               data={links}
               globalSearch={true}
               setSearchData={setLinks}
               tablePageSizes={[10, 15, 20, 25]}
               searchPath={"/short-links/search"}
               title="Tüm Kısa Linkler"
            />

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
                              className="border-b border-gray-200 dark:border-neutral-500"
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
                                       className="px-7 py-[18px] text-start last:text-end"
                                    >
                                       {column.id === "url" ? (
                                          <a
                                             target="_blank"
                                             href={`${props.ziggy.url}/${url_name}`}
                                             className="text-sm underline font-medium"
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
                                                className="text-sm w-12 py-0.5 flex items-center justify-center bg-gray-100 rounded"
                                             >
                                                <ChartLineUp className="text-gray-700" />
                                                <span className="ml-1">
                                                   {visited.length}
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
                                                   className="text-sm py-[3px] px-2 flex text-gray-800 items-center justify-center bg-gray-100 hover:bg-gray-100 active:bg-gray-200 rounded whitespace-nowrap capitalize font-medium"
                                                >
                                                   Qr Oluştur
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
                                                className="text-sm py-[3px] px-2 flex text-gray-800 items-center justify-center bg-gray-100 hover:bg-gray-100 active:bg-gray-200 rounded whitespace-nowrap capitalize font-medium"
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
                                                apiPath={`/short-links/delete/${id}`}
                                                Component={
                                                   <IconButton
                                                      color="white"
                                                      variant="text"
                                                      className="w-7 h-7 rounded-full bg-red-50 hover:bg-red-50 active:bg-red-50 text-red-500 ml-3"
                                                   >
                                                      <Delete className="h-4 w-4" />
                                                   </IconButton>
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

            <TablePagination paginationInfo={links} className="p-7" />
         </div>
      </>
   );
};

Show.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Show;

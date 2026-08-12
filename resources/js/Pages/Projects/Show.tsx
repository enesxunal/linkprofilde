import { pageChange } from "@/utils/utils";
import Dashboard from "@/Layouts/Dashboard";
import { Head, Link } from "@inertiajs/react";
import Delete from "@/Components/Icons/Delete";
import { useTable, useSortBy } from "react-table";
import { projectsHead } from "@/utils/table-head";
import TableNav from "@/Components/Table/TableNav";
import TableHead from "@/Components/Table/TableHead";
import ProjectsIcon from "@/Components/Icons/Projects";
import DeleteByInertia from "@/Components/DeleteByInertia";
import { PageProps, PaginationProps, ProjectProps } from "@/types";
import TablePagination from "@/Components/Table/TablePagination";
import { ReactNode, useMemo, useState, useEffect } from "react";
import CreateProject from "@/Components/Project/CreateProject";
import EditProject from "@/Components/Project/EditProject";
import { parseISO, format } from "date-fns";
import LimitWarning from "@/Components/LimitWarning";
import { getTableRowId } from "@/utils/table-row";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";

interface Props extends PageProps {
   projects: PaginationProps;
   limit: boolean | string;
}

const Projects = (props: Props) => {
   const [projects, setProjects] = useState(props.projects);
   const data = useMemo(() => projects.data, [projects]);
   const columns = useMemo(() => projectsHead, []);

   const { rows, getTableProps, getTableBodyProps, headerGroups, prepareRow } =
      useTable({ columns, data, getRowId: getTableRowId }, useSortBy);

   const stringToDate = (str: string) => {
      const time = format(parseISO(str), "hh:mm aa");
      const date = format(parseISO(str), "dd MMM, yyyy");
      return { date, time };
   };

   useEffect(() => {
      const change = pageChange(props.projects, projects);
      if (change) {
         setProjects(props.projects);
      }
   }, [props]);

   return (
      <>
         <Head title="Proje Linkleri" />
         <PageHeader
            title="Projeler"
            description="QR kodlarınızı projeler altında gruplayın."
            actions={<CreateProject />}
         />
         <LimitWarning limit={props.limit} />

         <PanelCard noPadding>
            <TableNav
               data={projects}
               globalSearch={true}
               setSearchData={setProjects}
               tablePageSizes={[10, 15, 20, 25]}
               searchPath="/projects/search"
               title="Tüm Projeler"
            />

            {rows.length === 0 ? (
               <EmptyState
                  icon={<ProjectsIcon className="h-6 w-6" />}
                  title="Proje bulunamadı"
                  description="Arama kriterlerinize uygun proje yok."
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
                           const recordId = (row.original as ProjectProps).id;
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
                                       qrcodes,
                                       created_at,
                                       project_name,
                                    }: any = row.original;
                                    const { date, time } =
                                       stringToDate(created_at);

                                    return (
                                       <td
                                          {...cell.getCellProps()}
                                          className="px-4 py-3.5 text-start text-sm text-slate-700 last:text-end sm:px-6"
                                       >
                                          {column.id === "name" ? (
                                             <p className="text-sm font-medium">
                                                {project_name}
                                             </p>
                                          ) : column.id === "qrcodes" ? (
                                             <p className="text-center font-medium">
                                                {qrcodes.length}
                                             </p>
                                          ) : column.id === "view" ? (
                                             <>
                                                {qrcodes.length > 0 ? (
                                                   <div className="flex justify-center">
                                                      <Link
                                                         href={`/projects/qrcodes/${id}`}
                                                         className="inline-flex w-24 items-center justify-center whitespace-nowrap rounded-lg bg-slate-50 px-2.5 py-1 text-sm font-medium text-slate-700 hover:bg-slate-100"
                                                      >
                                                         QR Görüntüle
                                                      </Link>
                                                   </div>
                                                ) : (
                                                   <div className="flex justify-center">
                                                      <span className="inline-flex w-20 items-center justify-center whitespace-nowrap rounded-lg bg-slate-50 px-2.5 py-1 text-sm font-medium text-slate-500">
                                                         Boş
                                                      </span>
                                                   </div>
                                                )}
                                             </>
                                          ) : column.id === "action" ? (
                                             <div className="flex justify-end items-center">
                                                <EditProject
                                                   key={recordId}
                                                   projects={projects}
                                                   setProjects={setProjects}
                                                   project={
                                                      row.original as ProjectProps
                                                   }
                                                />

                                                <DeleteByInertia
                                                   apiPath={`/projects/delete/${id}`}
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
               paginationInfo={projects}
               className="border-t border-slate-200 px-5 py-4 sm:px-6"
            />
         </PanelCard>
      </>
   );
};

Projects.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Projects;

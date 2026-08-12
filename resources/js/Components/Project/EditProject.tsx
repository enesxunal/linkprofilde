import axios from "axios";
import Input from "../Input";
import EditPen from "../Icons/EditPen";
import { useForm } from "@inertiajs/react";
import { FormEventHandler, useEffect, useState } from "react";
import { PaginationProps, ProjectProps } from "@/types";
import { Dialog } from "@material-tailwind/react";
import { error, success } from "@/utils/toast";

interface Props {
   project: ProjectProps;
   projects: PaginationProps;
   setProjects: (res: PaginationProps) => void;
}

const EditProject = (props: Props) => {
   const { project, projects, setProjects } = props;
   const [open, setOpen] = useState(false);

   const handleOpen = () => {
      setOpen((prev) => !prev);
   };

   const { data, setData } = useForm({
      project_name: project.project_name,
   });

   const [nameError, setNameError] = useState<string | null>(null);

   useEffect(() => {
      setData({
         project_name: project.project_name,
      });
      setNameError(null);
   }, [project.id]);

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const submit: FormEventHandler = async (e) => {
      e.preventDefault();
      setNameError(null);

      try {
         const res = await axios.put(`/projects/update/${project.id}`, data);
         if (res.data.error) {
            error(res.data.error);
         } else if (res.data.success && res.data.project) {
            handleOpen();
            success(res.data.success);

            const updatedProjects = projects.data.map((item) => {
               return item.id === res.data.project.id
                  ? { ...item, project_name: res.data.project.project_name }
                  : item;
            });

            setProjects({
               ...projects,
               data: updatedProjects,
            });
         }
      } catch (error: any) {
         const { projects_name } = error.response.data.errors;
         if (projects_name) setNameError(projects_name);
      }
   };

   return (
      <>
         <button
            type="button"
            onClick={handleOpen}
            className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100"
            aria-label="Projeyi düzenle"
         >
            <EditPen className="h-4 w-4" />
         </button>

         <Dialog
            size="sm"
            open={open}
            handler={handleOpen}
            className="mx-4 max-h-[calc(100vh-80px)] overflow-y-auto rounded-xl border border-slate-200 bg-white p-6 text-slate-800 shadow-sm sm:mx-0"
         >
            <div className="mb-6 flex items-center justify-between">
               <p className="text-lg font-semibold text-slate-900">
                  Projeyi Güncelle
               </p>
               <button
                  type="button"
                  onClick={handleOpen}
                  className="text-2xl leading-none text-slate-400 hover:text-slate-700"
                  aria-label="Kapat"
               >
                  ×
               </button>
            </div>

            <form onSubmit={submit}>
               <div className="mb-4">
                  <Input
                     type="text"
                     error={nameError}
                     name="project_name"
                     label="Proje Adı"
                     value={data.project_name}
                     onChange={onHandleChange}
                     placeholder="Proje adını girin"
                     fullWidth
                     required
                  />
               </div>

               <div className="mt-4 flex justify-end gap-2">
                  <button
                     type="button"
                     onClick={handleOpen}
                     className="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                  >
                     İptal
                  </button>
                  <button
                     type="submit"
                     className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                  >
                     Kaydet
                  </button>
               </div>
            </form>
         </Dialog>
      </>
   );
};

export default EditProject;

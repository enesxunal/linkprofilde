import axios from "axios";
import Input from "../Input";
import EditPen from "../Icons/EditPen";
import { useForm } from "@inertiajs/react";
import { FormEventHandler, useState } from "react";
import { PaginationProps, ProjectProps } from "@/types";
import { Button, Dialog, IconButton } from "@material-tailwind/react";
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

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const [nameError, setNameError] = useState<string | null>(null);

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
         <IconButton
            variant="text"
            color="white"
            onClick={handleOpen}
            className="w-7 h-7 rounded-full bg-blue-50 hover:bg-blue-50 active:bg-blue-50 text-blue-500"
         >
            <EditPen className="h-4 w-4" />
         </IconButton>

         <Dialog
            size="sm"
            open={open}
            handler={handleOpen}
            className="p-6 max-h-[calc(100vh-80px)] overflow-y-auto text-gray-800"
         >
            <div className="flex items-center justify-between mb-6">
               <p className="text-xl font-medium">Update Project</p>
               <span
                  onClick={handleOpen}
                  className="text-3xl leading-none cursor-pointer"
               >
                  ×
               </span>
            </div>

            <form onSubmit={submit}>
               <div className="mb-4">
                  <Input
                     type="text"
                     error={nameError}
                     name="project_name"
                     label="Kısa Link Adı"
                     value={data.project_name}
                     onChange={onHandleChange}
                     placeholder="Enter the project name"
                     fullWidth
                     required
                  />
               </div>

               <div className="flex justify-end mt-4">
                  <Button
                     color="red"
                     variant="text"
                     onClick={handleOpen}
                     className="py-2 font-medium capitalize text-base mr-2"
                  >
                     <span>Cancel</span>
                  </Button>
                  <Button
                     type="submit"
                     color="blue"
                     variant="gradient"
                     className="py-2 font-medium capitalize text-base"
                  >
                     <span>Save Changes</span>
                  </Button>
               </div>
            </form>
         </Dialog>
      </>
   );
};

export default EditProject;

import Input from "@/Components/Input";
import { useForm } from "@inertiajs/react";
import { Button } from "@material-tailwind/react";
import { FormEventHandler } from "react";

const ChangePassword = () => {
   const { data, setData, post, errors, clearErrors } = useForm({
      current_password: "",
      password: "",
      password_confirmation: "",
   });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const submit: FormEventHandler = (e) => {
      e.preventDefault();
      clearErrors();
      post(route("password.change"));
   };

   return (
      <div className="card max-w-[1000px] w-full mx-auto my-7">
         <div className="px-7 pt-7 pb-4 border-b border-b-gray-200">
            <p className="text18 font-bold text-gray-900">Change Password</p>
         </div>
         <form onSubmit={submit} className="p-7">
            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="current_password"
                  label="Current Password"
                  value={data.current_password}
                  error={errors.current_password}
                  placeholder="Enter your current password"
                  onChange={onHandleChange}
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="password"
                  label="New Password"
                  value={data.password}
                  error={errors.password}
                  placeholder="Enter your new password"
                  onChange={onHandleChange}
                  flexLabel
                  required
               />
            </div>

            <div className="mb-7">
               <Input
                  fullWidth
                  type="password"
                  name="password_confirmation"
                  value={data.password_confirmation}
                  placeholder="Retype your new password"
                  onChange={onHandleChange}
                  label="Re-type Password"
                  flexLabel
                  required
               />
            </div>

            <div className="flex items-center mt-6 md:pl-[164px]">
               <Button
                  type="submit"
                  color="blue"
                  variant="gradient"
                  className="py-2.5 px-5 rounded-md font-medium capitalize text-sm hover:shadow-md"
               >
                  Change Password
               </Button>
            </div>
         </form>
      </div>
   );
};

export default ChangePassword;

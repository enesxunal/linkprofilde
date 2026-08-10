import Input from "@/Components/Input";
import { PageProps } from "@/types";
import { useForm, usePage } from "@inertiajs/react";
import { Button, Card } from "@material-tailwind/react";
import { FormEventHandler } from "react";

const ForgetPassword = () => {
   const { props } = usePage<PageProps>();
   const { email } = props.auth.user;

   const { data, setData, post, errors, clearErrors } = useForm({
      email: email,
   });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const submit: FormEventHandler = (e) => {
      e.preventDefault();
      clearErrors();
      post(route("password.email"));
   };

   return (
      <div className="card max-w-[1000px] w-full mx-auto">
         <div className="px-7 pt-7 pb-4 border-b border-b-gray-200">
            <p className="text18 font-bold text-gray-900">Forget Password</p>
         </div>
         <form onSubmit={submit} className="p-7">
            <div className="mb-7">
               <Input
                  fullWidth
                  type="email"
                  name="email"
                  value={data.email}
                  error={errors.email}
                  placeholder="Enter your email"
                  onChange={onHandleChange}
                  label="Email Address"
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
                  Get Password Change Link
               </Button>
            </div>
         </form>
      </div>
   );
};

export default ForgetPassword;

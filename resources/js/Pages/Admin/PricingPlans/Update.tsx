import Input from "@/Components/Input";
import Breadcrumb from "@/Components/Breadcrumb";
import { Head, useForm } from "@inertiajs/react";
import InputDropdown from "@/Components/InputDropdown";
import { Button, Card, Checkbox } from "@material-tailwind/react";
import Dashboard from "@/Layouts/Dashboard";
import { ReactNode, FormEventHandler } from "react";
import { PlanProps } from "@/types";
import Pricing from "@/Components/Icons/Pricing";

const LimitsizCheckBox = ({
   onHandler,
   name,
}: {
   onHandler: any;
   name: string;
}) => {
   return (
      <div className="flex items-center absolute top-0 right-0">
         <label className="text-sm whitespace-nowrap flex items-center font-medium text-gray-500 mr-2">
            Limitsiz
         </label>
         <Checkbox
            ripple={false}
            color="indigo"
            name={name}
            className="hover:before:opacity-0 w-3.5 h-3.5 rounded"
            containerProps={{ className: "p-0" }}
            onChange={onHandler}
         />
      </div>
   );
};

const Create = ({ plan }: { plan: PlanProps }) => {
   const { data, setData, put, errors, clearErrors } = useForm({
      name: plan.name,
      description: plan.description,
      monthly_price: plan.monthly_price,
      yearly_price: plan.yearly_price,
      currency: plan.currency,
      status: plan.status,
      biolinks: plan.biolinks,
      biolink_blocks: plan.biolink_blocks,
      shortlinks: plan.shortlinks,
      projects: plan.projects,
      qrcodes: plan.qrcodes,
      themes: plan.themes,
      custom_theme: plan.custom_theme,
      support: plan.support,
   });

   const onHandleChange = (event: any) => {
      if (event.target.type === "checkbox") {
         if (event.target.checked) {
            setData(event.target.name, "Limitsiz");
         } else {
            setData(event.target.name, null);
         }
      } else {
         setData(event.target.name, event.target.value);
      }
   };

   const submit: FormEventHandler = (e) => {
      e.preventDefault();
      clearErrors();

      put(route("plan.update", [{ id: plan.id }]));
   };

   const planType = [
      { key: "Basic", value: "BASIC" },
      { key: "Standard", value: "STANDARD" },
      { key: "Premium", value: "PREMIUM" },
   ];

   const themesList = [
      { key: "Basic Only", value: "Free" },
      { key: "Standard (Free Themes Included)", value: "Standard" },
      { key: "Premium (All Themes Included)", value: "Premium" },
   ];

   let blockList = [];
   for (let i = 0; i < 10; i++) {
      const obj = { key: i, value: i };
      blockList.push(obj);
   }

   return (
      <>
         <Head title="Abonelik Planı Güncelle" />
         <Breadcrumb Icon={Pricing} title="Abonelik Planı Güncelle" />

         <Card className="shadow-card max-w-[1000px] w-full mx-auto">
            <div className="px-7 pt-7 pb-4 border-b border-b-gray-200">
               <p className="text18 font-bold text-gray-900">
                  Create New Subscription Plan
               </p>
            </div>
            <form onSubmit={submit} className="p-7">
               <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                  <div>
                     <InputDropdown
                        required
                        fullWidth
                        name="name"
                        label="Plan Name"
                        error={errors.name}
                        defaultValue={data.name}
                        onChange={(e: any) => setData("name", e.value)}
                        itemList={planType}
                     />
                  </div>
                  <div>
                     <Input
                        type="text"
                        fullWidth
                        name="description"
                        label="Description"
                        value={data.description}
                        error={errors.description}
                        placeholder="Write a short description"
                        onChange={onHandleChange}
                        maxLength={100}
                        required
                     />
                  </div>
                  <div>
                     <Input
                        fullWidth
                        type="number"
                        name="monthly_price"
                        label="Monthly Price"
                        value={data.monthly_price as any}
                        error={errors.monthly_price}
                        placeholder="Monthly subscription plan"
                        onChange={onHandleChange}
                        required
                     />
                  </div>
                  <div>
                     <Input
                        fullWidth
                        type="number"
                        name="yearly_price"
                        label="Yearly Price"
                        value={data.yearly_price as any}
                        error={errors.yearly_price}
                        placeholder="Yearly subscription plan"
                        onChange={onHandleChange}
                        required
                     />
                  </div>
                  <div>
                     <InputDropdown
                        required
                        fullWidth
                        name="currency"
                        label="Currency"
                        error={errors.currency}
                        defaultValue={data.currency}
                        onChange={(e: any) => setData("currency", e.value)}
                        itemList={[{ key: "TL", value: "TL" }]}
                     />
                  </div>
                  <div>
                     <InputDropdown
                        required
                        fullWidth
                        name="status"
                        label="Plan Status"
                        error={errors.status}
                        defaultValue={data.status}
                        onChange={(e: any) => setData("status", e.value)}
                        itemList={[
                           { key: "Active", value: "active" },
                           { key: "Deactive", value: "deactive" },
                        ]}
                     />
                  </div>
               </div>

               <p className="text18 font-bold text-gray-900 mb-4">
                  Subscription Plan Features
               </p>
               <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                  <div className="relative">
                     <LimitsizCheckBox
                        name={"biolinks"}
                        onHandler={onHandleChange}
                     />
                     <Input
                        required
                        fullWidth
                        name="biolinks"
                        label="Profil Oluşturma"
                        error={errors.biolinks}
                        onChange={onHandleChange}
                        value={data.biolinks as any}
                        placeholder=""
                        type={data.biolinks === "Limitsiz" ? "text" : "number"}
                        disabled={data.biolinks === "Limitsiz" ? true : false}
                     />
                  </div>
                  
                  <div className="relative">
                     <LimitsizCheckBox
                        name={"shortlinks"}
                        onHandler={onHandleChange}
                     />
                     <Input
                        fullWidth
                        required
                        name="shortlinks"
                        error={errors.shortlinks}
                        value={data.shortlinks as any}
                        placeholder=""
                        onChange={onHandleChange}
                        label="Kısa Link Oluşturma"
                        type={
                           data.shortlinks === "Limitsiz" ? "text" : "number"
                        }
                        disabled={
                           data.shortlinks === "Limitsiz" ? true : false
                        }
                     />
                  </div>
                  
                  <div className="relative">
                     <LimitsizCheckBox
                        name={"qrcodes"}
                        onHandler={onHandleChange}
                     />
                     <Input
                        fullWidth
                        required
                        name="qrcodes"
                        label="QR Kod Oluşturma"
                        onChange={onHandleChange}
                        error={errors.qrcodes}
                        value={data.qrcodes as any}
                        placeholder=""
                        type={data.qrcodes === "Limitsiz" ? "text" : "number"}
                        disabled={data.qrcodes === "Limitsiz" ? true : false}
                     />
                  </div>
                  <div>
                     <InputDropdown
                        required
                        fullWidth
                        name="themes"
                        error={errors.themes}
                        defaultValue={data.themes}
                        onChange={(e: any) => setData("themes", e.value)}
                        itemList={themesList}
                        label="Theme Access"
                     />
                  </div>
                  <div>
                     <InputDropdown
                        required
                        fullWidth
                        name="custom_theme"
                        error={errors.custom_theme}
                        defaultValue={data.custom_theme as any}
                        onChange={(e: any) => setData("custom_theme", e.value)}
                        label="Custom Theme Create Access"
                        itemList={[
                           { key: "True", value: 1 },
                           { key: "False", value: 0 },
                        ]}
                     />
                  </div>
                  <div>
                     <InputDropdown
                        required
                        fullWidth
                        name="support"
                        error={errors.support}
                        defaultValue={data.support}
                        onChange={(e: any) => setData("support", e.value)}
                        label="Support"
                        itemList={[
                           { key: "24", value: 24 },
                           { key: "48", value: 48 },
                           { key: "72", value: 72 },
                        ]}
                     />
                  </div>
               </div>

               <div className="flex items-center">
                  <Button
                     type="submit"
                     color="blue"
                     variant="gradient"
                     className="py-2.5 px-5 rounded-md font-medium capitalize text-sm hover:shadow-md"
                  >
                     Save Changes
                  </Button>
               </div>
            </form>
         </Card>
      </>
   );
};

Create.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Create;

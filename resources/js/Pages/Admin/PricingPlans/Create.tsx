import Input from "@/Components/Input";
import Breadcrumb from "@/Components/Breadcrumb";
import { Head, useForm } from "@inertiajs/react";
import InputDropdown from "@/Components/InputDropdown";
import { Button, Card, Checkbox } from "@material-tailwind/react";
import { ReactNode, FormEventHandler } from "react";
import Pricing from "@/Components/Icons/Pricing";
import Dashboard from "@/Layouts/Dashboard";

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

const Create = () => {
   const { data, setData, post, errors, clearErrors } = useForm({
      name: "BASIC",
      description: "",
      monthly_price: null,
      yearly_price: null,
      currency: "USD",
      status: "active",
      biolinks: null,
      biolink_blocks: 0,
      shortlinks: null,
      projects: null,
      qrcodes: null,
      themes: "Free",
      custom_theme: 0,
      support: 24,
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

      console.log(data);
      post(route("plan.store"));
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
         <Head title="Yeni Abonelik Planı" />
         <Breadcrumb Icon={Pricing} title="Yeni Abonelik Planı" />

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
                        itemList={[{ key: "USD", value: "USD" }]}
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
                        label="Biolink Create"
                        error={errors.biolinks}
                        onChange={onHandleChange}
                        value={data.biolinks as any}
                        placeholder="Max limit of create biolinks"
                        type={data.biolinks === "Limitsiz" ? "text" : "number"}
                        disabled={data.biolinks === "Limitsiz" ? true : false}
                     />
                  </div>
                  <div className="relative">
                     <InputDropdown
                        required
                        fullWidth
                        name="biolink_blocks"
                        label="Biolink Blocks Access"
                        error={errors.biolink_blocks}
                        defaultValue={data.biolink_blocks}
                        onChange={(e: any) => setData("status", e.value)}
                        itemList={blockList}
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
                        placeholder="Max limit of create shortlinks"
                        onChange={onHandleChange}
                        label="Shortlink Create"
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
                        name="projects"
                        onHandler={onHandleChange}
                     />
                     <Input
                        fullWidth
                        required
                        name="projects"
                        label="Project Create"
                        onChange={onHandleChange}
                        error={errors.projects}
                        value={data.projects as any}
                        placeholder="Max limit of create projects"
                        type={data.projects === "Limitsiz" ? "text" : "number"}
                        disabled={data.projects === "Limitsiz" ? true : false}
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
                        label="QRCode Create"
                        onChange={onHandleChange}
                        error={errors.qrcodes}
                        value={data.qrcodes as any}
                        placeholder="Qr kodları oluşturmanın maksimum sınırı "
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
                        defaultValue={data.custom_theme}
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
                     Save New Plan
                  </Button>
               </div>
            </form>
         </Card>
      </>
   );
};

Create.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Create;

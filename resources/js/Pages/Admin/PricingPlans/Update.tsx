import Input from "@/Components/Input";
import { Head, useForm } from "@inertiajs/react";
import InputDropdown from "@/Components/InputDropdown";
import { Checkbox } from "@material-tailwind/react";
import Dashboard from "@/Layouts/Dashboard";
import { ReactNode, FormEventHandler } from "react";
import { PlanProps } from "@/types";
import PageHeader from "@/Components/Panel/PageHeader";

const LimitsizCheckBox = ({
   onHandler,
   name,
}: {
   onHandler: any;
   name: string;
}) => {
   return (
      <div className="absolute right-0 top-0 flex items-center">
         <label className="mr-2 flex items-center whitespace-nowrap text-sm font-medium text-slate-500">
            Limitsiz
         </label>
         <Checkbox
            ripple={false}
            color="indigo"
            name={name}
            className="h-3.5 w-3.5 rounded hover:before:opacity-0"
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
      { key: "Temel", value: "BASIC" },
      { key: "Standart", value: "STANDARD" },
      { key: "Premium", value: "PREMIUM" },
   ];

   const themesList = [
      { key: "Yalnızca Temel", value: "Free" },
      { key: "Standart (Ücretsiz temalar dahil)", value: "Standard" },
      { key: "Premium (Tüm temalar dahil)", value: "Premium" },
   ];

   return (
      <>
         <Head title="Abonelik Planı Güncelle" />
         <PageHeader
            title="Abonelik Planı Güncelle"
            description="Mevcut abonelik planını düzenleyin."
         />

         <div className="card mx-auto w-full max-w-[1000px]">
            <div className="border-b border-slate-200 px-5 pb-4 pt-5 sm:px-6">
               <p className="text-lg font-semibold text-slate-900">
                  Plan Bilgileri
               </p>
            </div>
            <form onSubmit={submit} className="p-5 sm:p-6">
               <div className="mb-10 grid grid-cols-1 gap-6 md:grid-cols-2">
                  <div>
                     <InputDropdown
                        required
                        fullWidth
                        name="name"
                        label="Plan Adı"
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
                        label="Açıklama"
                        value={data.description}
                        error={errors.description}
                        placeholder="Kısa bir açıklama yazın"
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
                        label="Aylık Fiyat"
                        value={data.monthly_price as any}
                        error={errors.monthly_price}
                        placeholder="Aylık abonelik fiyatı"
                        onChange={onHandleChange}
                        required
                     />
                  </div>
                  <div>
                     <Input
                        fullWidth
                        type="number"
                        name="yearly_price"
                        label="Yıllık Fiyat"
                        value={data.yearly_price as any}
                        error={errors.yearly_price}
                        placeholder="Yıllık abonelik fiyatı"
                        onChange={onHandleChange}
                        required
                     />
                  </div>
                  <div>
                     <InputDropdown
                        required
                        fullWidth
                        name="currency"
                        label="Para Birimi"
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
                        label="Plan Durumu"
                        error={errors.status}
                        defaultValue={data.status}
                        onChange={(e: any) => setData("status", e.value)}
                        itemList={[
                           { key: "Aktif", value: "active" },
                           { key: "Pasif", value: "deactive" },
                        ]}
                     />
                  </div>
               </div>

               <p className="mb-4 text-lg font-semibold text-slate-900">
                  Plan Özellikleri
               </p>
               <div className="mb-10 grid grid-cols-1 gap-6 md:grid-cols-2">
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
                        label="Tema Erişimi"
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
                        label="Özel Tema Oluşturma"
                        itemList={[
                           { key: "Evet", value: 1 },
                           { key: "Hayır", value: 0 },
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
                        label="Destek (saat)"
                        itemList={[
                           { key: "24", value: 24 },
                           { key: "48", value: 48 },
                           { key: "72", value: 72 },
                        ]}
                     />
                  </div>
               </div>

               <button
                  type="submit"
                  className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
               >
                  Değişiklikleri Kaydet
               </button>
            </form>
         </div>
      </>
   );
};

Create.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Create;

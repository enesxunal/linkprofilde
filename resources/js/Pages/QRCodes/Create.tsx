import {
   useRef,
   useState,
   ReactNode,
   useEffect,
   FormEventHandler,
} from "react";
import Input from "@/Components/Input";
import { QRCode } from "react-qrcode-logo";
import { Head, useForm } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import TextArea from "@/Components/TextArea";
import QRCorner from "@/Components/QRCode/QRCorner";
import InputDropdown from "@/Components/InputDropdown";
import LogoUpload from "@/Components/QRCode/LogoUpload";
import ColorInput from "@/Components/QRCode/ColorInput";
import QRCodeDownloader from "@/Components/QRCode/QRCodeDownloader";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";
import QRcode from "@/Components/Icons/QRcode";
import { ProjectProps } from "@/types";

const Create = ({ projects }: { projects: ProjectProps[] }) => {
   const [state, setState] = useState<{ [key: string]: any }>({
      size: 300,
      quietZone: 20,
      value: "",
      ecLevel: "M",
      bgColor: "#ffffff",
      fgColor: "#000000",
      qrStyle: "squares",
      logoImage: "",
      logoWidth: 80,
      logoHeight: 80,
      logoOpacity: 1,
      enableCORS: "",
      logoPadding: 0,
      logoPaddingStyle: "square",
      removeQrCodeBehindLogo: false,
      eyeradius_0_outer_0: 0,
      eyeradius_0_outer_1: 0,
      eyeradius_0_outer_2: 0,
      eyeradius_0_outer_3: 0,
      eyeradius_0_inner_0: 0,
      eyeradius_0_inner_1: 0,
      eyeradius_0_inner_2: 0,
      eyeradius_0_inner_3: 0,
      eyeradius_1_outer_0: 0,
      eyeradius_1_outer_1: 0,
      eyeradius_1_outer_2: 0,
      eyeradius_1_outer_3: 0,
      eyeradius_1_inner_0: 0,
      eyeradius_1_inner_1: 0,
      eyeradius_1_inner_2: 0,
      eyeradius_1_inner_3: 0,
      eyeradius_2_outer_0: 0,
      eyeradius_2_outer_1: 0,
      eyeradius_2_outer_2: 0,
      eyeradius_2_outer_3: 0,
      eyeradius_2_inner_0: 0,
      eyeradius_2_inner_1: 0,
      eyeradius_2_inner_2: 0,
      eyeradius_2_inner_3: 0,
      eyecolor_0_outer: "#000000",
      eyecolor_0_inner: "#000000",
      eyecolor_1_outer: "#000000",
      eyecolor_1_inner: "#000000",
      eyecolor_2_outer: "#000000",
      eyecolor_2_inner: "#000000",
   });

   const handleChange = ({ target }: any) => {
      if (target.type === "checkbox") {
         setState((prevState) => ({
            ...prevState,
            [target.name]: target.checked,
         }));
      } else {
         setState((prevState) => ({
            ...prevState,
            [target.name]: target.value,
         }));
      }
   };

   const { data, setData, post, errors, clearErrors } = useForm({
      content: null,
      qr_code: null,
      qr_type: "project_qr",
      project_id: projects[0] ? projects[0].id : null,
      name: "",
   });

   const qrCodeRef: any = useRef(null);
   const getImageBlobData = () => {
      return qrCodeRef.current.canvas.current.toDataURL();
   };

   const submit: FormEventHandler = async (e) => {
      e.preventDefault();
      const qrCode = getImageBlobData();
      setData("qr_code", qrCode);
   };

   useEffect(() => {
      const { content, qr_code } = data;
      if (content && qr_code) {
         clearErrors();
         post("/qrcodes/save");
      }
   }, [data]);

   const project_list = projects.map((item) => {
      return { key: item.project_name, value: item.id };
   });

   const qrStyleOptions = [
      { key: "Kareler", value: "squares" },
      { key: "Noktalar", value: "dots" },
   ];

   const logoPaddingOptions = [
      { key: "Square", value: "square" },
      { key: "Circle", value: "circle" },
   ];

   return (
      <>
         <Head title="QR Kod Oluştur" />
         <PageHeader
            title="QR Kod Oluştur"
            description="Bağlantın için markana uygun bir QR kod tasarla."
         />

         <form onSubmit={submit}>
            <div className="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
               <div className="min-w-0 space-y-6">
                  <PanelCard
                     title="İçerik"
                     description="QR kodun okuttuğu değeri girin."
                  >
                     <TextArea
                        rows={3}
                        cols={10}
                        name="value"
                        label="QR İçeriği"
                        value={state.value}
                        onChange={(e) => {
                           handleChange(e);
                           setData("content", e.target.value);
                        }}
                        error={errors.content}
                        placeholder="qr kod değeri"
                        fullWidth
                        required
                     />
                  </PanelCard>

                  <PanelCard
                     title="Görünüm"
                     description="Boyut, dolgu ve renkleri ayarlayın."
                  >
                     <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <Input
                           min={100}
                           max={500}
                           name="size"
                           type="number"
                           label="QR Boyutu"
                           value={state.size}
                           onChange={handleChange}
                           fullWidth
                        />
                        <Input
                           min={0}
                           max={80}
                           type="number"
                           name="quietZone"
                           label="QR Dolgusu"
                           value={state.quietZone}
                           onChange={handleChange}
                           fullWidth
                        />
                        <InputDropdown
                           fullWidth
                           name="ecLevel"
                           label="Hata düzeltme seviyesi"
                           defaultValue={state.ecLevel}
                           itemList={[
                              { key: "L", value: "L" },
                              { key: "M", value: "M" },
                              { key: "Q", value: "Q" },
                              { key: "H", value: "H" },
                           ]}
                           onChange={(e: any) =>
                              setState((prev: any) => ({
                                 ...prev,
                                 ecLevel: e.value,
                              }))
                           }
                        />
                        <div className="min-w-0 sm:col-span-2">
                           <p className="mb-1.5 text-sm font-medium text-slate-700">
                              QR Tipi
                           </p>
                           <div className="grid grid-cols-2 gap-3">
                              {qrStyleOptions.map((option) => {
                                 const selected =
                                    state.qrStyle === option.value;
                                 return (
                                    <button
                                       key={option.value}
                                       type="button"
                                       onClick={() =>
                                          setState((prev: any) => ({
                                             ...prev,
                                             qrStyle: option.value,
                                          }))
                                       }
                                       className={`rounded-xl border px-3 py-3 text-sm font-medium transition ${
                                          selected
                                             ? "border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-500"
                                             : "border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50"
                                       }`}
                                    >
                                       {option.key}
                                    </button>
                                 );
                              })}
                           </div>
                        </div>
                        <ColorInput
                           name="bgColor"
                           label="Arka plan rengi"
                           value={state.bgColor}
                           onChange={handleChange}
                        />
                        <ColorInput
                           name="fgColor"
                           label="QR Rengi"
                           value={state.fgColor}
                           onChange={handleChange}
                        />
                     </div>
                  </PanelCard>

                  <PanelCard
                     title="Stil"
                     description="Köşe yuvarlaklığı ve köşe renkleri."
                  >
                     <div className="space-y-6">
                        <div>
                           <p className="mb-3 text-sm font-semibold text-slate-900">
                              Köşe yuvarlaklığı
                           </p>
                           <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                              <div className="min-w-0 space-y-3">
                                 <p className="text-sm font-medium text-slate-700">
                                    Sol üst
                                 </p>
                                 <QRCorner
                                    state={state}
                                    name="0_outer"
                                    title="Dıştan"
                                    onChange={handleChange}
                                 />
                                 <QRCorner
                                    state={state}
                                    name="0_inner"
                                    title="İçten"
                                    onChange={handleChange}
                                 />
                              </div>
                              <div className="min-w-0 space-y-3">
                                 <p className="text-sm font-medium text-slate-700">
                                    Sağ üst
                                 </p>
                                 <QRCorner
                                    state={state}
                                    name="1_outer"
                                    title="Dıştan"
                                    onChange={handleChange}
                                 />
                                 <QRCorner
                                    state={state}
                                    name="1_inner"
                                    title="İçten"
                                    onChange={handleChange}
                                 />
                              </div>
                              <div className="min-w-0 space-y-3 sm:col-span-2 lg:col-span-1">
                                 <p className="text-sm font-medium text-slate-700">
                                    Sol alt
                                 </p>
                                 <QRCorner
                                    state={state}
                                    name="2_outer"
                                    title="Dıştan"
                                    onChange={handleChange}
                                 />
                                 <QRCorner
                                    state={state}
                                    name="2_inner"
                                    title="İçten"
                                    onChange={handleChange}
                                 />
                              </div>
                           </div>
                        </div>

                        <div>
                           <p className="mb-3 text-sm font-semibold text-slate-900">
                              Köşe Rengi
                           </p>
                           <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                              <div className="min-w-0 space-y-3">
                                 <p className="text-sm font-medium text-slate-700">
                                    Sol üst
                                 </p>
                                 <ColorInput
                                    label="Dıştan"
                                    name="eyecolor_0_outer"
                                    value={state.eyecolor_0_outer}
                                    onChange={handleChange}
                                 />
                                 <ColorInput
                                    label="İçten"
                                    name="eyecolor_0_inner"
                                    value={state.eyecolor_0_inner}
                                    onChange={handleChange}
                                 />
                              </div>
                              <div className="min-w-0 space-y-3">
                                 <p className="text-sm font-medium text-slate-700">
                                    Sağ üst
                                 </p>
                                 <ColorInput
                                    label="Dıştan"
                                    name="eyecolor_1_outer"
                                    value={state.eyecolor_1_outer}
                                    onChange={handleChange}
                                 />
                                 <ColorInput
                                    label="İçten"
                                    name="eyecolor_1_inner"
                                    value={state.eyecolor_1_inner}
                                    onChange={handleChange}
                                 />
                              </div>
                              <div className="min-w-0 space-y-3 sm:col-span-2 lg:col-span-1">
                                 <p className="text-sm font-medium text-slate-700">
                                    Sol alt
                                 </p>
                                 <ColorInput
                                    label="Dıştan"
                                    name="eyecolor_2_outer"
                                    value={state.eyecolor_2_outer}
                                    onChange={handleChange}
                                 />
                                 <ColorInput
                                    label="İçten"
                                    name="eyecolor_2_inner"
                                    value={state.eyecolor_2_inner}
                                    onChange={handleChange}
                                 />
                              </div>
                           </div>
                        </div>
                     </div>
                  </PanelCard>

                  <PanelCard
                     title="Logo"
                     description="İsteğe bağlı logo ve logo ayarları."
                  >
                     <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <LogoUpload
                           name="logoImage"
                           handleChange={handleChange}
                           value={state.logoImage}
                           className="sm:col-span-2"
                        />
                        <div className="min-w-0 sm:col-span-2">
                           <p className="mb-1.5 text-sm font-medium text-slate-700">
                              Stil
                           </p>
                           <div className="grid grid-cols-2 gap-3">
                              {logoPaddingOptions.map((option) => {
                                 const selected =
                                    state.logoPaddingStyle === option.value;
                                 return (
                                    <button
                                       key={option.value}
                                       type="button"
                                       onClick={() =>
                                          setState((prev: any) => ({
                                             ...prev,
                                             logoPaddingStyle: option.value,
                                          }))
                                       }
                                       className={`rounded-xl border px-3 py-3 text-sm font-medium transition ${
                                          selected
                                             ? "border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-500"
                                             : "border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50"
                                       }`}
                                    >
                                       {option.key}
                                    </button>
                                 );
                              })}
                           </div>
                        </div>
                        <Input
                           min={0}
                           max={1}
                           type="number"
                           step="0.1"
                           name="logoOpacity"
                           label="Opaklık"
                           value={state.logoOpacity}
                           onChange={handleChange}
                           fullWidth
                        />
                        <Input
                           min={0}
                           max={20}
                           type="number"
                           name="logoPadding"
                           label="Logo dolgusu"
                           value={state.logoPadding}
                           onChange={handleChange}
                           fullWidth
                        />
                        <Input
                           min={40}
                           max={400}
                           type="number"
                           name="logoWidth"
                           label="Genişlik"
                           value={state.logoWidth}
                           onChange={handleChange}
                           fullWidth
                        />
                        <Input
                           min={40}
                           max={400}
                           type="number"
                           name="logoHeight"
                           label="Yükseklik"
                           value={state.logoHeight}
                           onChange={handleChange}
                           fullWidth
                        />
                        <div className="flex items-center sm:col-span-2">
                           <input
                              id="remember"
                              type="checkbox"
                              name="removeQrCodeBehindLogo"
                              checked={state.removeQrCodeBehindLogo}
                              className="mr-2 h-3.5 w-3.5 rounded border-slate-300 text-blue-600 focus:outline-0 focus:ring-blue-500"
                              onChange={handleChange}
                           />
                           <label
                              htmlFor="remember"
                              className="text-sm text-slate-700"
                           >
                              Logonun Arkasındaki QRCode'u Kaldır
                           </label>
                        </div>
                     </div>
                  </PanelCard>

                  <PanelCard
                     title="Kaydetme"
                     description="Proje seçimi ve kayıt bilgileri."
                  >
                     <div className="space-y-4">
                        <InputDropdown
                           required
                           fullWidth
                           name="project_id"
                           label="Proje Seçiniz"
                           error={errors.project_id}
                           defaultValue={data.project_id}
                           itemList={project_list}
                           onChange={(e: any) => setData("project_id", e.value)}
                        />
                        <Input
                           fullWidth
                           type="text"
                           name="name"
                           label="QR kod adı (isteğe bağlı)"
                           value={data.name ?? ""}
                           onChange={(e) => setData("name", e.target.value)}
                           error={errors.name}
                           placeholder="Örn: Menü, Instagram linki"
                        />
                        <div className="flex flex-wrap gap-3 pt-2">
                           <button
                              type="submit"
                              className="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 sm:w-auto"
                           >
                              QR Kod Oluştur
                           </button>
                           <div className="w-full min-w-0 sm:w-auto sm:min-w-[220px]">
                              <QRCodeDownloader
                                 buttonText="İndir"
                                 imageBlogData={getImageBlobData}
                              />
                           </div>
                        </div>
                     </div>
                  </PanelCard>
               </div>

               <div className="min-w-0 lg:sticky lg:top-6 lg:self-start">
                  <PanelCard title="Önizleme">
                     {!state.value ? (
                        <EmptyState
                           icon={<QRcode className="h-6 w-6" />}
                           title="Önizleme bekleniyor"
                           description="İçerik alanına bir değer yazdığınızda QR kod burada görünür."
                           className="py-8"
                        />
                     ) : null}
                     <div
                        className={`flex justify-center overflow-x-auto rounded-xl bg-white p-4 ${
                           !state.value ? "sr-only" : ""
                        }`}
                     >
                        <div className="mx-auto shrink-0">
                           <QRCode
                              ref={qrCodeRef}
                              {...{
                                 ...state,
                                 eyeRadius: [
                                    {
                                       outer: [
                                          state.eyeradius_0_outer_0,
                                          state.eyeradius_0_outer_1,
                                          state.eyeradius_0_outer_2,
                                          state.eyeradius_0_outer_3,
                                       ],
                                       inner: [
                                          state.eyeradius_0_inner_0,
                                          state.eyeradius_0_inner_1,
                                          state.eyeradius_0_inner_2,
                                          state.eyeradius_0_inner_3,
                                       ],
                                    },
                                    {
                                       outer: [
                                          state.eyeradius_1_outer_0,
                                          state.eyeradius_1_outer_1,
                                          state.eyeradius_1_outer_2,
                                          state.eyeradius_1_outer_3,
                                       ],
                                       inner: [
                                          state.eyeradius_1_inner_0,
                                          state.eyeradius_1_inner_1,
                                          state.eyeradius_1_inner_2,
                                          state.eyeradius_1_inner_3,
                                       ],
                                    },
                                    {
                                       outer: [
                                          state.eyeradius_2_outer_0,
                                          state.eyeradius_2_outer_1,
                                          state.eyeradius_2_outer_2,
                                          state.eyeradius_2_outer_3,
                                       ],
                                       inner: [
                                          state.eyeradius_2_inner_0,
                                          state.eyeradius_2_inner_1,
                                          state.eyeradius_2_inner_2,
                                          state.eyeradius_2_inner_3,
                                       ],
                                    },
                                 ],
                                 eyeColor: [
                                    {
                                       outer:
                                          state.eyecolor_0_outer ??
                                          state.fgColor,
                                       inner:
                                          state.eyecolor_0_inner ??
                                          state.fgColor,
                                    },
                                    {
                                       outer:
                                          state.eyecolor_1_outer ??
                                          state.fgColor,
                                       inner:
                                          state.eyecolor_1_inner ??
                                          state.fgColor,
                                    },
                                    {
                                       outer:
                                          state.eyecolor_2_outer ??
                                          state.fgColor,
                                       inner:
                                          state.eyecolor_2_inner ??
                                          state.fgColor,
                                    },
                                 ],
                              }}
                           />
                        </div>
                     </div>
                  </PanelCard>
               </div>
            </div>
         </form>
      </>
   );
};

Create.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Create;

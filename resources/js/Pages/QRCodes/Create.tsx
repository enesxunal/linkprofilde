import {
   useRef,
   useState,
   ReactNode,
   FormEventHandler,
} from "react";
import axios from "axios";
import Input from "@/Components/Input";
import { QRCode } from "react-qrcode-logo";
import { Head, router } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import QRCorner from "@/Components/QRCode/QRCorner";
import InputDropdown from "@/Components/InputDropdown";
import LogoUpload from "@/Components/QRCode/LogoUpload";
import ColorInput from "@/Components/QRCode/ColorInput";
import QRCodeDownloader from "@/Components/QRCode/QRCodeDownloader";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";
import AlertBanner from "@/Components/Panel/AlertBanner";
import QRcode from "@/Components/Icons/QRcode";
import { LinkProps, ProjectProps } from "@/types";

type DestinationType = "external" | "biolink" | "shortlink";

interface Props {
   projects: ProjectProps[];
   biolinks: Pick<LinkProps, "id" | "link_name" | "url_name" | "link_type">[];
   shortlinks: Pick<LinkProps, "id" | "link_name" | "url_name" | "link_type">[];
}

const Create = ({ projects, biolinks = [], shortlinks = [] }: Props) => {
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

   const [projectId, setProjectId] = useState<number | null>(
      projects[0] ? projects[0].id : null
   );
   const [name, setName] = useState("");
   const [destinationType, setDestinationType] =
      useState<DestinationType>("external");
   const [destinationUrl, setDestinationUrl] = useState("");
   const [destinationLinkId, setDestinationLinkId] = useState<number | null>(
      null
   );
   const [errors, setErrors] = useState<Record<string, string>>({});
   const [saving, setSaving] = useState(false);
   const [lockedPublicUrl, setLockedPublicUrl] = useState<string | null>(null);
   const preparedRef = useRef<{ id: number; publicUrl: string } | null>(null);

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

   const qrCodeRef: any = useRef(null);
   const getImageBlobData = () => {
      return qrCodeRef.current.canvas.current.toDataURL();
   };

   const waitForPaint = () =>
      new Promise<void>((resolve) => {
         requestAnimationFrame(() => {
            requestAnimationFrame(() => {
               setTimeout(() => resolve(), 50);
            });
         });
      });

   const destinationTypeOptions = [
      { key: "Harici URL", value: "external" },
      { key: "Bio Link", value: "biolink" },
      { key: "Kısa Link", value: "shortlink" },
   ];

   const biolinkOptions = biolinks.map((item) => ({
      key: item.link_name || item.url_name,
      value: item.id,
   }));

   const shortlinkOptions = shortlinks.map((item) => ({
      key: item.link_name || item.url_name,
      value: item.id,
   }));

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

   const previewValue = lockedPublicUrl || "";

   const submit: FormEventHandler = async (e) => {
      e.preventDefault();
      if (saving) return;

      setErrors({});
      setSaving(true);

      try {
         let qrId: number;
         let publicUrl: string;
         const existing = preparedRef.current;

         if (existing) {
            qrId = existing.id;
            publicUrl = existing.publicUrl;
         } else {
            const payload: Record<string, unknown> = {
               project_id: projectId,
               name: name || null,
               qr_type: "project_qr",
               destination_type: destinationType,
               destination_url:
                  destinationType === "external" ? destinationUrl : null,
               destination_link_id:
                  destinationType === "external" ? null : destinationLinkId,
            };

            const prep = await axios.post("/qrcodes/prepare", payload);
            publicUrl = prep.data.public_url;
            qrId = prep.data.id;

            if (!publicUrl || typeof publicUrl !== "string" || !qrId) {
               throw new Error("Sunucu QR adresi döndürmedi.");
            }

            preparedRef.current = { id: qrId, publicUrl };
         }

         setLockedPublicUrl(publicUrl);
         setState((prev) => ({ ...prev, value: publicUrl }));
         await waitForPaint();

         const qrCode = getImageBlobData();
         await axios.post(`/qrcodes/${qrId}/finalize`, {
            qr_code: qrCode,
         });
         preparedRef.current = null;
         setLockedPublicUrl(null);
         router.visit("/qrcodes");
      } catch (err: any) {
         const bag = err?.response?.data?.errors;
         if (bag && typeof bag === "object") {
            const next: Record<string, string> = {};
            Object.keys(bag).forEach((key) => {
               const val = bag[key];
               next[key] = Array.isArray(val) ? val[0] : String(val);
            });
            setErrors(next);
         } else {
            setErrors({
               destination_type:
                  err?.response?.data?.message ||
                  "QR kod oluşturulamadı. Lütfen tekrar deneyin.",
            });
         }
         setSaving(false);
      }
   };

   return (
      <>
         <Head title="QR Kod Oluştur" />
         <PageHeader
            title="QR Kod Oluştur"
            description="Dinamik QR kod oluşturun. Hedefi sonradan değiştirebilirsiniz."
         />

         <form onSubmit={submit}>
            <div className="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
               <div className="min-w-0 space-y-6">
                  <PanelCard
                     title="Hedef"
                     description="QR okutulunca açılacak adresi seçin."
                  >
                     <div className="space-y-4">
                        <AlertBanner variant="info">
                           QR görseli sabit bir yönlendirme adresi taşır. Hedefi
                           sonra değiştirseniz basılı QR’lar yeni adrese gider.
                        </AlertBanner>

                        <InputDropdown
                           required
                           fullWidth
                           name="destination_type"
                           label="Hedef Türü"
                           error={errors.destination_type}
                           defaultValue={destinationType}
                           itemList={destinationTypeOptions}
                           onChange={(e: any) => {
                              const next = e.value as DestinationType;
                              setDestinationType(next);
                              setDestinationLinkId(null);
                              setDestinationUrl("");
                           }}
                        />

                        {destinationType === "external" ? (
                           <Input
                              fullWidth
                              required
                              type="url"
                              name="destination_url"
                              label="Hedef URL"
                              value={destinationUrl}
                              onChange={(e) =>
                                 setDestinationUrl(e.target.value)
                              }
                              error={errors.destination_url}
                              placeholder="https://ornek.com/sayfa"
                           />
                        ) : null}

                        {destinationType === "biolink" ? (
                           <InputDropdown
                              required
                              fullWidth
                              name="destination_link_id"
                              label="Bio Link"
                              error={errors.destination_link_id}
                              defaultValue={destinationLinkId}
                              itemList={biolinkOptions}
                              onChange={(e: any) =>
                                 setDestinationLinkId(e.value)
                              }
                           />
                        ) : null}

                        {destinationType === "shortlink" ? (
                           <InputDropdown
                              required
                              fullWidth
                              name="destination_link_id"
                              label="Kısa Link"
                              error={errors.destination_link_id}
                              defaultValue={destinationLinkId}
                              itemList={shortlinkOptions}
                              onChange={(e: any) =>
                                 setDestinationLinkId(e.value)
                              }
                           />
                        ) : null}
                     </div>
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
                           defaultValue={projectId}
                           itemList={project_list}
                           onChange={(e: any) => setProjectId(e.value)}
                        />
                        <Input
                           fullWidth
                           type="text"
                           name="name"
                           label="QR kod adı (isteğe bağlı)"
                           value={name}
                           onChange={(e) => setName(e.target.value)}
                           error={errors.name}
                           placeholder="Örn: Menü, Instagram linki"
                        />
                        <div className="flex flex-wrap gap-3 pt-2">
                           <button
                              type="submit"
                              disabled={saving}
                              className="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60 sm:w-auto"
                           >
                              {saving ? "Oluşturuluyor..." : "QR Kod Oluştur"}
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
                     {!previewValue ? (
                        <EmptyState
                           icon={<QRcode className="h-6 w-6" />}
                           title="Önizleme kayıttan sonra"
                           description="Stil ayarlarını yapın. Kaydettiğinizde QR, sabit yönlendirme adresini encode eder."
                           className="py-8"
                        />
                     ) : null}
                     <div
                        className={`flex justify-center overflow-x-auto rounded-xl bg-white p-4 ${
                           !previewValue ? "sr-only" : ""
                        }`}
                     >
                        <div className="mx-auto shrink-0">
                           <QRCode
                              ref={qrCodeRef}
                              {...{
                                 ...state,
                                 value: previewValue,
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

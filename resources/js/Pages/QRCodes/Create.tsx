import {
   useRef,
   useState,
   ReactNode,
   useEffect,
   FormEventHandler,
} from "react";
import Input from "@/Components/Input";
import { QRCode } from "react-qrcode-logo";
import { useForm } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import TextArea from "@/Components/TextArea";
import { Button } from "@material-tailwind/react";
import QRCorner from "@/Components/QRCode/QRCorner";
import InputDropdown from "@/Components/InputDropdown";
import LogoUpload from "@/Components/QRCode/LogoUpload";
import ColorInput from "@/Components/QRCode/ColorInput";
import QRCodeDownloader from "@/Components/QRCode/QRCodeDownloader";
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

   return (
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-7">
         <form onSubmit={submit} className="lg:col-span-5 card p-4">
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
            <div className="py-2">
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
            </div>
            <div className="py-4">
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
            </div>
            <div className="grid grid-cols-3 gap-4">
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
               <InputDropdown
                  fullWidth
                  name="qrStyle"
                  label="QR Tipi"
                  defaultValue={state.qrStyle}
                  itemList={[
                     { key: "Kareler", value: "squares" },
                     { key: "Noktalar", value: "dots" },
                  ]}
                  onChange={(e: any) =>
                     setState((prev: any) => ({
                        ...prev,
                        qrStyle: e.value,
                     }))
                  }
               />

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

            <div className="my-7">
               <p className="text-gray-900 font-medium mb-2">Köşe yuvarlaklığı</p>
               <div className="grid grid-cols-3 gap-4">
                  <div>
                     <p className="text-sm text-gray-700 font-medium my-2">
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
                  <div>
                     <p className="text-sm text-gray-700 font-medium my-2">
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
                  <div>
                     <p className="text-sm text-gray-700 font-medium my-2">
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
               <p className="text-gray-900 font-medium mb-2">Köşe Rengi</p>
               <div className="grid grid-cols-3 gap-4">
                  <div>
                     <p className="text-sm text-gray-700 font-medium my-2">
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
                  <div>
                     <p className="text-sm text-gray-700 font-medium my-2">
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
                  <div>
                     <p className="text-sm text-gray-700 font-medium my-2">
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

            <div className="mt-7">
               <p className="text-gray-900 font-medium mb-2">QR logo</p>
               <div className="grid grid-cols-3 gap-4 mt-4">
                  <LogoUpload
                     name="logoImage"
                     handleChange={handleChange}
                     className="col-span-3"
                  />
                  <InputDropdown
                     fullWidth
                     label="Stil"
                     name="logoPaddingStyle"
                     defaultValue={state.logoPaddingStyle}
                     itemList={[
                        { key: "Square", value: "square" },
                        { key: "Circle", value: "circle" },
                     ]}
                     onChange={(e: any) =>
                        setState((prev: any) => ({
                           ...prev,
                           logoPaddingStyle: e.value,
                        }))
                     }
                  />
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
                  <div className="col-span-3 flex items-center">
                     <input
                        id="remember"
                        type="checkbox"
                        name="removeQrCodeBehindLogo"
                        checked={state.removeQrCodeBehindLogo}
                        className="rounded focus:outline-0 focus:ring-white w-3.5 h-3.5 mr-2"
                        onChange={handleChange}
                     />
                     <label htmlFor="remember" className="whitespace-nowrap">
                     Logonun Arkasındaki QRCode'u Kaldır
                     </label>
                  </div>
               </div>
            </div>

            <div className="grid sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-4 mt-8">
               <Button
                  type="submit"
                  variant="gradient"
                  color="blue"
                  className="w-full  py-2.5 px-1 rounded-md font-medium capitalize text-sm hover:shadow-md"
               >
                  QR Kodunu Kaydet
               </Button>
               <QRCodeDownloader
                  buttonText="İndir"
                  imageBlogData={getImageBlobData}
               />
            </div>
         </form>

         <div className="lg:col-span-7 overflow-hidden">
            <div className="lg:fixed">
               <QRCode
                  ref={qrCodeRef}
                  logoOnLoad={() => console.log("logo loaded")}
                  {...{
                     ...state,
                     eyeRadius: [
                        // build eyeRadius manually
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
                        // build eyeColor manually
                        {
                           outer: state.eyecolor_0_outer ?? state.fgColor,
                           inner: state.eyecolor_0_inner ?? state.fgColor,
                        },
                        {
                           outer: state.eyecolor_1_outer ?? state.fgColor,
                           inner: state.eyecolor_1_inner ?? state.fgColor,
                        },
                        {
                           outer: state.eyecolor_2_outer ?? state.fgColor,
                           inner: state.eyecolor_2_inner ?? state.fgColor,
                        },
                     ],
                  }}
               />
            </div>
         </div>
      </div>
   );
};

Create.layout = (page: ReactNode) => <Dashboard children={page} />;

export default Create;

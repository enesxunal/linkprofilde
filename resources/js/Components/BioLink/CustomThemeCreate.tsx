import axios from "axios";
import Camera from "../Icons/Camera";
import { error } from "@/utils/toast";
import { useForm } from "@inertiajs/react";
import ColorFillDrip from "../Icons/ColorFillDrip";
import { ChangeEvent, Fragment } from "react";
import { LinkProps } from "@/types";

interface Props {
   link: LinkProps;
   setLink: (state: any) => void;
}

const CustomThemeCreate = ({ link, setLink }: Props) => {
   // link branding handler
   const theme = "/bio-links/customize/custom-theme/update";
   const { data, setData } = useForm({
      bg_color: link.custom_theme ? link.custom_theme.bg_color : "",
      text_color: link.custom_theme ? link.custom_theme.text_color : "",
      btn_bg_color: link.custom_theme ? link.custom_theme.btn_bg_color : "",
      btn_text_color: link.custom_theme ? link.custom_theme.btn_text_color : "",
   });

   const customThemeBgImage = async (
      e: ChangeEvent<HTMLInputElement>
   ): Promise<void> => {
      const files = e.target.files;
      if (files && files[0]) {
         const formData: any = new FormData();
         formData.append("type", "bg_image");
         formData.append("bg_image", files[0]);

         const res = await axios.post(
            `${theme}/${link.custom_theme?.id}/${link.id}`,
            formData
         );
         if (res.data.error) {
            error(res.data.error);
         } else if (res.data.result) {
            setLink(res.data.result);
         }
      }
   };

   const customThemeItem = async (elements: any): Promise<void> => {
      const res = await axios.post(
         `${theme}/${link.custom_theme?.id}/${link.id}`,
         elements
      );
      if (res.data.error) {
         error(res.data.error);
      } else if (res.data.result) {
         setLink(res.data.result);
      }
   };

   return (
      <Fragment>
         {link.custom_theme_active && link.custom_theme ? (
            <div className="card p-6 mt-7">
               <div className="grid grid-cols-2 md:grid-cols-3 gap-6 mb-7">
                  <div className="col-span-2 md:col-span-3">
                     <h6 className="text-xl">Custom Theme</h6>
                  </div>
                  <div>
                     <div className="relative">
                        <label
                           htmlFor="bgColor"
                           className="absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2 cursor-pointer"
                        >
                           <ColorFillDrip className="w-10 h-10 text-blue-500" />
                        </label>
                        <input
                           id="bgColor"
                           type="color"
                           value={data.bg_color}
                           onChange={(e) => setData("bg_color", e.target.value)}
                           className={`h-[220px] 2xl:h-[260px] w-full p-0 border border-gray-300 hover:border-blue-500 cursor-pointer ring-0 outline-0 rounded-lg ${
                              link.custom_theme.background_type === "color" &&
                              "outline outline-1 outline-blue-500 !border-blue-500"
                           }`}
                           onBlur={(e) =>
                              customThemeItem({
                                 type: "bg_color",
                                 bg_color: e.target.value,
                              })
                           }
                        />
                     </div>
                     <p className="font-medium text-center mt-1 mb-2">
                        Background Color
                     </p>
                  </div>

                  <div>
                     <div
                        className={`h-[220px] 2xl:h-[260px] p-4 py-8 2xl:py-12 rounded-lg flex items-center justify-center border border-gray-300 hover:border-blue-500 bg-cover bg-center object-contain ${
                           link.custom_theme.background_type === "image" &&
                           "outline outline-1 outline-blue-500 !border-blue-500"
                        }`}
                        style={{
                           backgroundImage: link.custom_theme.bg_image
                              ? `url('/${link.custom_theme.bg_image}')`
                              : "",
                        }}
                     >
                        <label
                           htmlFor="customThemeBg"
                           className="cursor-pointer"
                        >
                           <Camera className="text-blue-500 w-7 h-7" />
                        </label>
                        <input
                           hidden
                           type="file"
                           onChange={customThemeBgImage}
                           id="customThemeBg"
                        ></input>
                     </div>
                     <p className="font-medium text-center mt-1 mb-2">
                        Background Image
                     </p>
                  </div>

                  <div className="hidden md:block"></div>
                  <div>
                     <div className="relative">
                        <label
                           htmlFor="textColor"
                           className="absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2"
                        >
                           <ColorFillDrip className="w-6 h-6 text-blue-500" />
                        </label>
                        <input
                           type="color"
                           id="textColor"
                           value={data.text_color}
                           onChange={(e) =>
                              setData("text_color", e.target.value)
                           }
                           className="h-[48px] w-full p-0"
                           onBlur={(e) =>
                              customThemeItem({
                                 type: "text_color",
                                 text_color: e.target.value,
                              })
                           }
                        />
                     </div>
                     <p className="font-medium text-center mt-1 mb-2">
                        Theme Text Color
                     </p>
                  </div>
               </div>
               <div className="grid grid-cols-2 md:grid-cols-3 gap-6 mb-7">
                  <div className="col-span-2 md:col-span-3">
                     <h6 className="text-xl">Button Type</h6>
                  </div>
                  {buttonTypes.map((button, ind) => {
                     return (
                        <button
                           key={ind}
                           className={`h-10 border border-gray-500 ${
                              link.custom_theme?.btn_type === button.btn_type &&
                              " outline outline-2 outline-blue-500"
                           }`}
                           style={{
                              backgroundColor: button.btn_color,
                              borderRadius: button.btn_radius,
                           }}
                           onClick={() =>
                              customThemeItem({
                                 type: "button",
                                 ...button,
                              })
                           }
                        ></button>
                     );
                  })}
                  <div>
                     <div className="relative">
                        <label
                           htmlFor="buttonBgColor"
                           className="absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2"
                        >
                           <ColorFillDrip className="w-6 h-6 text-blue-500" />
                        </label>
                        <input
                           type="color"
                           id="buttonBgColor"
                           value={data.btn_bg_color}
                           className="h-[48px] w-full p-0"
                           onChange={(e) =>
                              setData("btn_bg_color", e.target.value)
                           }
                           onBlur={(e) =>
                              customThemeItem({
                                 type: "btn_bg_color",
                                 btn_bg_color: e.target.value,
                              })
                           }
                        />
                     </div>
                     <p className="font-medium text-center mt-1 mb-2">
                        Button Background
                     </p>
                  </div>
                  <div>
                     <div className="relative">
                        <label
                           htmlFor="buttonTextColor"
                           className="absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2"
                        >
                           <ColorFillDrip className="w-6 h-6 text-blue-500" />
                        </label>
                        <input
                           type="color"
                           id="buttonTextColor"
                           value={data.btn_text_color}
                           className="h-[48px] w-full p-0"
                           onChange={(e) =>
                              setData("btn_text_color", e.target.value)
                           }
                           onBlur={(e) =>
                              customThemeItem({
                                 type: "btn_text_color",
                                 btn_text_color: e.target.value,
                              })
                           }
                        />
                     </div>
                     <p className="font-medium text-center mt-1 mb-2">
                        Button Text
                     </p>
                  </div>
               </div>
               <div className="grid grid-cols-2 md:grid-cols-3 gap-6">
                  <div className="col-span-2 md:col-span-3">
                     <h6 className="text-xl">Font Family</h6>
                  </div>
                  {fontFamily.map((font, ind) => {
                     return (
                        <button
                           key={ind}
                           onClick={() =>
                              customThemeItem({
                                 type: "font_family",
                                 font_family: font,
                              })
                           }
                           className={`h-10 border border-gray-500 rounded-lg whitespace-nowrap overflow-x-auto ${
                              link.custom_theme?.font_family === font &&
                              "outline outline-2 outline-blue-500"
                           }`}
                           style={{ fontFamily: font }}
                        >
                           {font}
                        </button>
                     );
                  })}
               </div>
            </div>
         ) : null}
      </Fragment>
   );
};

const buttonTypes = [
   {
      btn_type: "rounded",
      btn_color: "#000",
      btn_radius: "30px",
      btn_transparent: false,
   },
   {
      btn_type: "radius",
      btn_color: "#000",
      btn_radius: "12px",
      btn_transparent: false,
   },
   {
      btn_type: "rectangle",
      btn_color: "#000",
      btn_radius: "8px",
      btn_transparent: false,
   },
   {
      btn_type: "rounded-trans",
      btn_color: "#fff",
      btn_radius: "30px",
      btn_transparent: true,
   },
   {
      btn_type: "radius-trans",
      btn_color: "#fff",
      btn_radius: "12px",
      btn_transparent: true,
   },
   {
      btn_type: "rectangle-trans",
      btn_color: "#fff",
      btn_radius: "8px",
      btn_transparent: true,
   },
];

const fontFamily = [
   "Inter, sans-serif",
   "MintGrotesk, sans-serif",
   "DM Sans, sans-serif",
   "Bebas Neue, cursive",
   "Poppins, sans-serif",
   "Quicksand, sans-serif",
];

export default CustomThemeCreate;

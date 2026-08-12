import axios from "axios";
import Camera from "../Icons/Camera";
import { error } from "@/utils/toast";
import { usePage } from "@inertiajs/react";
import { jsxStyle, stringToCss } from "@/utils/utils";
import { LinkProps, PageProps, ThemeProps } from "@/types";
import { ChangeEvent, useState, useEffect } from "react";
import CustomThemeCreate from "./CustomThemeCreate";
import ThemeBadge from "./ThemeBadge";

interface Props {
   link: LinkProps;
   themes: ThemeProps[];
   setLink: (state: any) => void;
}

const LinkThemes = ({ link, themes, setLink }: Props) => {
   const { props } = usePage();
   const { app, auth } = props as PageProps;
   const [imageUrl, setImageUrl] = useState("");

   const activeTheme = (theme: ThemeProps | null) => {
      if (!link.custom_theme_active && theme && theme.id === link.theme.id) {
         return "ring-2 ring-blue-500 border-blue-500";
      }
   };

   useEffect(() => {
      if (auth.user.roles[0].name === "BASIC") {
         setImageUrl(`/${app.logo}`);
      } else {
         if (link.branding) {
            setImageUrl(`/${link.branding}`);
         } else {
            setImageUrl(`/${app.logo}`);
         }
      }
   }, []);

   const updateTheme = async (
      theme: ThemeProps,
      linkId: number
   ): Promise<void> => {
      if (auth.user.roles[0].name === "BASIC") {
         if (theme.type !== "Free") {
            return;
         }
      }
      if (auth.user.roles[0].name === "STANDARD") {
         if (theme.type === "Premium") {
            return;
         }
      }

      const res = await axios.put(
         `/bio-links/customize/update-theme/${theme.id}/${linkId}`
      );

      if (res.data.error) {
         error(res.data.error);
      } else if (res.data.success) {
         setLink(res.data.link);
      }
   };

   // Custom theme handler
   const customThemeHandler = async (link: LinkProps): Promise<void> => {
      if (auth.user.roles[0].name === "BASIC") {
         return;
      }

      if (link.custom_theme_id) {
         const res = await axios.put(
            `/bio-links/customize/custom-theme/active/${link.id}`
         );
         if (res.data.error) {
            error(res.data.error);
         } else if (res.data.success) {
            setLink(res.data.link);
         }
      } else {
         const theme = {
            background: "background: #30425A;",
            background_type: "color",
            bg_color: "#30425A",
            text_color: "#ffffff",
            btn_type: "rounded",
            btn_transparent: false,
            btn_radius: "30px",
            btn_bg_color: "#ffffff",
            btn_text_color: "#1d2939",
            font_family: "Inter, sans-serif",
         };

         const res = await axios.post(
            `/bio-links/customize/custom-theme/create/${link.id}`,
            theme
         );
         if (res.data.error) {
            error(res.data.error);
         } else if (res.data.success) {
            setLink(res.data.link);
         }
      }
   };

   // link branding handler
   const brandingHandle = async (
      e: ChangeEvent<HTMLInputElement>
   ): Promise<void> => {
      if (auth.user.roles[0].name === "BASIC") {
         return;
      }

      const files = e.target.files;
      if (files && files[0]) {
         setImageUrl(URL.createObjectURL(files[0]));
         const formData: any = new FormData();
         formData.append("branding", files[0]);

         const res = await axios.post(
            `/bio-links/customize/update-logo/${link.id}`,
            formData
         );
         if (res.data.error) {
            error(res.data.error);
         } else if (res.data.success) {
            setLink(res.data.link);
         }
      }
   };

   return (
      <div>
         <div className="grid grid-cols-2 gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:gap-6 sm:p-6 md:grid-cols-3">
            <div className="col-span-2 md:col-span-3">
               <h2 className="text-base font-semibold text-slate-900">
                  Mevcut Temalar
               </h2>
               <p className="mt-0.5 text-sm text-slate-600">
                  Temayı seçin veya özel tema oluşturun.
               </p>
            </div>
            {themes.map((theme, ind) => {
               let bgStyle = jsxStyle(stringToCss(theme.background));
               if (theme.bg_image) {
                  bgStyle.backgroundImage = `url(/${theme.bg_image})`;
               }
               let btnStyle = jsxStyle(stringToCss(theme.button_style));

               return (
                  <div key={ind}>
                     <div className="relative">
                        <div
                           onClick={() => updateTheme(theme, link.id)}
                           className={`flex h-[220px] cursor-pointer flex-col justify-between rounded-xl border border-slate-200 p-4 py-8 transition hover:border-blue-300 2xl:h-[260px] 2xl:py-12 ${activeTheme(
                              theme
                           )}`}
                           style={bgStyle}
                        >
                           {[1, 2, 3, 4].map((item) => (
                              <button
                                 key={item}
                                 className="h-[30px] w-full"
                                 style={btnStyle}
                              ></button>
                           ))}
                        </div>
                        <ThemeBadge title={theme.type} theme={theme} />
                     </div>
                     <p className="mb-2 mt-1 text-center text-sm font-medium text-slate-800">
                        {({
                           Basic: "Temel",
                           "Dark Carbon": "Koyu Karbon",
                           Glitch: "Glitch",
                        } as Record<string, string>)[theme.name] || theme.name}
                     </p>
                  </div>
               );
            })}

            <div>
               <div className="relative">
                  <div
                     onClick={() => customThemeHandler(link)}
                     className={`flex h-[220px] cursor-pointer items-center rounded-xl border border-slate-200 p-4 py-8 transition hover:border-blue-300 2xl:h-[260px] 2xl:py-12 ${
                        link.custom_theme_active &&
                        "ring-2 ring-blue-500 border-blue-500"
                     }`}
                  >
                     <p className="text-center text-sm font-medium text-slate-800">
                        Özel Tema Oluştur
                     </p>
                  </div>
                  <ThemeBadge title="Pro" />
               </div>
               <p className="mb-2 mt-1 text-center text-sm font-medium text-slate-800">
                  Özel Tema
               </p>
            </div>

            <div>
               <div className="relative">
                  <div
                     className={`flex h-[220px] flex-col items-center justify-center rounded-xl border border-slate-200 p-4 py-8 transition hover:border-blue-300 2xl:h-[260px] 2xl:py-12`}
                  >
                     <img src={imageUrl} className="h-20 w-20 rounded" alt="" />
                     <label
                        htmlFor="linkBranding"
                        className="mt-4 cursor-pointer"
                     >
                        <Camera className="h-7 w-7 text-blue-500" />
                     </label>
                     <input
                        hidden
                        type="file"
                        onChange={brandingHandle}
                        id="linkBranding"
                     ></input>
                  </div>
                  <ThemeBadge title="Pro" />
               </div>

               <p className="mb-2 mt-1 text-center text-sm font-medium text-slate-800">
                  Logo Değiştir
               </p>
            </div>
         </div>

         <CustomThemeCreate link={link} setLink={setLink} />
      </div>
   );
};

export default LinkThemes;

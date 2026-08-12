import {
   Tab,
   Tabs,
   TabsBody,
   TabPanel,
   TabsHeader,
} from "@material-tailwind/react";
import { Head } from "@inertiajs/react";
import {
   customThemeButtonStyle,
   customThemePageStyle,
   jsxStyle,
   stringToCss,
} from "@/utils/utils";
import Dashboard from "@/Layouts/Dashboard";
import { ReactNode, useEffect, useRef, useState } from "react";
import { LinkProps, PageProps, SocialLinkProps, ThemeProps } from "@/types";
import AddSocialLinks from "@/Components/BioLink/AddSocialLinks";
import LinkProfile from "@/Components/BioLink/LinkProfile";
import LinkThemes from "@/Components/BioLink/LinkThemes";
import AddBlocks from "@/Components/BioLink/AddBlocks";
import LinkBlocks from "@/Components/BioLink/LinkBlocks";
import LinkPreview from "@/Components/BioLink/LinkPreview";
import PageHeader from "@/Components/Panel/PageHeader";

interface Props extends PageProps {
   link: LinkProps;
   themes: ThemeProps[];
   itemLastPosition: number;
   socialLinks: SocialLinkProps[];
}

const AddItem = (props: Props) => {
   const { themes, socialLinks, itemLastPosition } = props;
   const [link, setLink] = useState<LinkProps>(props.link);

   const blockRaf = useRef<any>();
   const settingRaf = useRef<any>();

   const refHandler = (type: string) => {
      if (blockRaf.current && settingRaf.current) {
         if (type === "block") {
            blockRaf.current.classList.add("active");
         } else {
            blockRaf.current.classList.remove("active");
         }
         if (type === "setting") {
            settingRaf.current.classList.add("active");
         } else {
            settingRaf.current.classList.remove("active");
         }
      }
   };

   let parsedStyle: any;
   let buttonStyle: any = new Object();

   if (link.custom_theme && link.custom_theme_active) {
      const theme = link.custom_theme;
      parsedStyle = customThemePageStyle(theme);
      buttonStyle = customThemeButtonStyle(theme);
   } else {
      const { background, text_color, font_family, bg_image, button_style } =
         link.theme;
      parsedStyle = jsxStyle(stringToCss(background));
      parsedStyle.color = text_color;
      parsedStyle.fontFamily = font_family;
      if (bg_image) {
         parsedStyle.backgroundImage = `url(/${bg_image})`;
      }
      buttonStyle = jsxStyle(stringToCss(button_style));
   }

   useEffect(() => {
      if (link.custom_theme && link.custom_theme_active) {
         parsedStyle = customThemePageStyle(link.custom_theme);
         buttonStyle = customThemeButtonStyle(link.custom_theme);
      } else {
         const { background, text_color, font_family, bg_image, button_style } =
            link.theme;
         parsedStyle = jsxStyle(stringToCss(background));
         parsedStyle.color = text_color;
         parsedStyle.fontFamily = font_family;
         if (bg_image) {
            parsedStyle.backgroundImage = `url(/${bg_image})`;
         }
         buttonStyle = jsxStyle(stringToCss(button_style));
      }
   }, [link]);

   return (
      <>
         <Head title="Bio Link'i Düzenle" />
         <PageHeader
            title="Bio Link'i Düzenle"
            description="Profil, bloklar ve temayı düzenleyin; sağda canlı önizlemeyi görün."
            actions={
               <a
                  href={`/${link.url_name}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
               >
                  Görüntüle
               </a>
            }
         />

         <div className="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div className="min-w-0">
               <Tabs value="settings">
                  <div className="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                     <div className="max-w-full overflow-x-auto">
                        <TabsHeader className="tabs-header min-w-max bg-transparent p-0">
                           <Tab
                              ref={settingRaf}
                              value="settings"
                              onClick={() => refHandler("setting")}
                              className="active px-3 py-[7px] md:px-4"
                           >
                              Ayarlar
                           </Tab>
                           <Tab
                              ref={blockRaf}
                              value="blocks"
                              onClick={() => refHandler("block")}
                              className="px-3 py-[7px] md:px-4"
                           >
                              Bloklar
                           </Tab>
                        </TabsHeader>
                     </div>
                     <div className="flex flex-wrap items-center gap-2">
                        <AddBlocks
                           link={link}
                           setLink={setLink}
                           itemPosition={itemLastPosition}
                        />
                     </div>
                  </div>

                  <TabsBody>
                     <TabPanel value="settings" className="p-0">
                        <div className="space-y-6">
                           <LinkProfile link={link} setLink={setLink} />
                           <AddSocialLinks link={link} setLink={setLink} />
                           <LinkThemes
                              link={link}
                              themes={themes}
                              setLink={setLink}
                           />
                        </div>
                     </TabPanel>
                     <TabPanel value="blocks" className="p-0">
                        <LinkBlocks link={link} setLink={setLink} />
                     </TabPanel>
                  </TabsBody>
               </Tabs>
            </div>

            <div className="min-w-0">
               <div className="lg:sticky lg:top-24">
                  <p className="mb-3 text-sm font-medium text-slate-600">
                     Canlı önizleme
                  </p>
                  <div
                     style={parsedStyle}
                     className="mx-auto h-[min(720px,calc(100vh-10rem))] w-full max-w-[360px] overflow-y-auto rounded-[2rem] border-[8px] border-slate-800 bg-cover bg-center object-contain shadow-sm"
                  >
                     <LinkPreview link={link} buttonStyle={buttonStyle} />
                  </div>
               </div>
            </div>
         </div>
      </>
   );
};

AddItem.layout = (page: ReactNode) => <Dashboard children={page} />;

export default AddItem;

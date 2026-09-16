import {
   Tab,
   Tabs,
   TabsBody,
   TabPanel,
   TabsHeader,
} from "@/Components/MaterialLite";
import { Head } from "@inertiajs/react";
import {
   customThemeButtonStyle,
   customThemePageStyle,
   jsxStyle,
   stringToCss,
} from "@/utils/utils";
import Dashboard from "@/Layouts/Dashboard";
import { ReactNode, useMemo, useState } from "react";
import { LinkProps, PageProps, SocialLinkProps, ThemeProps } from "@/types";
import AddSocialLinks from "@/Components/BioLink/AddSocialLinks";
import LinkProfile from "@/Components/BioLink/LinkProfile";
import LinkThemes from "@/Components/BioLink/LinkThemes";
import AddBlocks from "@/Components/BioLink/AddBlocks";
import LinkBlocks from "@/Components/BioLink/LinkBlocks";
import LinkPreview from "@/Components/BioLink/LinkPreview";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";

interface Props extends PageProps {
   link: LinkProps;
   themes: ThemeProps[];
   itemLastPosition: number;
   socialLinks: SocialLinkProps[];
}

const AddItem = (props: Props) => {
   const { themes, socialLinks, itemLastPosition } = props;
   const [link, setLink] = useState<LinkProps>(props.link);
   const [copied, setCopied] = useState(false);
   const isOnboarding =
      typeof window !== "undefined" &&
      new URLSearchParams(window.location.search).get("onboarding") === "1";

   const onboardingState = useMemo(() => {
      let socialCount = 0;
      try {
         const parsed = link.socials ? JSON.parse(link.socials) : [];
         socialCount = Array.isArray(parsed) ? parsed.length : 0;
      } catch {
         socialCount = 0;
      }

      const steps = [
         {
            title: "Profilini tamamla",
            description: "Fotoğraf, görünen ad ve kısa açıklamanı ekle.",
            done: Boolean(link.thumbnail && link.short_bio?.trim()),
         },
         {
            title: "İletişim kanallarını ekle",
            description: "Instagram, WhatsApp, telefon veya e-posta hesabını bağla.",
            done: socialCount > 0,
         },
         {
            title: "İlk bağlantını ekle",
            description: "Ziyaretçinin tıklayacağı en önemli bağlantıyı ekle.",
            done: Array.isArray(link.items) && link.items.length > 0,
         },
         {
            title: "Görünümünü seç ve paylaş",
            description: "Tema seçtikten sonra Paylaşım sekmesinden profilini yayınla.",
            done: Boolean(link.theme_id),
         },
      ];

      const completed = steps.filter((step) => step.done).length;
      return {
         steps,
         completed,
         percent: Math.round((completed / steps.length) * 100),
      };
   }, [link]);

   const { parsedStyle, buttonStyle } = useMemo(() => {
      let pageStyle: any;
      let currentButtonStyle: any = {};

      if (link.custom_theme && link.custom_theme_active) {
         pageStyle = customThemePageStyle(link.custom_theme);
         currentButtonStyle = customThemeButtonStyle(link.custom_theme);
      } else {
         const { background, text_color, font_family, bg_image, button_style } =
            link.theme;
         pageStyle = jsxStyle(stringToCss(background));
         pageStyle.color = text_color;
         pageStyle.fontFamily = font_family;
         if (bg_image) {
            pageStyle.backgroundImage = `url(/${bg_image})`;
         }
         currentButtonStyle = jsxStyle(stringToCss(button_style));
      }

      return {
         parsedStyle: pageStyle,
         buttonStyle: currentButtonStyle,
      };
   }, [link]);

   const copyProfileUrl = async () => {
      const url = `${window.location.origin}/${link.url_name}`;
      try {
         await navigator.clipboard.writeText(url);
         setCopied(true);
         window.setTimeout(() => setCopied(false), 1500);
      } catch {
         setCopied(false);
      }
   };

   return (
      <>
         <Head title="Profili Düzenle" />
         <PageHeader
            title="Profili Düzenle"
            description="Profil bilgilerinizi, bağlantılarınızı ve görünümünüzü tek ekrandan yönetin."
            actions={
               <a
                  href={`/${link.url_name}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
               >
                  Profili Gör
               </a>
            }
         />

         {isOnboarding ? (
            <div className="mt-6 rounded-2xl border border-blue-200 bg-blue-50/70 p-5 sm:p-6">
               <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                  <div>
                     <p className="text-xs font-semibold uppercase tracking-[0.16em] text-blue-600">
                        İlk profil kurulumu
                     </p>
                     <h2 className="mt-1 text-lg font-bold text-slate-900">
                        Profilini yayınlamaya hazırla
                     </h2>
                     <p className="mt-1 max-w-2xl text-sm leading-6 text-slate-600">
                        Bu dört adımı tamamladığında paylaşılabilir dijital profilin hazır olacak.
                     </p>
                  </div>
                  <div className="min-w-[120px] text-left sm:text-right">
                     <p className="text-2xl font-bold text-blue-700">
                        {onboardingState.percent}%
                     </p>
                     <p className="text-xs text-slate-500">
                        {onboardingState.completed}/4 adım tamamlandı
                     </p>
                  </div>
               </div>

               <div className="mt-4 h-2 overflow-hidden rounded-full bg-blue-100">
                  <div
                     className="h-full rounded-full bg-blue-600 transition-all duration-300"
                     style={{ width: `${onboardingState.percent}%` }}
                  />
               </div>

               <div className="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                  {onboardingState.steps.map((step, index) => (
                     <div
                        key={step.title}
                        className="flex gap-3 rounded-xl border border-blue-100 bg-white/80 p-3.5"
                     >
                        <span
                           className={`mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold ${
                              step.done
                                 ? "bg-emerald-100 text-emerald-700"
                                 : "bg-slate-100 text-slate-500"
                           }`}
                        >
                           {step.done ? "✓" : index + 1}
                        </span>
                        <div>
                           <p className="text-sm font-semibold text-slate-900">
                              {step.title}
                           </p>
                           <p className="mt-0.5 text-xs leading-5 text-slate-500">
                              {step.description}
                           </p>
                        </div>
                     </div>
                  ))}
               </div>
            </div>
         ) : null}

         <div className="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div className="min-w-0">
               <Tabs value="profile">
                  <div className="mb-6 max-w-full overflow-x-auto">
                     <TabsHeader className="tabs-header min-w-max bg-transparent p-0">
                        <Tab value="profile" className="px-3 py-[7px] md:px-4">
                           Profil
                        </Tab>
                        <Tab value="links" className="px-3 py-[7px] md:px-4">
                           Bağlantılar
                        </Tab>
                        <Tab value="appearance" className="px-3 py-[7px] md:px-4">
                           Görünüm
                        </Tab>
                        <Tab value="share" className="px-3 py-[7px] md:px-4">
                           Paylaşım
                        </Tab>
                     </TabsHeader>
                  </div>

                  <TabsBody>
                     <TabPanel value="profile" className="p-0">
                        <div className="space-y-6">
                           <LinkProfile link={link} setLink={setLink} />
                           <AddSocialLinks link={link} setLink={setLink} />
                        </div>
                     </TabPanel>

                     <TabPanel value="links" className="p-0">
                        <div className="space-y-4">
                           <PanelCard noPadding bodyClassName="p-5 sm:p-6">
                              <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                 <div>
                                    <h3 className="font-semibold text-slate-900">
                                       Profil bağlantıları
                                    </h3>
                                    <p className="mt-1 text-sm text-slate-500">
                                       Link, metin, görsel ve diğer içerik bloklarını ekleyip sıralayın.
                                    </p>
                                 </div>
                                 <AddBlocks
                                    link={link}
                                    setLink={setLink}
                                    itemPosition={itemLastPosition}
                                 />
                              </div>
                           </PanelCard>
                           <LinkBlocks link={link} setLink={setLink} />
                        </div>
                     </TabPanel>

                     <TabPanel value="appearance" className="p-0">
                        <LinkThemes
                           link={link}
                           themes={themes}
                           setLink={setLink}
                        />
                     </TabPanel>

                     <TabPanel value="share" className="p-0">
                        <div className="space-y-4">
                           <PanelCard noPadding bodyClassName="p-5 sm:p-6">
                              <p className="text-sm font-medium text-slate-500">
                                 Profil adresiniz
                              </p>
                              <p className="mt-2 break-all text-base font-semibold text-slate-900">
                                 {`${props.ziggy?.url ?? ""}/${link.url_name}`}
                              </p>
                              <div className="mt-5 grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                                 <button
                                    type="button"
                                    onClick={copyProfileUrl}
                                    className="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                 >
                                    {copied ? "Kopyalandı" : "Linki Kopyala"}
                                 </button>
                                 <a
                                    href={`/${link.url_name}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                 >
                                    Profili Gör
                                 </a>
                                 <a
                                    href={`/qrcodes/create?link_id=${link.id}&type=biolink`}
                                    className="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                 >
                                    QR Kod Oluştur
                                 </a>
                                 <a
                                    href={`/link/analytics/${link.id}`}
                                    className="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                 >
                                    Analitiği Gör
                                 </a>
                              </div>
                           </PanelCard>
                        </div>
                     </TabPanel>
                  </TabsBody>
               </Tabs>
            </div>

            <div className="min-w-0">
               <div className="lg:sticky lg:top-24">
                  <div className="mb-3 flex items-center justify-between gap-3">
                     <p className="text-sm font-medium text-slate-600">Canlı önizleme</p>
                     <span className="text-xs text-slate-400">Mobil görünüm</span>
                  </div>
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

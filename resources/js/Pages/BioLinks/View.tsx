import SimpleBar from "simplebar-react";
import {
   customThemeButtonStyle,
   customThemePageStyle,
   isSafeHex,
   jsxStyle,
   safeHttpUrl,
   safeMailtoHref,
   safeTelHref,
   stringToCss,
   whatsappHref,
} from "@/utils/utils";
import { useCallback, useEffect, useState } from "react";
import { LinkProps, PageProps } from "@/types";
import { Head, usePage } from "@inertiajs/react";
import LinkBlock from "@/Components/BioLink/LinkBlock";
import UserCircle from "@/Components/Icons/UserCircle";
import { socialType } from "@/utils/data/socials-links";
import icons from "@/Components/Icons";

const IdCardIcon = icons.IdCard;
const LinkIcon = icons.Link;

const View = (props: { link: LinkProps }) => {
   const { link } = props;
   const page = usePage();
   const { app, auth } = page.props as PageProps;
   const [branding, setBranding] = useState("");
   const [copied, setCopied] = useState(false);

   let parsedStyle: any;
   let buttonStyle: any = {};

   if (link.custom_theme && link.custom_theme_active) {
      parsedStyle = customThemePageStyle(link.custom_theme);
      buttonStyle = customThemeButtonStyle(link.custom_theme);
   } else {
      const { background, text_color, font_family, bg_image, button_style } = link.theme;
      parsedStyle = jsxStyle(stringToCss(background));
      parsedStyle.color = text_color;
      parsedStyle.fontFamily = font_family;
      if (bg_image) parsedStyle.backgroundImage = `url(/${bg_image})`;
      buttonStyle = jsxStyle(stringToCss(button_style));
   }

   useEffect(() => {
      if (auth.user && auth.user.roles?.[0]?.name === "BASIC") {
         setBranding(`/${app.logo}`);
      } else if (link.branding) {
         setBranding(`/${link.branding}`);
      } else {
         setBranding(`/${app.logo}`);
      }
   }, [app.logo, auth.user, link.branding]);

   let socials: socialType[] = [];
   if (link.socials) {
      try {
         const parsed = JSON.parse(link.socials);
         socials = Array.isArray(parsed) ? parsed : [];
      } catch {
         socials = [];
      }
   }

   const baseUrl = (page.props as PageProps).ziggy?.url || window.location.origin;
   const cleanBaseUrl = baseUrl.replace(/\/$/, "");
   const profileUrl = `${cleanBaseUrl}/${link.url_name}`;
   const profileTitle = `${link.link_name} | ${app.title || "LinkProfilde"}`;
   const profileDescription =
      link.short_bio?.trim() ||
      `${link.link_name} profilindeki bağlantıları ve iletişim bilgilerini görüntüleyin.`;
   const profileImage = link.thumbnail
      ? `${cleanBaseUrl}/${String(link.thumbnail).replace(/^\//, "")}`
      : undefined;
   const structuredData = {
      "@context": "https://schema.org",
      "@type": "Person",
      name: link.link_name,
      description: profileDescription,
      url: profileUrl,
      ...(profileImage ? { image: profileImage } : {}),
   };

   const socialColor = isSafeHex(link.social_color) ? link.social_color : "#101828";

   const track = useCallback(
      (
         eventType: "link_click" | "social_click" | "vcard_download" | "share",
         payload: { source_id?: number; source_type?: string; label?: string } = {}
      ) => {
         try {
            void fetch(`/api/profile/${encodeURIComponent(link.url_name)}/event`, {
               method: "POST",
               headers: { "Content-Type": "application/json", Accept: "application/json" },
               body: JSON.stringify({ event_type: eventType, ...payload }),
               keepalive: true,
            });
         } catch {
            // Analytics must never block the public profile experience.
         }
      },
      [link.url_name]
   );

   const contactPhone = socials.find(
      (s) => s.link && (s.name === "telephone" || s.name === "whatsapp")
   )?.link;
   const contactEmail = socials.find((s) => s.link && s.name === "email")?.link;
   const canAddToContacts = Boolean(
      link.link_name && contactPhone && String(contactPhone).trim()
   );

   const downloadVCard = () => {
      if (!canAddToContacts) return;
      track("vcard_download", { source_type: "profile", label: "Rehbere Ekle" });

      const name = String(link.link_name)
         .replace(/\\/g, "\\\\")
         .replace(/;/g, "\\;")
         .replace(/,/g, "\\,")
         .replace(/\n/g, " ");
      const digits = String(contactPhone).replace(/\D/g, "");
      const tel = digits.startsWith("0") ? `90${digits.slice(1)}` : digits;
      const vcard = [
         "BEGIN:VCARD",
         "VERSION:3.0",
         `FN:${name}`,
         `N:;${name};;;`,
         `TEL;TYPE=CELL:${tel}`,
         contactEmail ? `EMAIL:${String(contactEmail).replace(/^mailto:/i, "")}` : null,
         `URL:${profileUrl}`,
         "END:VCARD",
      ]
         .filter(Boolean)
         .join("\r\n");

      const blob = new Blob([vcard], { type: "text/vcard;charset=utf-8" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = `${
         link.link_name.replace(/[^a-zA-Z0-9\u00C0-\u024F\s-]/g, "") || "contact"
      }.vcf`;
      a.click();
      URL.revokeObjectURL(url);
   };

   const shareProfile = async () => {
      track("share", { source_type: "profile", label: "Paylaş" });
      try {
         if (navigator.share) {
            await navigator.share({
               title: link.link_name,
               text: profileDescription,
               url: profileUrl,
            });
            return;
         }
         await navigator.clipboard.writeText(profileUrl);
         setCopied(true);
         window.setTimeout(() => setCopied(false), 1600);
      } catch {
         // User cancelling the share sheet is not an error state.
      }
   };

   return (
      <div style={parsedStyle} className="min-h-screen bg-cover bg-center">
         <Head title={profileTitle}>
            <meta name="description" content={profileDescription} />
            <link rel="canonical" href={profileUrl} />
            <meta property="og:type" content="profile" />
            <meta property="og:title" content={profileTitle} />
            <meta property="og:description" content={profileDescription} />
            <meta property="og:url" content={profileUrl} />
            {profileImage ? <meta property="og:image" content={profileImage} /> : null}
            <meta
               name="twitter:card"
               content={profileImage ? "summary_large_image" : "summary"}
            />
            <meta name="twitter:title" content={profileTitle} />
            <meta name="twitter:description" content={profileDescription} />
            {profileImage ? <meta name="twitter:image" content={profileImage} /> : null}
            <script
               type="application/ld+json"
               dangerouslySetInnerHTML={{ __html: JSON.stringify(structuredData) }}
            />
         </Head>

         <SimpleBar style={{ height: "100vh" }}>
            <main className="mx-auto flex min-h-screen w-full max-w-[680px] flex-col px-4 py-6 sm:px-6 sm:py-10">
               <div className="mb-6 flex justify-end">
                  <button
                     type="button"
                     onClick={shareProfile}
                     className="inline-flex h-10 items-center gap-2 rounded-full border border-current/20 bg-white/15 px-4 text-sm font-medium backdrop-blur-md transition hover:bg-white/25"
                     aria-label="Profili paylaş"
                  >
                     <LinkIcon className="h-4 w-4" />
                     {copied ? "Link kopyalandı" : "Paylaş"}
                  </button>
               </div>

               <section className="text-center">
                  <div className="relative mx-auto h-[108px] w-[108px]">
                     {link.thumbnail ? (
                        <img
                           src={`/${link.thumbnail}`}
                           alt={`${link.link_name} profil fotoğrafı`}
                           className="h-full w-full rounded-full border-4 border-white/40 object-cover shadow-lg"
                        />
                     ) : (
                        <UserCircle className="h-full w-full opacity-80" />
                     )}
                  </div>

                  <h1 className="mt-4 text-2xl font-bold tracking-tight sm:text-3xl">
                     {link.link_name}
                  </h1>
                  {link.short_bio ? (
                     <p className="mx-auto mt-2 max-w-[520px] whitespace-pre-line text-sm leading-6 opacity-85 sm:text-base">
                        {link.short_bio}
                     </p>
                  ) : null}

                  {socials.length > 0 ? (
                     <div className="mt-5 flex flex-wrap items-center justify-center gap-2">
                        {socials.map((item, ind) => {
                           const Icon = icons[item.icon];
                           if (!Icon) return null;

                           const href =
                              item.name === "email"
                                 ? safeMailtoHref(item.link)
                                 : item.name === "telephone"
                                 ? safeTelHref(item.link)
                                 : item.name === "whatsapp"
                                 ? whatsappHref(item.link)
                                 : safeHttpUrl(item.link);
                           if (!href) return null;

                           const external = item.name !== "email" && item.name !== "telephone";
                           return (
                              <a
                                 key={`${item.name}-${ind}`}
                                 href={href}
                                 onClick={() =>
                                    track("social_click", {
                                       source_type: "social",
                                       label: item.name,
                                    })
                                 }
                                 className="inline-flex h-11 w-11 items-center justify-center rounded-full border border-current/15 bg-white/10 backdrop-blur-sm transition hover:-translate-y-0.5 hover:bg-white/20"
                                 aria-label={`${item.name} bağlantısını aç`}
                                 {...(external
                                    ? { target: "_blank", rel: "noopener noreferrer" }
                                    : {})}
                              >
                                 <Icon className="h-5 w-5" style={{ color: socialColor }} />
                              </a>
                           );
                        })}
                     </div>
                  ) : null}

                  {canAddToContacts ? (
                     <div className="mt-5 flex justify-center">
                        <button
                           type="button"
                           onClick={downloadVCard}
                           className="inline-flex min-h-[44px] items-center gap-2 rounded-full border border-current/25 bg-white/10 px-5 py-2.5 text-sm font-semibold backdrop-blur-sm transition hover:bg-white/20"
                           style={{ color: socialColor, borderColor: socialColor }}
                        >
                           <IdCardIcon className="h-5 w-5" />
                           Rehbere Ekle
                        </button>
                     </div>
                  ) : null}
               </section>

               <section className="mt-7 flex-1">
                  {link.items.map((item) => (
                     <LinkBlock
                        key={item.id}
                        item={item}
                        buttonStyle={buttonStyle}
                        onTrack={track}
                     />
                  ))}
               </section>

               <footer className="mt-10 flex justify-center pb-2 pt-6">
                  {branding ? (
                     <img
                        src={branding}
                        alt={app.title || "LinkProfilde"}
                        className="h-10 w-auto max-w-[120px] object-contain opacity-80"
                     />
                  ) : null}
               </footer>
            </main>
         </SimpleBar>
      </div>
   );
};

export default View;

import { useEffect, useState } from "react";
import SimpleBar from "simplebar-react";
import UserCircle from "../Icons/UserCircle";
import { LinkProps, PageProps } from "@/types";
import { usePage } from "@inertiajs/react";
import LinkBlock from "./LinkBlock";
import { socialType } from "@/utils/data/socials-links";
import {
   isSafeHex,
   safeHttpUrl,
   safeMailtoHref,
   safeTelHref,
   whatsappHref,
} from "@/utils/utils";
import icons from "../Icons";
const IdCardIcon = icons.IdCard;

const LinkPreview = (props: { link: LinkProps; buttonStyle: any }) => {
   const page = usePage();
   const { link, buttonStyle } = props;
   const { app, auth } = page.props as PageProps;
   const [branding, setBranding] = useState("");

   useEffect(() => {
      if (auth.user.roles[0].name === "BASIC") {
         setBranding(`/${app.logo}`);
      } else {
         if (link.branding) {
            setBranding(`/${link.branding}`);
         } else {
            setBranding(`/${app.logo}`);
         }
      }
   }, [link]);

   let socials: socialType[] = [];
   if (link.socials) {
      socials = JSON.parse(link.socials);
   }

   const socialColor = isSafeHex(link.social_color)
      ? link.social_color
      : "#101828";

   const contactPhone = socials.find(
      (s) => s.link && (s.name === "telephone" || s.name === "whatsapp")
   )?.link;
   const canAddToContacts =
      link.link_name && contactPhone && String(contactPhone).trim();

   const downloadVCard = () => {
      if (!canAddToContacts) return;
      const name = String(link.link_name)
         .replace(/\\/g, "\\\\")
         .replace(/;/g, "\\;")
         .replace(/,/g, "\\,")
         .replace(/\n/g, " ");
      const d = String(contactPhone).replace(/\D/g, "");
      const tel = d.startsWith("0") ? "90" + d.slice(1) : d;
      const vcard = [
         "BEGIN:VCARD",
         "VERSION:3.0",
         `FN:${name}`,
         `N:;${name};;;`,
         `TEL;TYPE=CELL:${tel}`,
         "END:VCARD",
      ].join("\r\n");
      const blob = new Blob([vcard], { type: "text/vcard;charset=utf-8" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = `${link.link_name.replace(/[^a-zA-Z0-9\u00C0-\u024F\s-]/g, "") || "contact"}.vcf`;
      a.click();
      URL.revokeObjectURL(url);
   };

   return (
      <SimpleBar style={{ height: "100%" }} className="px-4 py-5">
         <div className="min-h-[calc(100vh-206px)] flex flex-col justify-between">
            <div>
               <div className="flex flex-col items-center">
                  {link.thumbnail ? (
                     <img
                        src={`/${link.thumbnail}`}
                        alt="linkdrop"
                        className="w-[100px] h-[100px] object-cover rounded-full"
                     />
                  ) : (
                     <UserCircle className="w-[100px] h-[100px] text-gray-700" />
                  )}
                  <p className="text-xl font-medium mt-2">{link.link_name}</p>
                  <p className="font-medium text-justify mt-2 mb-4">
                     {link.short_bio}
                  </p>
               </div>
               {socials.length > 0 && (
                  <div className="flex items-center justify-center flex-wrap gap-4 mt-2 mb-8">
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

                        const external =
                           item.name !== "email" && item.name !== "telephone";

                        return (
                           <a
                              key={ind}
                              href={href}
                              className="mx-2"
                              {...(external
                                 ? {
                                      target: "_blank",
                                      rel: "noopener noreferrer",
                                   }
                                 : {})}
                           >
                              <Icon
                                 className="w-6 h-6"
                                 style={{ color: socialColor }}
                              />
                           </a>
                        );
                     })}
                  </div>
               )}

               {canAddToContacts && (
                  <div className="flex justify-center mt-2 mb-6">
                     <button
                        type="button"
                        onClick={downloadVCard}
                        className="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border border-current opacity-90 hover:opacity-100 transition-opacity"
                        style={{
                           color: socialColor,
                           borderColor: socialColor,
                        }}
                     >
                        <IdCardIcon className="w-5 h-5" />
                        Rehbere Ekle
                     </button>
                  </div>
               )}

               {link.items.map((item) => (
                  <LinkBlock item={item} buttonStyle={buttonStyle} />
               ))}
            </div>

            <img src={branding} alt="" className="w-10 mx-auto" />
         </div>
      </SimpleBar>
   );
};

export default LinkPreview;

import { LinkItemProps } from "@/types";
import { useState } from "react";
import icons from "../Icons";
import RightArrow from "../Icons/RightArrow";
import { safeEmbedSrc, safeHttpUrl, safeTikTokUrl } from "@/utils/utils";

interface Props {
   item: LinkItemProps;
   buttonStyle: any;
   onTrack?: (
      eventType: "link_click",
      payload: { source_id: number; source_type: "link_item"; label?: string }
   ) => void;
}

const LinkBlock = ({ item, buttonStyle, onTrack }: Props) => {
   const [openAcc, setOpenAcc] = useState(false);
   const Icon = icons[item.item_icon];
   const linkHref = safeHttpUrl(item.item_link);
   const embedSrc = safeEmbedSrc(item.item_link);
   const tiktokUrl = safeTikTokUrl(item.item_link);

   const trackLink = () => {
      onTrack?.("link_click", {
         source_id: item.id,
         source_type: "link_item",
         label: item.item_title,
      });
   };

   const blockClass =
      "my-3 flex min-h-[56px] items-center justify-between gap-3 px-4 py-3.5 font-medium transition-transform duration-150 hover:-translate-y-0.5";

   if (item.item_icon === "Link") {
      if (!linkHref) {
         return (
            <div className={blockClass} style={buttonStyle}>
               <span className="flex h-8 w-8 shrink-0 items-center justify-center">
                  {Icon ? <Icon className="h-5 w-5" /> : null}
               </span>
               <span className="min-w-0 flex-1 text-center">{item.item_title}</span>
               <span className="h-4 w-4" />
            </div>
         );
      }

      return (
         <a
            target="_blank"
            rel="noopener noreferrer"
            href={linkHref}
            onClick={trackLink}
            className={blockClass}
            style={buttonStyle}
         >
            <span className="flex h-8 w-8 shrink-0 items-center justify-center">
               {Icon ? <Icon className="h-5 w-5" /> : null}
            </span>
            <span className="min-w-0 flex-1 text-center">{item.item_title}</span>
            <RightArrow className="h-4 w-4 shrink-0 opacity-60" />
         </a>
      );
   }

   if (item.item_icon === "Heading") {
      const headingClass = "font-semibold leading-tight";
      return (
         <div className="my-5 px-2 text-center">
            {item.item_sub_type === "h1" ? (
               <h1 className={headingClass}>{item.item_title}</h1>
            ) : item.item_sub_type === "h2" ? (
               <h2 className={headingClass}>{item.item_title}</h2>
            ) : item.item_sub_type === "h3" ? (
               <h3 className={headingClass}>{item.item_title}</h3>
            ) : item.item_sub_type === "h4" ? (
               <h4 className={headingClass}>{item.item_title}</h4>
            ) : item.item_sub_type === "h5" ? (
               <h5 className={headingClass}>{item.item_title}</h5>
            ) : item.item_sub_type === "h6" ? (
               <h6 className={headingClass}>{item.item_title}</h6>
            ) : null}
         </div>
      );
   }

   return (
      <div className="card my-3 overflow-hidden" style={buttonStyle}>
         <button
            type="button"
            onClick={() => setOpenAcc((current) => !current)}
            className="flex min-h-[56px] w-full items-center justify-between gap-3 px-4 py-3 text-base font-medium"
            aria-expanded={openAcc}
            aria-controls={`bio-block-${item.id}`}
         >
            <span className="flex h-8 w-8 shrink-0 items-center justify-center">
               {Icon ? <Icon className="h-5 w-5" /> : null}
            </span>
            <span className="min-w-0 flex-1 text-center">{item.item_title}</span>
            <RightArrow
               className={`h-4 w-4 shrink-0 transition-transform duration-200 ${
                  openAcc ? "rotate-90" : "rotate-0"
               }`}
            />
         </button>

         {openAcc ? (
            <div id={`bio-block-${item.id}`} className="px-3 pb-3 pt-0">
               {item.item_icon === "Image" ? (
                  linkHref ? (
                     <a
                        href={linkHref}
                        target="_blank"
                        rel="noopener noreferrer"
                        onClick={trackLink}
                     >
                        <img
                           src={`/${item.content}`}
                           alt={item.item_title}
                           className="w-full rounded-xl object-cover"
                           loading="lazy"
                        />
                     </a>
                  ) : (
                     <img
                        src={`/${item.content}`}
                        alt={item.item_title}
                        className="w-full rounded-xl object-cover"
                        loading="lazy"
                     />
                  )
               ) : item.item_icon === "Paragraph" ? (
                  <p className="text-left font-normal leading-6">{item.content}</p>
               ) : null}

               {item.item_type === "Embed" && item.item_link ? (
                  item.item_icon === "TikTok" ? (
                     tiktokUrl ? (
                        <blockquote
                           cite={tiktokUrl}
                           data-video-id={tiktokUrl.split("video").pop()?.slice(1)}
                           className="tiktok-embed h-auto w-full"
                        >
                           <section></section>
                        </blockquote>
                     ) : null
                  ) : embedSrc ? (
                     <iframe
                        width="100%"
                        height="220"
                        src={embedSrc}
                        className="rounded-xl"
                        sandbox="allow-scripts allow-same-origin allow-popups"
                        referrerPolicy="no-referrer"
                        allow="fullscreen; encrypted-media"
                        allowFullScreen
                        loading="lazy"
                        title={item.item_title || "Embed"}
                     ></iframe>
                  ) : null
               ) : null}
            </div>
         ) : null}
      </div>
   );
};

export default LinkBlock;

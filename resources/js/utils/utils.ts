import { LinkProps, PaginationProps } from "@/types";
import { socialType } from "./data/socials-links";

export function stringToCss(strStyle: string) {
   const parsedStyle: any = {};

   // Parse the CSS string
   strStyle.split(";").forEach((declaration) => {
      const [property, value] = declaration.split(":");
      if (property && value) {
         parsedStyle[property.trim()] = value.trim();
      }
   });

   return parsedStyle;
}

export function jsxStyle(styles: {
   [key: string]: string;
}): React.CSSProperties {
   const inlineStyles: any = {};
   for (const key in styles) {
      if (styles.hasOwnProperty(key)) {
         const propKey = key.replace(/-([a-z])/g, (g) => g[1].toUpperCase());
         inlineStyles[propKey] = styles[key];
      }
   }
   return inlineStyles;
}

export function getLink(link: LinkProps, name: string): string {
   if (link.socials) {
      const socials: socialType[] = JSON.parse(link.socials);
      const value = socials.find((item) => item.name === name);
      if (value && value.link) {
         return value.link;
      } else {
         return "";
      }
   } else {
      return "";
   }
}

export const soundCloudUrl = (url: string) => {
   return `https://w.soundcloud.com/player/?url=${url}&amp;color=%23ff5500&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;show_teaser=true&amp;visual=true`;
};

export const youTubeUrl = (url: string) => {
   let lastUrl: any = url.split("/").pop();
   const videoId = lastUrl.split("=").pop();
   return `https://www.youtube.com/embed/${videoId}`;
};

export const spotifyUrl = (url: string) => {
   let urlArray = url.split("/");
   let videoId: any = urlArray.pop();
   const videoType = urlArray.pop();
   const videoUrl = `${videoType}/${videoId.split("?")[0]}`;
   return `https://open.spotify.com/embed/${videoUrl}`;
};

export const vimeoUrl = (url: string) => {
   const lastUrl = url.split("/").pop();
   return `https://player.vimeo.com/video/${lastUrl}`;
};

export const pageChange = (
   current: PaginationProps | undefined | null,
   previous: PaginationProps | undefined | null
): boolean => {
   if (!current || !previous) return false;
   const curData = current.data ?? [];
   const prevData = previous.data ?? [];
   const curLength = curData.length;
   const prevLength = prevData.length;

   if (curLength !== prevLength) {
      return true;
   }
   for (let i = 0; i < curLength; i++) {
      const newItem = curData[i];
      const prevItem = prevData[i];
      if (!newItem || !prevItem || newItem.id !== prevItem.id) {
         return true;
      }
   }
   if (curData[0]?.qrcode !== prevData[0]?.qrcode) {
      return true;
   }
   return false;
};

export const formats = [
   "font",
   "size",
   "bold",
   "italic",
   "underline",
   "strike",
   "color",
   "background",
   "script",
   "header",
   "blockquote",
   "code-block",
   "indent",
   "list",
   "direction",
   "align",
   "link",
   "image",
   "video",
   "formula",
];

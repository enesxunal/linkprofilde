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

const ALLOWED_FONTS = [
   "Inter, sans-serif",
   "MintGrotesk, sans-serif",
   "DM Sans, sans-serif",
   "Bebas Neue, cursive",
   "Poppins, sans-serif",
   "Quicksand, sans-serif",
];

const EMBED_HOST_RULES: { host: string; pathPrefix: string }[] = [
   { host: "www.youtube.com", pathPrefix: "/embed/" },
   { host: "youtube.com", pathPrefix: "/embed/" },
   { host: "www.youtube-nocookie.com", pathPrefix: "/embed/" },
   { host: "youtube-nocookie.com", pathPrefix: "/embed/" },
   { host: "player.vimeo.com", pathPrefix: "/video/" },
   { host: "open.spotify.com", pathPrefix: "/embed/" },
   { host: "w.soundcloud.com", pathPrefix: "/player" },
];

const TIKTOK_HOSTS = [
   "tiktok.com",
   "www.tiktok.com",
   "m.tiktok.com",
   "vm.tiktok.com",
   "vt.tiktok.com",
];

function hasControlOrSpace(value: string): boolean {
   return /[\u0000-\u001F\u007F]/.test(value) || /\s/.test(value);
}

export function safeHttpUrl(raw: unknown): string | null {
   if (typeof raw !== "string") return null;
   const trimmed = raw.trim();
   if (!trimmed || hasControlOrSpace(trimmed) || trimmed.startsWith("//")) {
      return null;
   }
   let parsed: URL;
   try {
      parsed = new URL(trimmed);
   } catch {
      return null;
   }
   const scheme = parsed.protocol.toLowerCase();
   if (scheme !== "http:" && scheme !== "https:") return null;
   if (parsed.username || parsed.password) return null;
   return parsed.href;
}

export function safeEmbedSrc(raw: unknown): string | null {
   const href = safeHttpUrl(raw);
   if (!href) return null;
   const parsed = new URL(href);
   const host = parsed.hostname.toLowerCase().replace(/\.$/, "");
   const rule = EMBED_HOST_RULES.find((item) => item.host === host);
   if (!rule) return null;
   if (!parsed.pathname.startsWith(rule.pathPrefix)) return null;
   return href;
}

export function safeTikTokUrl(raw: unknown): string | null {
   const href = safeHttpUrl(raw);
   if (!href) return null;
   const parsed = new URL(href);
   const host = parsed.hostname.toLowerCase().replace(/\.$/, "");
   if (!TIKTOK_HOSTS.includes(host)) return null;
   if (parsed.pathname === "" || parsed.pathname === "/") return null;
   return href;
}

export function safeMailtoHref(raw: unknown): string | null {
   if (typeof raw !== "string") return null;
   let value = raw.trim();
   if (/^mailto:/i.test(value)) {
      value = value.slice(7);
   }
   if (!value || hasControlOrSpace(value)) return null;
   if (/^(javascript|data|blob|vbscript):/i.test(value)) return null;
   if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return null;
   return "mailto:" + value;
}

export function safeTelHref(raw: unknown): string | null {
   if (typeof raw !== "string") return null;
   const digits = raw.replace(/[^\d+]/g, "");
   if (!/^\+?\d{7,20}$/.test(digits)) return null;
   return "tel:" + digits;
}

export function whatsappHref(raw: unknown): string | null {
   if (typeof raw !== "string") return null;
   const d = raw.replace(/\D/g, "");
   if (d.length < 7 || d.length > 20) return null;
   const num = d.startsWith("0") ? "90" + d.slice(1) : d;
   return "https://wa.me/" + num;
}

export function isSafeHex(value: unknown): value is string {
   return typeof value === "string" && /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(value);
}

export function safeFontFamily(value: unknown): string {
   return typeof value === "string" && ALLOWED_FONTS.includes(value)
      ? value
      : "Inter, sans-serif";
}

export function safeRadius(value: unknown): string {
   return typeof value === "string" && ["8px", "12px", "30px"].includes(value)
      ? value
      : "30px";
}

function isUploadPath(path: unknown): path is string {
   return (
      typeof path === "string" &&
      /^upload\/[A-Za-z0-9._-]+\.(jpg|jpeg|png)$/i.test(path) &&
      !path.includes("..")
   );
}

export function customThemePageStyle(theme: {
   background_type?: string;
   bg_color?: string | null;
   bg_image?: string | null;
   text_color?: string | null;
   font_family?: string | null;
}): React.CSSProperties {
   const style: React.CSSProperties = {
      color: isSafeHex(theme.text_color) ? theme.text_color : "#ffffff",
      fontFamily: safeFontFamily(theme.font_family),
   };
   if (theme.background_type === "image" && isUploadPath(theme.bg_image)) {
      style.backgroundImage = `url(/${theme.bg_image})`;
   } else {
      style.backgroundColor = isSafeHex(theme.bg_color)
         ? theme.bg_color
         : "#30425A";
   }
   return style;
}

export function customThemeButtonStyle(theme: {
   btn_text_color?: string | null;
   btn_radius?: string | null;
   btn_transparent?: unknown;
   btn_bg_color?: string | null;
}): React.CSSProperties {
   const transparent =
      theme.btn_transparent === true ||
      theme.btn_transparent === 1 ||
      theme.btn_transparent === "1";
   return {
      color: isSafeHex(theme.btn_text_color) ? theme.btn_text_color : "#1d2939",
      borderRadius: safeRadius(theme.btn_radius),
      background: transparent
         ? "transparent"
         : isSafeHex(theme.btn_bg_color)
         ? theme.btn_bg_color
         : "#ffffff",
   };
}

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

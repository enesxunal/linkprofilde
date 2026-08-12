import { PaginationProps } from "@/types";

function toUrl(raw: string): URL | null {
   if (!raw) {
      return null;
   }

   try {
      return new URL(raw, window.location.origin);
   } catch {
      return null;
   }
}

export function extraPaginatorParams(info: PaginationProps): URLSearchParams {
   const sample =
      info.first_page_url ||
      info.next_page_url ||
      info.prev_page_url ||
      info.last_page_url ||
      "";
   const params = new URLSearchParams();
   const url = toUrl(sample);
   if (!url) {
      return params;
   }

   url.searchParams.forEach((value, key) => {
      if (key !== "page" && key !== "per_page") {
         params.set(key, value);
      }
   });

   return params;
}

export function buildPaginatorUrl(
   info: PaginationProps,
   page: number,
   perPage: number
): string {
   const params = extraPaginatorParams(info);
   params.set("page", String(page));
   params.set("per_page", String(perPage));
   const base = toUrl(info.path || "");
   const pathname = base?.pathname || info.path || window.location.pathname;

   return `${pathname}?${params.toString()}`;
}

export function withPerPage(rawUrl: string, perPage: number): string {
   const url = toUrl(rawUrl);
   if (!url) {
      const joiner = rawUrl.includes("?") ? "&" : "?";
      return `${rawUrl}${joiner}per_page=${perPage}`;
   }

   url.searchParams.set("per_page", String(perPage));
   return `${url.pathname}${url.search}`;
}

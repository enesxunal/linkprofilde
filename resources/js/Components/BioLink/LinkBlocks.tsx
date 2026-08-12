import axios from "axios";
import { useRef } from "react";
import { LinkProps } from "@/types";
import Move from "../Icons/Move";
import { error } from "@/utils/toast";
import icons from "../Icons";
import EditBlock from "./EditBlock";
import DeleteBlock from "./DeleteBlock";
import EmptyState from "@/Components/Panel/EmptyState";
import PanelCard from "@/Components/Panel/PanelCard";

interface Props {
   link: LinkProps;
   setLink: (state: any) => void;
}

const LinkBlocks = (props: Props) => {
   const { link, setLink } = props;
   const bioLinkItemsRef = useRef<HTMLDivElement>(null);

   const handleDragStart = (e: React.DragEvent<HTMLDivElement>) => {
      e.currentTarget.classList.add("dragging");
   };

   const handleDragEnd = async (e: React.DragEvent<HTMLDivElement>) => {
      e.currentTarget.classList.remove("dragging");

      const bioLink = document.getElementById("bioLinkItems");
      if (bioLink) {
         const elements = bioLink.getElementsByTagName("div");
         const updatedItems = [];
         for (let i = 0; i < elements.length; i++) {
            const element = elements[i];
            const id = parseInt(element.dataset.item_id || "");
            if (id) updatedItems.push({ id, position: i + 1 });
         }

         const res = await axios.put(
            `/bio-links/customize/block/position/${link.id}`,
            {
               linkItems: updatedItems,
            }
         );

         if (res.data.success) {
            setLink(res.data.link);
         } else if (res.data.error) {
            error(res.data.error);
         }
      }
   };

   const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
      e.preventDefault();
      const afterElement = getDragAfterElement(
         bioLinkItemsRef.current!,
         e.clientY
      );
      const draggable: any = document.querySelector(".dragging");

      if (afterElement == null) {
         bioLinkItemsRef.current?.appendChild(draggable);
      } else {
         bioLinkItemsRef.current?.insertBefore(draggable, afterElement);
      }
   };

   const getDragAfterElement = (
      container: HTMLElement,
      y: number
   ): HTMLElement | null => {
      const draggableElements = [
         ...container.querySelectorAll<HTMLElement>(
            ".draggable:not(.dragging)"
         ),
      ];

      return draggableElements.reduce(
         (closest: any, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
               return { offset: offset, element: child };
            } else {
               return closest;
            }
         },
         { offset: Number.NEGATIVE_INFINITY, element: null }
      ).element;
   };

   return (
      <div
         id="bioLinkItems"
         className="bioLinkItems space-y-3"
         ref={bioLinkItemsRef}
         onDragOver={handleDragOver}
      >
         {link.items.length === 0 ? (
            <PanelCard>
               <EmptyState
                  title="Henüz blok eklenmedi"
                  description="Blok Ekle ile link, başlık, görsel veya gömülü içerik ekleyebilirsiniz."
               />
            </PanelCard>
         ) : (
            link.items.map((item) => {
               const Icon = icons[item.item_icon];
               return (
                  <div
                     draggable
                     key={item.id}
                     data-item_id={item.id}
                     className="draggable flex items-center gap-3"
                     onDragStart={handleDragStart}
                     onDragEnd={handleDragEnd}
                  >
                     <Move
                        id="elementMove"
                        className="h-5 w-5 shrink-0 cursor-grab text-slate-400"
                     />
                     <div className="flex w-full min-w-0 items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm transition hover:border-blue-200 hover:bg-slate-50">
                        <div className="flex min-w-0 items-center gap-3">
                           <Icon className="h-5 w-5 shrink-0 text-slate-600" />
                           <div className="min-w-0">
                              <p className="truncate text-sm font-medium text-slate-900">
                                 {item.item_title}
                              </p>
                              <p className="truncate text-xs text-slate-500">
                                 {item.item_icon}
                              </p>
                           </div>
                        </div>
                        <div className="flex shrink-0 items-center gap-1">
                           <EditBlock block={item} setLink={setLink} />
                           <DeleteBlock block={item} setLink={setLink} />
                        </div>
                     </div>
                  </div>
               );
            })
         )}
      </div>
   );
};

export default LinkBlocks;

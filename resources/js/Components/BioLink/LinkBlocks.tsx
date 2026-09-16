import axios from "axios";
import { useRef, useState } from "react";
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

type PositionItem = { id: number; position: number };

const LinkBlocks = (props: Props) => {
   const { link, setLink } = props;
   const bioLinkItemsRef = useRef<HTMLDivElement>(null);
   const [savingOrder, setSavingOrder] = useState(false);

   const savePositions = async (updatedItems: PositionItem[]) => {
      if (savingOrder || updatedItems.length === 0) return;

      setSavingOrder(true);
      try {
         const res = await axios.put(
            `/bio-links/customize/block/position/${link.id}`,
            { linkItems: updatedItems }
         );

         if (res.data.success && res.data.link) {
            setLink(res.data.link);
         } else if (res.data.error) {
            error(res.data.error);
         }
      } catch (err: any) {
         error(
            err?.response?.data?.error ||
               "Blok sırası kaydedilemedi. Lütfen tekrar deneyin."
         );
      } finally {
         setSavingOrder(false);
      }
   };

   const positionsFromDom = (): PositionItem[] => {
      const container = bioLinkItemsRef.current;
      if (!container) return [];

      return Array.from(
         container.querySelectorAll<HTMLElement>(
            ":scope > .draggable[data-item_id]"
         )
      )
         .map((element, index) => ({
            id: Number(element.dataset.item_id),
            position: index + 1,
         }))
         .filter((item) => Number.isInteger(item.id) && item.id > 0);
   };

   const handleDragStart = (e: React.DragEvent<HTMLDivElement>) => {
      if (savingOrder) {
         e.preventDefault();
         return;
      }
      e.currentTarget.classList.add("dragging", "opacity-60");
      e.dataTransfer.effectAllowed = "move";
   };

   const handleDragEnd = async (e: React.DragEvent<HTMLDivElement>) => {
      e.currentTarget.classList.remove("dragging", "opacity-60");
      await savePositions(positionsFromDom());
   };

   const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
      e.preventDefault();
      if (savingOrder || !bioLinkItemsRef.current) return;

      const afterElement = getDragAfterElement(
         bioLinkItemsRef.current,
         e.clientY
      );
      const draggable = bioLinkItemsRef.current.querySelector<HTMLElement>(
         ":scope > .dragging"
      );
      if (!draggable) return;

      if (afterElement == null) {
         bioLinkItemsRef.current.appendChild(draggable);
      } else {
         bioLinkItemsRef.current.insertBefore(draggable, afterElement);
      }
   };

   const getDragAfterElement = (
      container: HTMLElement,
      y: number
   ): HTMLElement | null => {
      const draggableElements = [
         ...container.querySelectorAll<HTMLElement>(
            ":scope > .draggable:not(.dragging)"
         ),
      ];

      return draggableElements.reduce(
         (closest: { offset: number; element: HTMLElement | null }, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
               return { offset, element: child };
            }
            return closest;
         },
         { offset: Number.NEGATIVE_INFINITY, element: null }
      ).element;
   };

   const moveItem = async (itemId: number, direction: -1 | 1) => {
      if (savingOrder) return;

      const ordered = [...link.items];
      const currentIndex = ordered.findIndex((item) => item.id === itemId);
      const nextIndex = currentIndex + direction;

      if (
         currentIndex < 0 ||
         nextIndex < 0 ||
         nextIndex >= ordered.length
      ) {
         return;
      }

      const [item] = ordered.splice(currentIndex, 1);
      ordered.splice(nextIndex, 0, item);

      await savePositions(
         ordered.map((entry, index) => ({
            id: entry.id,
            position: index + 1,
         }))
      );
   };

   return (
      <div>
         {link.items.length > 0 ? (
            <div className="mb-3 flex items-center justify-between gap-3 px-1">
               <p className="text-xs text-slate-500">
                  Masaüstünde sürükleyin; mobilde okları kullanın.
               </p>
               {savingOrder ? (
                  <span
                     role="status"
                     className="shrink-0 text-xs font-medium text-blue-600"
                  >
                     Sıra kaydediliyor…
                  </span>
               ) : null}
            </div>
         ) : null}

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
               link.items.map((item, index) => {
                  const Icon = icons[item.item_icon];
                  return (
                     <div
                        draggable={!savingOrder}
                        key={item.id}
                        data-item_id={item.id}
                        className="draggable flex items-center gap-2 sm:gap-3"
                        onDragStart={handleDragStart}
                        onDragEnd={handleDragEnd}
                     >
                        <Move
                           id="elementMove"
                           className="hidden h-5 w-5 shrink-0 cursor-grab text-slate-400 active:cursor-grabbing sm:block"
                        />

                        <div className="flex flex-col gap-1 sm:hidden">
                           <button
                              type="button"
                              disabled={savingOrder || index === 0}
                              onClick={() => moveItem(item.id, -1)}
                              className="flex h-7 w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-xs font-bold text-slate-500 disabled:cursor-not-allowed disabled:opacity-30"
                              aria-label={`${item.item_title} bloğunu yukarı taşı`}
                           >
                              ↑
                           </button>
                           <button
                              type="button"
                              disabled={
                                 savingOrder || index === link.items.length - 1
                              }
                              onClick={() => moveItem(item.id, 1)}
                              className="flex h-7 w-7 items-center justify-center rounded-md border border-slate-200 bg-white text-xs font-bold text-slate-500 disabled:cursor-not-allowed disabled:opacity-30"
                              aria-label={`${item.item_title} bloğunu aşağı taşı`}
                           >
                              ↓
                           </button>
                        </div>

                        <div className="flex w-full min-w-0 items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-3 py-3 shadow-sm transition hover:border-blue-200 hover:bg-slate-50 sm:px-4">
                           <div className="flex min-w-0 items-center gap-3">
                              {Icon ? (
                                 <Icon className="h-5 w-5 shrink-0 text-slate-600" />
                              ) : null}
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
      </div>
   );
};

export default LinkBlocks;

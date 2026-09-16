import {
   Children,
   cloneElement,
   createContext,
   isValidElement,
   ReactElement,
   ReactNode,
   useContext,
   useEffect,
   useRef,
   useState,
} from "react";

const join = (...values: Array<string | undefined | false>) =>
   values.filter(Boolean).join(" ");

type AnyProps = Record<string, any> & { children?: ReactNode; className?: string };

export const Button = ({
   children,
   className,
   variant: _variant,
   color: _color,
   ripple: _ripple,
   loading,
   ...props
}: AnyProps) => (
   <button
      type={props.type ?? "button"}
      {...props}
      disabled={props.disabled || loading}
      className={join(
         "inline-flex items-center justify-center disabled:cursor-not-allowed disabled:opacity-60",
         className
      )}
   >
      {children}
   </button>
);

export const IconButton = ({
   children,
   className,
   variant: _variant,
   color: _color,
   ripple: _ripple,
   ...props
}: AnyProps) => (
   <button
      type={props.type ?? "button"}
      {...props}
      className={join("inline-flex items-center justify-center", className)}
   >
      {children}
   </button>
);

export const Avatar = ({
   src,
   alt = "",
   className,
   size: _size,
   variant: _variant,
   ...props
}: AnyProps) => (
   <img
      src={src}
      alt={alt}
      {...props}
      className={join("h-10 w-10 rounded-full object-cover", className)}
   />
);

export const Dialog = ({
   open,
   handler,
   children,
   className,
   size: _size,
   dismiss: _dismiss,
   animate: _animate,
   ...props
}: AnyProps) => {
   const dialogRef = useRef<HTMLDivElement>(null);

   useEffect(() => {
      if (!open) return;
      const previousOverflow = document.body.style.overflow;
      const previousFocus = document.activeElement as HTMLElement | null;
      document.body.style.overflow = "hidden";

      const focusTimer = window.setTimeout(() => {
         const focusable = dialogRef.current?.querySelector<HTMLElement>(
            'button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
         );
         (focusable ?? dialogRef.current)?.focus();
      }, 0);

      const onKeyDown = (event: KeyboardEvent) => {
         if (event.key === "Escape") {
            handler?.();
            return;
         }
         if (event.key !== "Tab" || !dialogRef.current) return;

         const items = Array.from(
            dialogRef.current.querySelectorAll<HTMLElement>(
               'button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
            )
         );
         if (items.length === 0) {
            event.preventDefault();
            dialogRef.current.focus();
            return;
         }

         const first = items[0];
         const last = items[items.length - 1];
         if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
         } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
         }
      };

      window.addEventListener("keydown", onKeyDown);
      return () => {
         window.clearTimeout(focusTimer);
         document.body.style.overflow = previousOverflow;
         window.removeEventListener("keydown", onKeyDown);
         previousFocus?.focus?.();
      };
   }, [open, handler]);

   if (!open) return null;

   return (
      <div
         className="fixed inset-0 z-[1000] flex items-center justify-center bg-slate-950/45 p-4 backdrop-blur-[1px]"
         role="presentation"
         onMouseDown={(event) => {
            if (event.target === event.currentTarget) handler?.();
         }}
      >
         <div
            ref={dialogRef}
            role="dialog"
            aria-modal="true"
            tabIndex={-1}
            {...props}
            className={join(
               "max-h-[calc(100vh-2rem)] w-full max-w-xl overflow-y-auto rounded-2xl bg-white shadow-2xl",
               className
            )}
            onMouseDown={(event) => event.stopPropagation()}
         >
            {children}
         </div>
      </div>
   );
};

type MenuContextValue = {
   open: boolean;
   setOpen: (open: boolean) => void;
   placement?: string;
};
const MenuContext = createContext<MenuContextValue | null>(null);

export const Menu = ({ children, placement }: AnyProps) => {
   const [open, setOpen] = useState(false);
   const menuRef = useRef<HTMLDivElement>(null);

   useEffect(() => {
      if (!open) return;
      const closeOnOutside = (event: MouseEvent) => {
         if (!menuRef.current?.contains(event.target as Node)) setOpen(false);
      };
      const closeOnEscape = (event: KeyboardEvent) => {
         if (event.key === "Escape") setOpen(false);
      };
      document.addEventListener("mousedown", closeOnOutside);
      document.addEventListener("keydown", closeOnEscape);
      return () => {
         document.removeEventListener("mousedown", closeOnOutside);
         document.removeEventListener("keydown", closeOnEscape);
      };
   }, [open]);

   return (
      <MenuContext.Provider value={{ open, setOpen, placement }}>
         <div ref={menuRef} className="relative inline-block">{children}</div>
      </MenuContext.Provider>
   );
};

export const MenuHandler = ({ children }: AnyProps) => {
   const context = useContext(MenuContext);
   const child = Children.only(children) as ReactElement<any>;

   return cloneElement<any>(child, {
      "aria-haspopup": "menu",
      "aria-expanded": context?.open ?? false,
      onClick: (event: any) => {
         child.props.onClick?.(event);
         if (!event.defaultPrevented) context?.setOpen(!context.open);
      },
   });
};

export const MenuList = ({ children, className, ...props }: AnyProps) => {
   const context = useContext(MenuContext);
   if (!context?.open) return null;
   const alignRight = context.placement?.includes("end") ?? false;

   return (
      <div
         role="menu"
         {...props}
         className={join(
            "absolute top-[calc(100%+0.4rem)] z-[80] min-w-[140px] rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg",
            alignRight ? "right-0" : "left-0",
            className
         )}
      >
         {children}
      </div>
   );
};

export const MenuItem = ({
   children,
   className,
   value: _value,
   onClick,
   ...props
}: AnyProps) => {
   const context = useContext(MenuContext);
   return (
      <button
         type="button"
         role="menuitem"
         {...props}
         onClick={(event) => {
            onClick?.(event);
            context?.setOpen(false);
         }}
         className={join(
            "block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50",
            className
         )}
      >
         {children}
      </button>
   );
};

type TabsContextValue = { active: string | number; setActive: (value: string | number) => void };
const TabsContext = createContext<TabsContextValue | null>(null);

export const Tabs = ({ value, children, className }: AnyProps) => {
   const [active, setActive] = useState<string | number>(value);
   useEffect(() => setActive(value), [value]);
   return (
      <TabsContext.Provider value={{ active, setActive }}>
         <div className={className}>{children}</div>
      </TabsContext.Provider>
   );
};

export const TabsHeader = ({
   children,
   className,
   indicatorProps: _indicatorProps,
   ...props
}: AnyProps) => (
   <div role="tablist" {...props} className={join("flex items-center gap-1", className)}>
      {children}
   </div>
);

export const Tab = ({
   value,
   children,
   className,
   activeClassName,
   onClick,
   ...props
}: AnyProps) => {
   const context = useContext(TabsContext);
   const active = context?.active === value;
   return (
      <button
         type="button"
         role="tab"
         aria-selected={active}
         {...props}
         onClick={(event) => {
            context?.setActive(value);
            onClick?.(event);
         }}
         className={join(
            "relative flex-1 transition-colors",
            className,
            active && (activeClassName || "bg-white text-slate-900 shadow-sm")
         )}
      >
         {children}
      </button>
   );
};

export const TabsBody = ({ children, className, ...props }: AnyProps) => (
   <div {...props} className={className}>{children}</div>
);

export const TabPanel = ({ value, children, className, ...props }: AnyProps) => {
   const context = useContext(TabsContext);
   if (context?.active !== value) return null;
   return (
      <div role="tabpanel" {...props} className={className}>
         {children}
      </div>
   );
};

export const Checkbox = ({
   label,
   className,
   containerProps,
   labelProps,
   color: _color,
   ripple: _ripple,
   ...props
}: AnyProps) => (
   <label
      {...labelProps}
      className={join("inline-flex cursor-pointer items-center gap-2", labelProps?.className)}
   >
      <span {...containerProps} className={join("inline-flex", containerProps?.className)}>
         <input
            type="checkbox"
            {...props}
            className={join(
               "h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500",
               className
            )}
         />
      </span>
      {label != null ? <span>{label}</span> : null}
   </label>
);

export const Switch = ({
   label,
   className,
   containerProps,
   circleProps: _circleProps,
   labelProps,
   color: _color,
   ...props
}: AnyProps) => (
   <label
      {...labelProps}
      className={join("inline-flex cursor-pointer items-center gap-2", labelProps?.className)}
   >
      <span {...containerProps} className={join("relative inline-flex", containerProps?.className)}>
         <input
            type="checkbox"
            {...props}
            className={join("peer sr-only", className)}
         />
         <span className="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-blue-600" />
         <span className="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4" />
      </span>
      {label != null ? <span>{label}</span> : null}
   </label>
);

export const Progress = ({
   value = 0,
   className,
   color: _color,
   size: _size,
   ...props
}: AnyProps) => (
   <div
      {...props}
      className={join("h-2 w-full overflow-hidden rounded-full bg-slate-200", className)}
      role="progressbar"
      aria-valuenow={Number(value)}
      aria-valuemin={0}
      aria-valuemax={100}
   >
      <div
         className="h-full rounded-full bg-blue-600 transition-all"
         style={{ width: `${Math.min(100, Math.max(0, Number(value) || 0))}%` }}
      />
   </div>
);

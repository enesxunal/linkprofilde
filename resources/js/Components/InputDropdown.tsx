import { Fragment, useEffect, useState } from "react";
import ArrowDown from "@/Components/Icons/ArrowDown";
import { Listbox, Transition } from "@headlessui/react";
import { SelectListProps, SelectInputProps } from "@/types";

const InputDropdown = (props: SelectInputProps) => {
   const {
      name,
      label,
      error,
      required,
      flexLabel,
      fullWidth,
      defaultValue,
      itemList,
      dropdownListClass,
   } = props;

   const defaultSelect = itemList.find((item) => item.value === defaultValue);
   const [selected, setSelected] = useState<any>(
      defaultSelect || { key: "", value: "" }
   );

   useEffect(() => {
      props.onChange(selected);
   }, [selected]);

   useEffect(() => {
      const select = itemList.find((item) => item.value === defaultValue);
      if (select) setSelected(select);
   }, [defaultValue]);

   const dropdownActive = (item: SelectListProps) => {
      const active =
         item.value === selected.value
            ? "bg-slate-50 text-blue-700"
            : "text-slate-800";

      return `relative cursor-pointer select-none px-3 py-2 hover:bg-slate-50 hover:text-blue-700 ${active} ${dropdownListClass}`;
   };

   return (
      <div
         className={`flex flex-col items-start ${
            flexLabel && "md:flex-row md:items-center"
         } ${fullWidth && "w-full"}`}
      >
         {label && (
            <>
               {flexLabel ? (
                  <label className="mb-1.5 flex w-full max-w-[164px] items-center whitespace-nowrap text-sm font-medium text-slate-700">
                     <span className="mr-1">{label}</span>
                     {required && <span className="text-red-600">*</span>}
                  </label>
               ) : (
                  <label className="mb-1.5 flex w-full items-center whitespace-nowrap text-sm font-medium text-slate-700">
                     <span className="mr-1">{label}</span>
                     {required && <span className="text-red-600">*</span>}
                  </label>
               )}
            </>
         )}

         <Listbox name={name} value={selected.key} onChange={setSelected}>
            <div className={`relative ${fullWidth && "w-full"}`}>
               <Listbox.Button
                  className={`h-10 w-full rounded-lg border border-slate-200 px-3 text-left text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 ${props.className}`}
               >
                  <span className="block truncate">{selected.key}</span>
                  <span className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                     <ArrowDown className="h-3 w-3 text-slate-500" />
                  </span>
               </Listbox.Button>

               <Transition
                  as={Fragment}
                  enter="transition ease-out duration-200"
                  enterFrom="transform opacity-0 scale-95"
                  enterTo="transform opacity-100 scale-100"
                  leave="transition ease-in duration-75"
                  leaveFrom="transform opacity-100 scale-100"
                  leaveTo="transform opacity-0 scale-95"
               >
                  <Listbox.Options className="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-xl border border-slate-200 bg-white py-1 text-sm shadow-sm focus:outline-none">
                     {itemList.map((item, index) => {
                        return (
                           <Listbox.Option
                              key={index}
                              value={item}
                              className={dropdownActive(item)}
                           >
                              {item.key}
                           </Listbox.Option>
                        );
                     })}
                  </Listbox.Options>
               </Transition>
            </div>
         </Listbox>

         {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
      </div>
   );
};

export default InputDropdown;

interface Props {
   name: string;
   label?: string;
   value?: string;
   onChange?: (e: any) => void;
   className?: string;
}

const ColorInput = (props: Props) => {
   const { name, label, value, onChange, className } = props;

   return (
      <div className={`min-w-0 ${className ?? ""}`}>
         {label && (
            <label className="mb-1.5 flex w-full items-center text-sm font-medium text-slate-700">
               {label}
            </label>
         )}
         <div className="flex items-center gap-2">
            <input
               type="color"
               name={name}
               value={value}
               onChange={onChange}
               className="h-11 w-full min-w-0 cursor-pointer rounded-lg border border-slate-200 bg-white p-1 hover:border-slate-300"
            />
            <span
               className="inline-flex h-11 shrink-0 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 text-xs font-medium text-slate-600"
               title={value}
            >
               <span
                  className="h-4 w-4 shrink-0 rounded border border-slate-200"
                  style={{ backgroundColor: value }}
                  aria-hidden
               />
               <span className="max-w-[4.5rem] truncate uppercase">
                  {value}
               </span>
            </span>
         </div>
      </div>
   );
};

export default ColorInput;

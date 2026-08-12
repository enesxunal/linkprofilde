import { InputProps } from "@/types";
import { useEffect, useState } from "react";

const Input = (props: InputProps) => {
   const {
      type,
      name,
      value,
      label,
      error,
      maxLength,
      fullWidth,
      onChange,
      className,
      placeholder,
      required,
      flexLabel,
      disabled,
      readOnly,
      ...restProps
   } = props;

   const [lengthOver, setLengthOver] = useState(false);
   useEffect(() => {
      value && maxLength && value.length >= maxLength
         ? setLengthOver(true)
         : setLengthOver(false);
   }, [value]);

   return (
      <div
         className={`flex flex-col items-start ${
            flexLabel ? "md:flex-row md:items-center" : ""
         } ${fullWidth ? "w-full" : ""}`}
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

         <div className="relative w-full">
            {maxLength && (
               <small className="absolute -top-5 right-0 w-full text-end text-xs text-slate-500">
                  {value ? value.length : 0}/{maxLength}
               </small>
            )}

            <input
               {...restProps}
               type={type}
               name={name}
               value={value ? value : ""}
               className={`${
                  lengthOver
                     ? "border-red-500 focus:border-red-500 focus:ring-red-500"
                     : "border-slate-200 focus:border-blue-500 focus:ring-blue-500"
               } h-10 w-full rounded-lg border px-3 text-sm font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400 ${className} ${
                  fullWidth ? "w-full" : ""
               }`}
               placeholder={placeholder}
               onChange={onChange}
               required={required}
               maxLength={maxLength}
               disabled={disabled}
               readOnly={readOnly}
            />

            {lengthOver && (
               <p className="mt-1 text-sm text-red-600">
                  Maksimum uzunluk daha az veya eşit olmalıdır {maxLength}
               </p>
            )}
            {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
         </div>
      </div>
   );
};

export default Input;

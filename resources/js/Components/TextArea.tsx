import { TextAreaProps } from "@/types";
import { useEffect, useRef, useState } from "react";

const TextArea = (props: TextAreaProps) => {
   const {
      rows,
      cols,
      name,
      value,
      label,
      error,
      maxLength,
      onChange,
      fullWidth,
      placeholder,
      flexLabel,
      required,
   } = props;

   const [lengthOver, setLengthOver] = useState(false);
   useEffect(() => {
      maxLength && value && value.length >= maxLength
         ? setLengthOver(true)
         : setLengthOver(false);
   }, [value]);

   const textAreaRef = useRef<any>();
   useEffect(() => {
      if (maxLength && textAreaRef.current) {
         textAreaRef.current.maxLength = maxLength;
      }
   }, []);

   return (
      <div
         className={`relative flex flex-col items-start ${
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
         {maxLength && (
            <small className="absolute top-0 right-0 w-full text-end text-xs text-slate-500">
               {value ? value.length : 0}/{maxLength}
            </small>
         )}

         <textarea
            name={name}
            value={value}
            rows={rows || 3}
            cols={cols || 10}
            className={`w-full rounded-lg border px-3 py-2 text-sm font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 ${
               lengthOver
                  ? "border-red-500 focus:border-red-500 focus:ring-red-500"
                  : "border-slate-200 focus:border-blue-500 focus:ring-blue-500"
            } ${fullWidth && "w-full"}`}
            placeholder={placeholder}
            onChange={onChange}
            required={required}
            ref={textAreaRef}
         ></textarea>

         {lengthOver && (
            <p className="mt-1 text-sm text-red-600">
               Max length should be less or equal {maxLength}
            </p>
         )}
         {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
      </div>
   );
};

export default TextArea;

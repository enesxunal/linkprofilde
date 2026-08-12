import { useState } from "react";
import { error } from "@/utils/toast";

interface Props {
   name: string;
   handleChange: (target: any) => void;
   className?: string;
   value?: string;
}

const LogoUpload = (props: Props) => {
   const { name, className, handleChange, value } = props;
   const [fileName, setFileName] = useState<string>("");

   const retrievePathFile = (files: any) => {
      const file = files[0];
      if (!file) {
         return;
      }
      if (file.type !== "image/png" && file.type !== "image/jpeg") {
         error("Only png and jpg/jpeg allowed.");
      } else {
         const target: any = {};
         const reader = new FileReader();
         reader.readAsDataURL(file);
         reader.onloadend = () => {
            target.name = name;
            target.value = reader.result;
            target.logoName = file.name;
            setFileName(file.name);
            handleChange({ target });
         };
      }
   };

   return (
      <div className={`min-w-0 w-full ${className ?? ""}`}>
         <label className="flex w-full cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center transition hover:border-blue-300 hover:bg-blue-50/40">
            {value ? (
               <img
                  src={value}
                  alt={fileName || "Logo önizleme"}
                  className="h-16 w-16 rounded-lg object-contain"
               />
            ) : (
               <span className="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-slate-400 shadow-sm ring-1 ring-slate-200">
                  <svg
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     strokeWidth="1.5"
                     className="h-6 w-6"
                     aria-hidden
                  >
                     <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"
                     />
                  </svg>
               </span>
            )}
            <span className="space-y-0.5">
               <span className="block text-sm font-medium text-slate-800">
                  {value ? "Logoyu değiştir" : "Logo yükle"}
               </span>
               <span className="block text-xs text-slate-500">
                  {fileName
                     ? fileName
                     : "PNG veya JPG/JPEG — tıklayarak seçin"}
               </span>
            </span>
            <input
               type="file"
               name={name}
               accept="image/*"
               onChange={(e) => retrievePathFile(e.target.files)}
               className="sr-only"
            />
         </label>
      </div>
   );
};

export default LogoUpload;

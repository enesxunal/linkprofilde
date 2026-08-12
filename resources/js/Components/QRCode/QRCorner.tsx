interface Props {
   title: string;
   name: "0_outer" | "1_outer" | "2_outer" | "0_inner" | "1_inner" | "2_inner";
   onChange?: (e: any) => void;
   className?: string;
   state: { [key: string]: any };
}

const QRCorner = (props: Props) => {
   const { state, title, name, onChange, className } = props;

   const buildEyeRadiusInput = (inputName: string) => {
      return (
         <input
            min={0}
            max={50}
            type="range"
            name={inputName}
            value={state[inputName]}
            onChange={onChange}
            className="w-full accent-blue-600"
         />
      );
   };

   return (
      <div className={`min-w-0 ${className ?? ""}`}>
         <small className="mb-1.5 flex w-full items-center text-sm font-medium text-slate-700">
            {title}
         </small>
         <div className="space-y-1.5 rounded-lg border border-slate-200 bg-slate-50/60 p-2.5">
            {buildEyeRadiusInput(`eyeradius_${name}_0`)}
            {buildEyeRadiusInput(`eyeradius_${name}_1`)}
            {buildEyeRadiusInput(`eyeradius_${name}_2`)}
            {buildEyeRadiusInput(`eyeradius_${name}_3`)}
         </div>
      </div>
   );
};

export default QRCorner;

import { ChangeEvent } from "react";
import { Switch as InputSwitch } from "@material-tailwind/react";

interface SwitchProps {
   name: string;
   label?: string;
   switchId: string;
   onChange: (event: ChangeEvent<HTMLInputElement>) => void;
   labelClass?: string;
   defaultChecked?: boolean;
   required?: boolean;
   checked?: boolean;
}

const Switch = (props: SwitchProps) => {
   const {
      name,
      label,
      switchId,
      onChange,
      labelClass,
      defaultChecked,
      required,
      checked,
   } = props;

   return (
      <label className="group flex max-w-[300px] cursor-pointer items-center">
         <InputSwitch
            id={switchId}
            name={name}
            ripple={false}
            checked={checked}
            required={required}
            defaultChecked={defaultChecked}
            onChange={onChange}
            containerProps={{ className: "w-11 h-6" }}
            className="h-full w-full checked:bg-blue-600"
            circleProps={{
               className: "before:hidden after:hidden left-0.5 border-none",
            }}
         />
         {label && (
            <span
               className={`pl-3 text-sm font-medium text-slate-700 ${labelClass}`}
            >
               {label}
            </span>
         )}
      </label>
   );
};

export default Switch;

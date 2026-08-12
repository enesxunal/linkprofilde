import DoubleArrow from "@/Components/Icons/DoubleArrow";

interface Props {
   className?: string;
   centerHead?: boolean;
   justifyHead?: boolean;
   headerGroups: any[];
}

const TableHead = (props: Props) => {
   const { className, centerHead, justifyHead, headerGroups } = props;
   let headStyle = "text-start last:text-end";
   if (centerHead) {
      headStyle = "text-center";
   }
   if (justifyHead) {
      headStyle = "text-center first:text-start last:text-end";
   }

   const groups = headerGroups ?? [];
   return (
      <>
         {groups.map((headerGroup) => {
            const headers = headerGroup.headers ?? [];
            return (
               <tr {...headerGroup.getHeaderGroupProps()}>
                  {headers.map((column: any) => (
                     <th
                        {...column.getHeaderProps(
                           column.getSortByToggleProps()
                        )}
                        className={`bg-slate-50 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 sm:px-6 ${headStyle} ${className}`}
                     >
                        <span className="relative whitespace-nowrap pr-4">
                           {column.render("Header")}
                           <DoubleArrow className="absolute right-0 top-1/2 ml-1 h-3 w-3 -translate-y-1/2 text-slate-400" />
                        </span>
                     </th>
                  ))}
               </tr>
            );
         })}
      </>
   );
};

export default TableHead;

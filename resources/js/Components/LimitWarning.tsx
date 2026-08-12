import { Link } from "@inertiajs/react";
import AlertBanner from "@/Components/Panel/AlertBanner";

const LimitWarning = ({ limit }: { limit: boolean | string }) => {
   return (
      <>
         {limit && (
            <AlertBanner variant="warning" className="text-center">
               <p>
                  {limit}{" "}
                  <Link
                     href="/current-plan"
                     className="font-medium text-amber-900 underline"
                  >
                     Buraya Tıklayın
                  </Link>
               </p>
            </AlertBanner>
         )}
      </>
   );
};

export default LimitWarning;

import Countries from "./Countries";
import Referrers from "./Referrers";
import Devices from "./Devices";
import Operating from "./Operating";
import Browsers from "./Browsers";
import Languages from "./Languages";

interface Props {
   languages: any[];
   analytics: any[];
}

const Overview = ({ languages, analytics }: Props) => {
   return (
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
         <Countries analytics={analytics} overview />
         <Referrers analytics={analytics} overview />
         <Devices analytics={analytics} overview />
         <Operating analytics={analytics} overview />
         <Browsers analytics={analytics} overview />
         <Languages analytics={analytics} languages={languages} overview />
      </div>
   );
};

export default Overview;

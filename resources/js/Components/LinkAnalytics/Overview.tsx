import Countries from "./Countries";
import Referrers from "./Referrers";
import Devices from "./Devices";
import Operating from "./Operating";
import Browsers from "./Browsers";
import Languages from "./Languages";
import { BreakdownItem } from "./BreakdownList";

interface Props {
   countries: BreakdownItem[];
   referrers: BreakdownItem[];
   devices: BreakdownItem[];
   operating_systems: BreakdownItem[];
   browsers: BreakdownItem[];
   languages: BreakdownItem[];
}

const Overview = ({
   countries,
   referrers,
   devices,
   operating_systems,
   browsers,
   languages,
}: Props) => {
   return (
      <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
         <Countries items={countries} overview />
         <Referrers items={referrers} overview />
         <Devices items={devices} overview />
         <Operating items={operating_systems} overview />
         <Browsers items={browsers} overview />
         <Languages items={languages} overview />
      </div>
   );
};

export default Overview;

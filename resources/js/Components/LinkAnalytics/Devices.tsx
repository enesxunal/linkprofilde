import BreakdownList, { BreakdownItem } from "./BreakdownList";

interface Props {
   items: BreakdownItem[];
   overview?: boolean;
}

const Devices = ({ items, overview }: Props) => (
   <BreakdownList
      items={items}
      title="Cihazlar"
      emptyTitle="Cihaz verisi yok"
      emptyDescription="Seçili dönemde görüntülenecek cihaz kaydı bulunmuyor."
      limit={overview ? 5 : undefined}
   />
);

export default Devices;

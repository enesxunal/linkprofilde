import BreakdownList, { BreakdownItem } from "./BreakdownList";

interface Props {
   items: BreakdownItem[];
   overview?: boolean;
}

const Referrers = ({ items, overview }: Props) => (
   <BreakdownList
      items={items}
      title="Yönlendirenler"
      emptyTitle="Yönlendiren verisi yok"
      emptyDescription="Seçili dönemde görüntülenecek yönlendiren kaydı bulunmuyor."
      limit={overview ? 5 : undefined}
   />
);

export default Referrers;

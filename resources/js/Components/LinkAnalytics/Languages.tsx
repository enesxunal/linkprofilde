import BreakdownList, { BreakdownItem } from "./BreakdownList";

interface Props {
   items: BreakdownItem[];
   overview?: boolean;
}

const Languages = ({ items, overview }: Props) => (
   <BreakdownList
      items={items}
      title="Diller"
      emptyTitle="Dil verisi yok"
      emptyDescription="Seçili dönemde görüntülenecek dil kaydı bulunmuyor."
      limit={overview ? 5 : undefined}
   />
);

export default Languages;

import BreakdownList, { BreakdownItem } from "./BreakdownList";

interface Props {
   items: BreakdownItem[];
   overview?: boolean;
}

const Countries = ({ items, overview }: Props) => (
   <BreakdownList
      items={items}
      title="Ülkeler"
      emptyTitle="Ülke verisi yok"
      emptyDescription="Seçili dönemde görüntülenecek ülke kaydı bulunmuyor."
      limit={overview ? 5 : undefined}
   />
);

export default Countries;

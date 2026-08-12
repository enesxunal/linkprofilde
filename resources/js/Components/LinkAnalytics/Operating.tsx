import BreakdownList, { BreakdownItem } from "./BreakdownList";

interface Props {
   items: BreakdownItem[];
   overview?: boolean;
}

const Operating = ({ items, overview }: Props) => (
   <BreakdownList
      items={items}
      title="İşletim Sistemleri"
      emptyTitle="İşletim sistemi verisi yok"
      emptyDescription="Seçili dönemde görüntülenecek işletim sistemi kaydı bulunmuyor."
      limit={overview ? 5 : undefined}
   />
);

export default Operating;

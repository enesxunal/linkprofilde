import BreakdownList, { BreakdownItem } from "./BreakdownList";

interface Props {
   items: BreakdownItem[];
   overview?: boolean;
}

const Browsers = ({ items, overview }: Props) => (
   <BreakdownList
      items={items}
      title="Tarayıcılar"
      emptyTitle="Tarayıcı verisi yok"
      emptyDescription="Seçili dönemde görüntülenecek tarayıcı kaydı bulunmuyor."
      limit={overview ? 5 : undefined}
   />
);

export default Browsers;

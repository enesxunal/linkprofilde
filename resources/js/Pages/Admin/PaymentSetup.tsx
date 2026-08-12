import { ReactNode } from "react";
import { PaymentProps } from "@/types";
import { Head } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import ToslaSettings from "@/Components/Forms/ToslaSettings";
import PageHeader from "@/Components/Panel/PageHeader";

interface Props {
   tosla?: PaymentProps | null;
}

const defaultTosla: PaymentProps = {
   id: 0,
   active: false,
   key: "",
   secret: "",
   created_at: "",
   updated_at: "",
};

const PaymentSetup = (props: Props) => {
   const tosla = props.tosla ?? defaultTosla;
   return (
      <>
         <Head title="Ödeme Ayarları" />
         <PageHeader
            title="Ödeme Ayarları"
            description="Tosla ödeme entegrasyon bilgilerini yönetin."
         />
         <ToslaSettings tosla={tosla} />
      </>
   );
};

PaymentSetup.layout = (page: ReactNode) => <Dashboard children={page} />;

export default PaymentSetup;

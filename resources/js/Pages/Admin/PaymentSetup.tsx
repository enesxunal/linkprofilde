import { ReactNode } from "react";
import { PaymentProps } from "@/types";
import { Head } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import Breadcrumb from "@/Components/Breadcrumb";
import ToslaSettings from "@/Components/Forms/ToslaSettings";
import PaymentSettings from "@/Components/Icons/PaymentSettings";

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
         <Breadcrumb Icon={PaymentSettings} title="Ödeme Ayarları" />

         <ToslaSettings tosla={tosla} />
      </>
   );
};

PaymentSetup.layout = (page: ReactNode) => <Dashboard children={page} />;

export default PaymentSetup;

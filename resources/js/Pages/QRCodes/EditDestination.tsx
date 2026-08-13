import { ReactNode, FormEventHandler, useState } from "react";
import { Head, useForm } from "@inertiajs/react";
import Dashboard from "@/Layouts/Dashboard";
import Input from "@/Components/Input";
import InputDropdown from "@/Components/InputDropdown";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import AlertBanner from "@/Components/Panel/AlertBanner";
import Badge from "@/Components/Panel/Badge";
import { LinkProps, QRCodeProps } from "@/types";

type DestinationType = "external" | "biolink" | "shortlink";

interface Props {
   qrcode: QRCodeProps & {
      destination_type?: string | null;
      destination_url?: string | null;
      destination_link_id?: number | null;
      public_code?: string | null;
      is_dynamic?: boolean;
      is_active?: boolean;
      destination_link?: Pick<LinkProps, "id" | "link_name" | "url_name"> | null;
   };
   biolinks: Pick<LinkProps, "id" | "link_name" | "url_name" | "link_type">[];
   shortlinks: Pick<LinkProps, "id" | "link_name" | "url_name" | "link_type">[];
}

const EditDestination = ({ qrcode, biolinks = [], shortlinks = [] }: Props) => {
   const initialType = (qrcode.destination_type ||
      "external") as DestinationType;

   const { data, setData, patch, processing, errors } = useForm({
      destination_type: initialType,
      destination_url: qrcode.destination_url || "",
      destination_link_id: qrcode.destination_link_id || null,
      is_active: qrcode.is_active !== false,
   });

   const [destinationType, setDestinationType] =
      useState<DestinationType>(initialType);

   const destinationTypeOptions = [
      { key: "Harici URL", value: "external" },
      { key: "Bio Link", value: "biolink" },
      { key: "Kısa Link", value: "shortlink" },
   ];

   const biolinkOptions = biolinks.map((item) => ({
      key: item.link_name || item.url_name,
      value: item.id,
   }));

   const shortlinkOptions = shortlinks.map((item) => ({
      key: item.link_name || item.url_name,
      value: item.id,
   }));

   const currentSummary = (() => {
      if (qrcode.destination_type === "external") {
         return qrcode.destination_url || "—";
      }
      const link = qrcode.destination_link;
      if (link) {
         return link.link_name || link.url_name;
      }
      return "—";
   })();

   const submit: FormEventHandler = (e) => {
      e.preventDefault();
      patch(`/qrcodes/${qrcode.id}/destination`);
   };

   return (
      <>
         <Head title="QR Hedefini Düzenle" />
         <PageHeader
            title="QR Hedefini Düzenle"
            description="Basılı QR aynı kalır; yalnızca yönlendirme hedefi değişir."
            actions={
               <Badge variant={data.is_active ? "success" : "warning"}>
                  {data.is_active ? "Aktif" : "Pasif"}
               </Badge>
            }
         />

         <form onSubmit={submit} className="mx-auto max-w-2xl space-y-6">
            <AlertBanner variant="info">
               QR kodunuz değişmez. Hedefi değiştirdiğinizde mevcut basılı QR
               kodları yeni adrese yönlenir.
            </AlertBanner>

            <PanelCard title="QR Bilgisi">
               <div className="space-y-4">
                  <div>
                     <p className="text-xs font-medium uppercase tracking-wide text-slate-500">
                        QR adı
                     </p>
                     <p className="mt-1 text-sm text-slate-900">
                        {qrcode.name && String(qrcode.name).trim()
                           ? qrcode.name
                           : "—"}
                     </p>
                  </div>
                  <Input
                     fullWidth
                     readOnly
                     type="text"
                     name="public_code"
                     label="Public code"
                     value={qrcode.public_code || ""}
                  />
                  <Input
                     fullWidth
                     readOnly
                     type="text"
                     name="content"
                     label="Encoded QR URL"
                     value={qrcode.content || ""}
                  />
                  <div>
                     <p className="text-xs font-medium uppercase tracking-wide text-slate-500">
                        Mevcut hedef
                     </p>
                     <p className="mt-1 break-all text-sm text-slate-800">
                        {currentSummary}
                     </p>
                  </div>
               </div>
            </PanelCard>

            <PanelCard title="Yeni Hedef">
               <div className="space-y-4">
                  <InputDropdown
                     required
                     fullWidth
                     name="destination_type"
                     label="Hedef Türü"
                     error={errors.destination_type}
                     defaultValue={destinationType}
                     itemList={destinationTypeOptions}
                     onChange={(e: any) => {
                        const next = e.value as DestinationType;
                        setDestinationType(next);
                        setData((prev) => ({
                           ...prev,
                           destination_type: next,
                           destination_url:
                              next === "external" ? prev.destination_url : "",
                           destination_link_id:
                              next === "external"
                                 ? null
                                 : prev.destination_link_id,
                        }));
                     }}
                  />

                  {destinationType === "external" ? (
                     <Input
                        fullWidth
                        required
                        type="url"
                        name="destination_url"
                        label="Hedef URL"
                        value={data.destination_url}
                        onChange={(e) =>
                           setData("destination_url", e.target.value)
                        }
                        error={errors.destination_url}
                        placeholder="https://ornek.com/sayfa"
                     />
                  ) : null}

                  {destinationType === "biolink" ? (
                     <InputDropdown
                        required
                        fullWidth
                        name="destination_link_id"
                        label="Bio Link"
                        error={errors.destination_link_id}
                        defaultValue={data.destination_link_id}
                        itemList={biolinkOptions}
                        onChange={(e: any) =>
                           setData("destination_link_id", e.value)
                        }
                     />
                  ) : null}

                  {destinationType === "shortlink" ? (
                     <InputDropdown
                        required
                        fullWidth
                        name="destination_link_id"
                        label="Kısa Link"
                        error={errors.destination_link_id}
                        defaultValue={data.destination_link_id}
                        itemList={shortlinkOptions}
                        onChange={(e: any) =>
                           setData("destination_link_id", e.value)
                        }
                     />
                  ) : null}

                  <div className="flex items-center pt-1">
                     <input
                        id="is_active"
                        type="checkbox"
                        checked={!!data.is_active}
                        className="mr-2 h-3.5 w-3.5 rounded border-slate-300 text-blue-600 focus:outline-0 focus:ring-blue-500"
                        onChange={(e) => setData("is_active", e.target.checked)}
                     />
                     <label htmlFor="is_active" className="text-sm text-slate-700">
                        QR aktif (pasif olursa tarama 410 döner)
                     </label>
                  </div>

                  {errors.is_active ? (
                     <p className="text-sm text-red-600">{errors.is_active}</p>
                  ) : null}

                  <div className="flex flex-wrap gap-3 pt-2">
                     <button
                        type="submit"
                        disabled={processing}
                        className="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60"
                     >
                        {processing ? "Kaydediliyor..." : "Hedefi Kaydet"}
                     </button>
                     <a
                        href="/qrcodes"
                        className="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                     >
                        Vazgeç
                     </a>
                  </div>
               </div>
            </PanelCard>
         </form>
      </>
   );
};

EditDestination.layout = (page: ReactNode) => <Dashboard children={page} />;

export default EditDestination;

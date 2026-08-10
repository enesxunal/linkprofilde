export const bioLinksHead = [
   {
      Header: "Link Adı",
      accessor: "link_name",
      id: "name",
   },
   {
      Header: "Link Özelleştir",
      id: "customize",
   },
   {
      Header: "Link Görüntüle",
      id: "visit",
   },
   {
      Header: "Tüm Görüntülenmeler",
      accessor: "visited_count",
      id: "view",
   },
   {
      Header: "QR Kod",
      id: "qrcode",
   },
   {
      Header: "Link Kopyala",
      id: "copy",
   },
   {
      Header: "Düzenle",
      id: "action",
   },
];

export const shortLinksHead = [
   {
      Header: "Link URL",
      accessor: "link_url",
      id: "url",
   },
   {
      Header: "Link Adı",
      accessor: "link_name",
      id: "name",
   },
   {
      Header: "Tüm Görüntülenmeler",
      accessor: "visited",
      id: "view",
   },
   {
      Header: "QR Kod",
      id: "qrcode",
   },
   {
      Header: "Link Kopyala",
      id: "copy",
   },
   {
      Header: "Düzenle",
      id: "action",
   },
];

export const qrCodesHead = [
   {
      Header: "QR Kod",
      id: "qrcode",
   },
   {
      Header: "QR Adı",
      accessor: "name",
      id: "name",
   },
   {
      Header: "Proje Adı",
      id: "project",
   },
   {
      Header: "Link Adı",
      id: "link",
   },
   {
      Header: "Oluşturulma Tarihi",
      accessor: "created_at",
      id: "created",
   },
   {
      Header: "Düzenle",
      id: "action",
   },
];

export const projectsHead = [
   {
      Header: "Proje Adı",
      accessor: "project_name",
      id: "name",
   },
   {
      Header: "Tüm Qr Kodları",
      accessor: "qrcodes.length",
      id: "qrcodes",
   },
   {
      Header: "Qr Kodları Görüntüle",
      id: "view",
   },
   {
      Header: "Oluşturulma Tarihi",
      accessor: "created_at",
      id: "created",
   },
   {
      Header: "Düzenle",
      id: "action",
   },
];

export const usersHead = [
   {
      Header: "Fotoğraf",
      id: "photo",
   },
   {
      Header: "Adı",
      accessor: "name",
      id: "name",
   },
   {
      Header: "E-mail",
      accessor: "email",
      id: "email",
   },
   {
      Header: "Durum",
      accessor: "status",
      id: "status",
   },
   {
      Header: "Fiyatlandırma Planı",
      accessor: "pricing_plan.name",
      id: "created",
   },
   {
      Header: "Düzenle",
      id: "action",
   },
];

export const subscriptionsHead = [
   {
      Header: "Ödeme Methodu",
      accessor: "method",
      id: "method",
   },
   {
      Header: "Faturalandırma Türü",
      accessor: "billing",
      id: "billing",
   },
   {
      Header: "İşlem Kimliği",
      accessor: "transaction_id",
      id: "transaction_id",
   },
   {
      Header: "Toplam tutar",
      accessor: "total_price",
      id: "price",
   },
   {
      Header: "Ödenen Tarih",
      accessor: "created_at",
      id: "created",
   },
];

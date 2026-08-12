import InputDropdown from "../InputDropdown";
import { useState } from "react";

interface Props {
   buttonText: string;
   imageBlogData: () => any;
}

const QRCodeDownloader = (props: Props) => {
   const { buttonText, imageBlogData } = props;
   const [downloadType, setDownloadType] = useState("png");

   function qrcodeDownload(base64Data: string, format: string) {
      // Convert base64 to binary
      const binaryData = atob(base64Data.split(",")[1]);

      // Create Uint8Array from binary data
      const arrayBuffer = new ArrayBuffer(binaryData.length);
      const uint8Array = new Uint8Array(arrayBuffer);
      for (let i = 0; i < binaryData.length; i++) {
         uint8Array[i] = binaryData.charCodeAt(i);
      }

      // Create Blob from Uint8Array
      const blob = new Blob([uint8Array], { type: `image/${format}` });

      // Create URL for the Blob
      const url = URL.createObjectURL(blob);

      // Create a temporary anchor element to trigger the download
      const link = document.createElement("a");
      link.href = url;
      link.download = `qrcode.${format}`;
      link.click();

      // Clean up the URL object after download
      URL.revokeObjectURL(url);

      // Remove the anchor element after download
      setTimeout(() => {
         document.body.removeChild(link);
      }, 0);
   }

   return (
      <div className="flex min-w-0 flex-wrap items-stretch overflow-hidden rounded-lg border border-slate-300 bg-white">
         <button
            type="button"
            onClick={() => qrcodeDownload(imageBlogData(), downloadType)}
            className="inline-flex min-w-0 flex-1 items-center justify-center px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
         >
            {buttonText}
         </button>
         <div className="w-[5.5rem] shrink-0 border-l border-slate-300">
            <InputDropdown
               name="qr_download"
               defaultValue="png"
               itemList={[
                  { key: "PNG", value: "png" },
                  { key: "JPEG", value: "jpeg" },
               ]}
               onChange={(e: any) => setDownloadType(e.value)}
               className="!w-full rounded-none border-none bg-slate-50 text-sm font-medium text-slate-700"
            />
         </div>
      </div>
   );
};

export default QRCodeDownloader;

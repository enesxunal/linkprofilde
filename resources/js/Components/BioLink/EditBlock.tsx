import axios from "axios";
import Input from "../Input";
import { LinkItemProps } from "@/types";
import { error } from "@/utils/toast";
import TextArea from "../TextArea";
import EditPen from "../Icons/EditPen";
import { useForm } from "@inertiajs/react";
import InputDropdown from "../InputDropdown";
import { Button, Dialog } from "@material-tailwind/react";
import { ChangeEvent, FormEventHandler, useState } from "react";
import { vimeoUrl, spotifyUrl, youTubeUrl, soundCloudUrl } from "@/utils/utils";

interface Props {
   block: LinkItemProps;
   setLink: (state: any) => void;
}

const EditBlock = (props: Props) => {
   const { block, setLink } = props;
   const [open, setOpen] = useState(false);

   const handleOpen = () => {
      setOpen((prev) => !prev);
   };

   const [blockImage, setBlockImage] = useState(null);
   const { data, setData, reset } = useForm({
      link_id: block.link_id,
      item_type: block.item_type,
      item_sub_type: block.item_sub_type,
      item_title: block.item_title,
      item_link: block.item_link,
      item_icon: block.item_icon,
      content: block.content,
   });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const handleImageChange = (e: ChangeEvent<HTMLInputElement>): void => {
      const files = e.target.files;
      if (files && files[0]) {
         setBlockImage(files[0] as any);
      }
   };

   const submit: FormEventHandler = async (e) => {
      e.preventDefault();
      const formData: any = new FormData();
      formData.append("link_id", data.link_id);
      formData.append("item_type", data.item_type);
      formData.append("item_sub_type", data.item_sub_type);
      formData.append("item_title", data.item_title);
      formData.append("item_link", data.item_link);
      formData.append("item_icon", data.item_icon);
      formData.append("content", data.content);
      formData.append("image", blockImage);

      const res = await axios.post(
         `/bio-links/customize/block/edit/${block.id}`,
         formData
      );

      if (res.data.success) {
         setOpen(false);
         setLink(res.data.link);
      } else if (res.data.error) {
         error(res.data.error);
      }
   };

   return (
      <>
         <EditPen
            onClick={handleOpen}
            className="h-5 w-5 cursor-pointer text-blue-500"
         />

         <Dialog
            size="sm"
            open={open}
            handler={handleOpen}
            className="p-6 max-h-[calc(100vh-80px)] overflow-y-auto text-gray-800"
         >
            <div className="flex items-center justify-between mb-6">
               <p className="text-xl font-medium">
                  {block.item_title} Öğesini Düzenle
               </p>
               <span
                  onClick={handleOpen}
                  className="text-3xl leading-none cursor-pointer"
               >
                  ×
               </span>
            </div>

            <form onSubmit={submit}>
               {block.item_icon === "Link" ? (
                  <>
                     <div className="mb-4">
                        <Input
                           type="text"
                           name="item_title"
                           label="Link Başlığı"
                           value={data.item_title}
                           onChange={onHandleChange}
                           placeholder="Link başlığınız"
                           fullWidth
                           required
                        />
                     </div>
                     <div className="mb-4">
                        <Input
                           type="url"
                           name="item_link"
                           label="Link Adresi"
                           value={data.item_link as any}
                           onChange={onHandleChange}
                           placeholder="Link adresiniz"
                           fullWidth
                           required
                        />
                     </div>
                  </>
               ) : block.item_icon === "Heading" ? (
                  <>
                     <div className="mb-4 relative z-10">
                        <InputDropdown
                           fullWidth
                           required
                           name="item_sub_type"
                           label="Başlık Tipi"
                           defaultValue={data.item_sub_type as string}
                           itemList={[
                              { key: "H1", value: "h1" },
                              { key: "H2", value: "h2" },
                              { key: "H3", value: "h3" },
                              { key: "H4", value: "h4" },
                              { key: "H5", value: "h5" },
                              { key: "H6", value: "h6" },
                           ]}
                           onChange={(e) =>
                              setData("item_sub_type", e.value as any)
                           }
                        />
                     </div>
                     <div className="mb-4">
                        <Input
                           type="text"
                           name="item_title"
                           label="Başlık Metni"
                           value={data.item_title}
                           onChange={onHandleChange}
                           placeholder="Başlık metnini girin"
                           fullWidth
                           required
                        />
                     </div>
                  </>
               ) : block.item_icon === "Paragraph" ? (
                  <>
                     <div className="mb-4">
                        <Input
                           type="text"
                           label="Başlık"
                           name="item_title"
                           value={data.item_title as any}
                           onChange={(event: any) => {
                              setData((prev: any) => ({
                                 ...prev,
                                 item_sub_type: "paragraph",
                                 item_title: event.target.value,
                              }));
                           }}
                           placeholder="Paragraf başlığı"
                           fullWidth
                           required
                        />
                     </div>
                     <div className="mb-4">
                        <TextArea
                           rows={6}
                           cols={10}
                           name="content"
                           label="Açıklama"
                           value={data.content as any}
                           onChange={onHandleChange}
                           placeholder="Paragraf açıklaması"
                           fullWidth
                           required
                        />
                     </div>
                  </>
               ) : block.item_icon === "Image" ? (
                  <>
                     <div className="mb-4">
                        <label className="block text-sm mb-2 font-medium text-gray-500">
                           Select Image
                        </label>
                        <input
                           type="file"
                           onChange={handleImageChange}
                           className="!h-10 !p-0 outline-none focus:outline-none"
                        />
                     </div>
                     <div className="mb-4">
                        <Input
                           type="text"
                           name="item_title"
                           label="Görsel Alt Metni"
                           value={data.item_title}
                           onChange={onHandleChange}
                           placeholder="Görsel alt metnini girin"
                           fullWidth
                           required
                        />
                     </div>
                     <div className="mb-4">
                        <Input
                           type="text"
                           name="item_link"
                           value={data.item_link as any}
                           onChange={onHandleChange}
                           placeholder="Görsel link adresi"
                           label="Görsel Adresi (İsteğe bağlı)"
                           fullWidth
                        />
                     </div>
                  </>
               ) : block.item_icon === "SoundCloud" ? (
                  <div>
                     <p className="text-gray-500 text-sm mb-3">
                        Soundcloud adresini yapıştırın, profilinizde çalınabilir şarkı olarak gösterilecektir.
                     </p>
                     <div className="mb-4">
                        <Input
                           type="text"
                           name="item_title"
                           label="Video Başlığı"
                           value={data.item_title}
                           onChange={onHandleChange}
                           placeholder="Video link başlığı girin"
                           fullWidth
                           required
                        />
                     </div>
                     <Input
                        type="url"
                        name="item_link"
                        value={data.item_link as any}
                        onChange={(e: ChangeEvent<HTMLInputElement>) => {
                           const url = soundCloudUrl(e.target.value);
                           setData("item_link", url as any);
                        }}
                        placeholder="Video adresini girin"
                        label="SoundCloud Video Adresi"
                        fullWidth
                        required
                     />
                  </div>
               ) : block.item_icon === "YouTube" ? (
                  <div>
                     <p className="text-gray-500 text-sm mb-3">
                        YouTube video adresini yapıştırın, profilinizde video olarak gösterilecektir.
                     </p>
                     <div className="mb-4">
                        <Input
                           type="text"
                           name="item_title"
                           label="Video Başlığı"
                           value={data.item_title}
                           onChange={onHandleChange}
                           placeholder="Video link başlığı girin"
                           fullWidth
                           required
                        />
                     </div>
                     <Input
                        type="url"
                        name="item_link"
                        value={data.item_link as any}
                        onChange={(e: ChangeEvent<HTMLInputElement>) => {
                           const url = youTubeUrl(e.target.value);
                           setData("item_link", url as any);
                        }}
                        placeholder="Video adresini girin"
                        label="YouTube Video Adresi"
                        fullWidth
                        required
                     />
                  </div>
               ) : block.item_icon === "Spotify" ? (
                  <div>
                     <p className="text-gray-500 text-sm mb-3">
                        Spotify şarkı, albüm, program veya bölüm adresini yapıştırın; profilinizde oynatıcı olarak gösterilecektir.
                     </p>
                     <div className="mb-4">
                        <Input
                           type="text"
                           name="item_title"
                           label="Video Başlığı"
                           value={data.item_title}
                           onChange={onHandleChange}
                           placeholder="Video link başlığı girin"
                           fullWidth
                           required
                        />
                     </div>
                     <Input
                        type="url"
                        name="item_link"
                        value={data.item_link as any}
                        onChange={(e: ChangeEvent<HTMLInputElement>) => {
                           const url = spotifyUrl(e.target.value);
                           setData("item_link", url as any);
                        }}
                        placeholder="Video adresini girin"
                        label="Spotify Video Adresi"
                        fullWidth
                        required
                     />
                  </div>
               ) : block.item_icon === "Vimeo" ? (
                  <div>
                     <p className="text-gray-500 text-sm mb-3">
                        Vimeo adresini yapıştırın, profilinizde video olarak gösterilecektir.
                     </p>
                     <div className="mb-4">
                        <Input
                           type="text"
                           name="item_title"
                           label="Video Başlığı"
                           value={data.item_title}
                           onChange={onHandleChange}
                           placeholder="Video link başlığı girin"
                           fullWidth
                           required
                        />
                     </div>
                     <Input
                        type="url"
                        name="item_link"
                        value={data.item_link as any}
                        onChange={(e: ChangeEvent<HTMLInputElement>) => {
                           const url = vimeoUrl(e.target.value);
                           setData("item_link", url as any);
                        }}
                        placeholder="Video adresini girin"
                        label="Vimeo Video Adresi"
                        fullWidth
                        required
                     />
                  </div>
               ) : block.item_icon === "TikTok" ? (
                  <div>
                     <p className="text-gray-500 text-sm mb-3">
                        TikTok video adresini yapıştırın, profilinizde video olarak gösterilecektir.
                     </p>
                     <div className="mb-4">
                        <Input
                           type="text"
                           name="item_title"
                           label="Video Başlığı"
                           value={data.item_title}
                           onChange={onHandleChange}
                           placeholder="Video link başlığı girin"
                           fullWidth
                           required
                        />
                     </div>
                     <Input
                        type="url"
                        name="item_link"
                        value={data.item_link as any}
                        onChange={onHandleChange}
                        placeholder="Video adresini girin"
                        label="TikTok Video Adresi"
                        fullWidth
                        required
                     />
                  </div>
               ) : null}

               <div className="flex justify-end mt-4">
                  <Button
                     color="red"
                     variant="text"
                     onClick={handleOpen}
                     className="py-2 font-medium capitalize text-base mr-2"
                  >
                     <span>İptal</span>
                  </Button>
                  <Button
                     type="submit"
                     color="blue"
                     variant="gradient"
                     className="py-2 font-medium capitalize text-base"
                  >
                     <span>Değişiklikleri Kaydet</span>
                  </Button>
               </div>
            </form>
         </Dialog>
      </>
   );
};

export default EditBlock;

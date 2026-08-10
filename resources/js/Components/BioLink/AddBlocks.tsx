import { ChangeEvent, FormEventHandler, useState } from "react";
import { Button, Dialog } from "@material-tailwind/react";
import { LinkProps, SocialLinkProps } from "@/types";
import Input from "../Input";
import { useForm } from "@inertiajs/react";
import axios from "axios";
import { error } from "@/utils/toast";
import {
   getLink,
   soundCloudUrl,
   spotifyUrl,
   vimeoUrl,
   youTubeUrl,
} from "@/utils/utils";
import InputDropdown from "../InputDropdown";
import LeftArrow from "../Icons/LeftArrow";
import TextArea from "../TextArea";

interface Props {
   link: LinkProps;
   itemPosition: number;
   setLink: (state: any) => void;
}

const AddBlocks = (props: Props) => {
   const { link, setLink } = props;
   const [itemPosition, setItemPosition] = useState(props.itemPosition);

   const [open, setOpen] = useState({ parent: false, child: false });
   const [block, setBlock] = useState({ title: "", type: "", content: "" });

   const handleOpen = (newState: {}) => {
      setOpen((prev) => ({
         ...prev,
         ...newState,
      }));
   };

   const [blockImage, setBlockImage] = useState(null);
   const { data, setData, reset } = useForm({
      link_id: link.id,
      item_position: itemPosition + 1,
      item_type: "",
      item_sub_type: null,
      item_title: "",
      item_link: null,
      item_icon: "",
      content: null,
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
      formData.append("item_position", data.item_position);
      formData.append("item_type", data.item_type);
      formData.append("item_sub_type", data.item_sub_type);
      formData.append("item_title", data.item_title);
      formData.append("item_link", data.item_link);
      formData.append("item_icon", data.item_icon);
      formData.append("content", data.content);
      formData.append("image", blockImage);

      const res = await axios.post("/bio-links/customize/block/add", formData);

      if (res.data.success) {
         const { item, link } = res.data;
         setLink(link);
         setItemPosition(item.item_position);
         setOpen({ parent: false, child: false });
      } else if (res.data.error) {
         error(res.data.error);
      }
   };

   const blockItems = [
      { title: "Link", type: "linkItem", content: "Link" },
      { title: "Heading", type: "headingItem", content: "Text" },
      { title: "Paragraph", type: "paragraphItem", content: "Text" },
      { title: "Image", type: "imageItem", content: "Image" },
      { title: "SoundCloud", type: "soundCloudItem", content: "Embed" },
      { title: "YouTube", type: "youTubeItem", content: "Embed" },
      { title: "Spotify", type: "spotifyItem", content: "Embed" },
      { title: "Vimeo", type: "vimeoItem", content: "Embed" },
      { title: "TikTok", type: "tiktokItem", content: "Embed" },
   ];

   return (
      <>
         <Button
            variant="text"
            color="white"
            onClick={() => handleOpen({ parent: true })}
            className="py-2 px-3 md:px-4 rounded-md bg-blue-500 active:bg-blue-500 hover:bg-blue-500 font-medium text-base shadow-sm shadow-blue-500/20 active:opacity-[0.85] capitalize whitespace-nowrap"
         >
            Blok Ekle
         </Button>

         <Dialog
            size="sm"
            open={open.parent}
            handler={() => handleOpen({ parent: false })}
            className="p-6 max-h-[calc(100vh-80px)] overflow-y-auto text-gray-800"
         >
            <div className="flex items-center justify-between mb-6">
               <p className="text-xl font-medium">Blok Ekle</p>
               <span
                  onClick={() => handleOpen({ parent: false })}
                  className="text-3xl leading-none cursor-pointer"
               >
                  ×
               </span>
            </div>

            {blockItems.map((item) => (
               <Button
                  variant="text"
                  color="white"
                  key={item.type}
                  onClick={() => {
                     reset();
                     setBlock(item);
                     setBlockImage(null);
                     handleOpen({ parent: false, child: true });
                     setData((prev: any) => ({
                        ...prev,
                        item_icon: item.title,
                        item_type: item.content,
                     }));
                  }}
                  className="py-2.5 px-4 block w-full my-4 rounded-md bg-gray-100 active:bg-gray-100 hover:bg-gray-200/80 font-medium text-base shadow-sm shadow-gray-100/20 active:opacity-[0.85] capitalize text-gray-800"
               >
                  {item.title}
               </Button>
            ))}
         </Dialog>

         <Dialog
            size="sm"
            open={open.child}
            handler={() => handleOpen({ child: false })}
            className="p-6 max-h-[calc(100vh-80px)] overflow-y-auto text-gray-800"
         >
            <div className="flex items-center justify-between mb-6">
               <div className="flex items-center">
                  <div
                     onClick={() => {
                        reset();
                        setBlockImage(null);
                        handleOpen({ parent: true, child: false });
                     }}
                     className="flex items-center cursor-pointer w-10"
                  >
                     <LeftArrow className="w-6 h-6" />
                     <LeftArrow className="w-6 h-6 -ml-4" />
                  </div>
                  <p className="text-xl font-medium">Blok</p>
               </div>
               <span
                  onClick={() => handleOpen({ child: false })}
                  className="text-3xl leading-none cursor-pointer"
               >
                  ×
               </span>
            </div>

            <form onSubmit={submit}>
               {block.type === "linkItem" ? (
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
               ) : block.type === "headingItem" ? (
                  <>
                     <div className="mb-4 relative z-10">
                        <InputDropdown
                           fullWidth
                           required
                           name="item_sub_type"
                           label="Başlık Tipi"
                           defaultValue={"h1"}
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
               ) : block.type === "paragraphItem" ? (
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
               ) : block.type === "imageItem" ? (
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
               ) : block.type === "soundCloudItem" ? (
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
               ) : block.type === "youTubeItem" ? (
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
               ) : block.type === "spotifyItem" ? (
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
               ) : block.type === "vimeoItem" ? (
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
               ) : block.type === "tiktokItem" ? (
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
                     onClick={() => handleOpen({ child: false })}
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

export default AddBlocks;

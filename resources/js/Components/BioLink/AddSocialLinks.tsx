import { FormEventHandler, useState } from "react";
import { Dialog } from "@material-tailwind/react";
import { LinkProps } from "@/types";
import CirclePlus from "../Icons/CirclePlus";
import Input from "../Input";
import { useForm } from "@inertiajs/react";
import { socialType, socialsLinks } from "@/utils/data/socials-links";
import axios from "axios";
import { error } from "@/utils/toast";
import {
   getLink,
   isSafeHex,
   safeHttpUrl,
   safeMailtoHref,
   safeTelHref,
   whatsappHref,
} from "@/utils/utils";
import Email2 from "../Icons/Email2";
import Telephone from "../Icons/Telephone";
import Telegram from "../Icons/Telegram";
import Whatsapp from "../Icons/Whatsapp2";
import Facebook from "../Icons/Facebook";
import Messenger from "../Icons/Messenger";
import Instagram from "../Icons/Instagram";
import Twitter from "../Icons/Twitter";
import TikTok from "../Icons/TikTok";
import YouTube from "../Icons/YouTube";
import SoundCloud from "../Icons/SoundCloud";
import Linkedin from "../Icons/Linkedin";
import Spotify from "../Icons/Spotify";
import Pinterest from "../Icons/Pinterest";
import Snapchat from "../Icons/Snapchat";
import Discord from "../Icons/Discord";
import icons from "../Icons";
import ColorFillDrip from "../Icons/ColorFillDrip";

interface Props {
   link: LinkProps;
   setLink: (state: any) => void;
}

const AddSocialLinks = (props: Props) => {
   const { link, setLink } = props;

   const [open, setOpen] = useState(false);
   const handleOpen = () => setOpen(!open);

   const { data, setData } = useForm({
      email: getLink(link, "email"),
      telephone: getLink(link, "telephone"),
      telegram: getLink(link, "telegram"),
      whatsapp: getLink(link, "whatsapp"),
      facebook: getLink(link, "facebook"),
      messenger: getLink(link, "messenger"),
      instagram: getLink(link, "instagram"),
      twitter: getLink(link, "twitter"),
      tiktok: getLink(link, "tiktok"),
      youtube: getLink(link, "youtube"),
      soundcloud: getLink(link, "soundcloud"),
      linkedin: getLink(link, "linkedin"),
      spotify: getLink(link, "spotify"),
      pinterest: getLink(link, "pinterest"),
      snapchat: getLink(link, "snapchat"),
      discord: getLink(link, "discord"),
      social_color: "#101828",
   });

   const onHandleChange = (event: any) => {
      setData(event.target.name, event.target.value);
   };

   const submit: FormEventHandler = async (e) => {
      e.preventDefault();

      const socials: any[] = [];
      Object.entries(data).forEach(([key, value]) => {
         let social = socialsLinks.find((item) => item.name === key);
         if (social && value && value.length > 0) {
            social.link = value;
            socials.push(social);
         }
      });

      const res = await axios.put(
         `/bio-links/customize/add-socials/${link.id}`,
         {
            socials: JSON.stringify(socials),
            social_color: data.social_color,
         }
      );
      if (res.data.error) {
         error(res.data.error);
      } else {
         handleOpen();
         setLink(res.data);
      }
   };

   let socials: socialType[] = [];
   if (link.socials) {
      socials = JSON.parse(link.socials);
   }

   const socialColor = isSafeHex(link.social_color)
      ? link.social_color
      : "#101828";

   return (
      <div className="mb-0 flex flex-wrap items-center justify-center gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
         {socials.map((item, ind) => {
            const Icon = icons[item.icon];
            if (!Icon) return null;

            const href =
               item.name === "email"
                  ? safeMailtoHref(item.link)
                  : item.name === "telephone"
                  ? safeTelHref(item.link)
                  : item.name === "whatsapp"
                  ? whatsappHref(item.link)
                  : safeHttpUrl(item.link);

            if (!href) return null;

            const external =
               item.name !== "email" && item.name !== "telephone";

            return (
               <a
                  key={ind}
                  href={href}
                  className="mx-2"
                  {...(external
                     ? { target: "_blank", rel: "noopener noreferrer" }
                     : {})}
               >
                  <Icon className="h-7 w-7" style={{ color: socialColor }} />
               </a>
            );
         })}

         <CirclePlus
            onClick={handleOpen}
            className="mx-2 h-8 w-8 cursor-pointer text-blue-500"
         />

         <Dialog
            open={open}
            size="sm"
            handler={handleOpen}
            className="mx-4 max-h-[calc(100vh-80px)] overflow-y-auto rounded-xl border border-slate-200 bg-white p-6 text-slate-800 shadow-sm sm:mx-0"
         >
            <div className="mb-6 flex items-center justify-between">
               <p className="text-lg font-semibold text-slate-900">
                  Sosyal Bağlantılar
               </p>
               <button
                  type="button"
                  onClick={handleOpen}
                  className="text-2xl leading-none text-slate-400 hover:text-slate-700"
                  aria-label="Kapat"
               >
                  ×
               </button>
            </div>

            <form onSubmit={submit}>
               <p className="font-medium text-sm text-gray-700">
                  Sosyal İkon Rengi
               </p>
               <div className="relative mb-7">
                  <label
                     htmlFor="textColor"
                     className="absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2"
                  >
                     <ColorFillDrip className="w-6 h-6 text-blue-500" />
                  </label>
                  <input
                     type="color"
                     id="textColor"
                     value={data.social_color}
                     onChange={(e) => setData("social_color", e.target.value)}
                     className="h-[48px] w-full p-0"
                  />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="email"
                     className="pl-12"
                     value={data.email}
                     onChange={onHandleChange}
                     placeholder="example@gmail.com"
                     fullWidth
                  />
                  <Email2 className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="telephone"
                     className="pl-12"
                     value={data.telephone}
                     onChange={onHandleChange}
                     placeholder="+00000000000"
                     fullWidth
                  />
                  <Telephone className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="telegram"
                     className="pl-12"
                     value={data.telegram}
                     onChange={onHandleChange}
                     placeholder="https://t.me/username"
                     fullWidth
                  />
                  <Telegram className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="whatsapp"
                     className="pl-12"
                     value={data.whatsapp}
                     onChange={onHandleChange}
                     placeholder="+00000000000"
                     fullWidth
                  />
                  <Whatsapp className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="facebook"
                     className="pl-12"
                     value={data.facebook}
                     onChange={onHandleChange}
                     placeholder="https://facebook.com/username"
                     fullWidth
                  />
                  <Facebook className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="messenger"
                     className="pl-12"
                     value={data.messenger}
                     onChange={onHandleChange}
                     placeholder="https://m.me/"
                     fullWidth
                  />
                  <Messenger className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="instagram"
                     className="pl-12"
                     value={data.instagram}
                     onChange={onHandleChange}
                     placeholder="https://instagram.com/"
                     fullWidth
                  />
                  <Instagram className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="twitter"
                     className="pl-12"
                     value={data.twitter}
                     onChange={onHandleChange}
                     placeholder="https://twitter.com/"
                     fullWidth
                  />
                  <Twitter className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="tiktok"
                     className="pl-12"
                     value={data.tiktok}
                     onChange={onHandleChange}
                     placeholder="https://tiktok.com/@username"
                     fullWidth
                  />
                  <TikTok className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="youtube"
                     className="pl-12"
                     value={data.youtube}
                     onChange={onHandleChange}
                     placeholder="https://youtube.com/channel/channelid"
                     fullWidth
                  />
                  <YouTube className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="soundcloud"
                     className="pl-12"
                     value={data.soundcloud}
                     onChange={onHandleChange}
                     placeholder="https://soundcloud.com/username"
                     fullWidth
                  />
                  <SoundCloud className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="linkedin"
                     className="pl-12"
                     value={data.linkedin}
                     onChange={onHandleChange}
                     placeholder="https://linkedin.com/profle"
                     fullWidth
                  />
                  <Linkedin className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="spotify"
                     className="pl-12"
                     value={data.spotify}
                     onChange={onHandleChange}
                     placeholder="https://open.spotify.com/artist/username"
                     fullWidth
                  />
                  <Spotify className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="pinterest"
                     className="pl-12"
                     value={data.pinterest}
                     onChange={onHandleChange}
                     placeholder="https://pinterest.com/username"
                     fullWidth
                  />
                  <Pinterest className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="snapchat"
                     className="pl-12"
                     value={data.snapchat}
                     onChange={onHandleChange}
                     placeholder="https://snapchat.com/add/username"
                     fullWidth
                  />
                  <Snapchat className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mb-4 relative">
                  <Input
                     type="text"
                     name="discord"
                     className="pl-12"
                     value={data.discord}
                     onChange={onHandleChange}
                     placeholder="https://discord.gg/username"
                     fullWidth
                  />
                  <Discord className="absolute top-1/2 -translate-y-1/2 left-4 h-4 w-4" />
               </div>

               <div className="mt-4 flex flex-wrap justify-end gap-2">
                  <button
                     type="button"
                     onClick={handleOpen}
                     className="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                  >
                     İptal
                  </button>
                  <button
                     type="submit"
                     className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                  >
                     Kaydet
                  </button>
               </div>
            </form>
         </Dialog>
      </div>
   );
};

export default AddSocialLinks;

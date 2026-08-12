import Palette from "@/Components/Icons/Palette";
import ThemeUpdate from "@/Components/ThemeUpdate";
import Dashboard from "@/Layouts/Dashboard";
import { ThemeProps } from "@/types";
import { jsxStyle, stringToCss } from "@/utils/utils";
import { Head } from "@inertiajs/react";
import { ReactNode } from "react";
import PageHeader from "@/Components/Panel/PageHeader";
import Badge from "@/Components/Panel/Badge";
import EmptyState from "@/Components/Panel/EmptyState";
import PanelCard from "@/Components/Panel/PanelCard";

interface Props {
   themes: ThemeProps[];
}

const ManageThemes = ({ themes }: Props) => {
   return (
      <>
         <Head title="Tema Yönetimi" />
         <PageHeader
            title="Tema Yönetimi"
            description="Profil temalarını görüntüleyin ve türlerini güncelleyin."
         />

         {themes.length === 0 ? (
            <PanelCard>
               <EmptyState
                  icon={<Palette className="h-6 w-6" />}
                  title="Tema bulunamadı"
                  description="Henüz yönetilecek tema yok."
               />
            </PanelCard>
         ) : (
            <div className="grid grid-cols-1 gap-x-6 gap-y-8 md:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-6">
               {themes.map((theme, ind) => {
                  let bgStyle = jsxStyle(stringToCss(theme.background));
                  if (theme.bg_image) {
                     bgStyle.backgroundImage = `url(/${theme.bg_image})`;
                  }
                  let btnStyle = jsxStyle(stringToCss(theme.button_style));

                  return (
                     <div key={ind}>
                        <div
                           className="relative flex h-[220px] cursor-pointer flex-col justify-between rounded-xl border border-slate-200 p-4 py-8 transition-colors hover:border-blue-500 2xl:h-[260px] 2xl:py-12"
                           style={bgStyle}
                        >
                           <div className="absolute left-2 top-1">
                              <ThemeUpdate theme={theme} />
                           </div>
                           <div className="absolute right-2 top-2">
                              <Badge variant="info">{theme.type}</Badge>
                           </div>
                           {[1, 2, 3, 4].map((item) => (
                              <button
                                 key={item}
                                 className="h-[30px] w-full"
                                 style={btnStyle}
                              />
                           ))}
                        </div>
                        <p className="mt-2 mb-1 text-center text-sm font-medium text-slate-800">
                           {theme.name}
                        </p>
                     </div>
                  );
               })}
            </div>
         )}
      </>
   );
};

ManageThemes.layout = (page: ReactNode) => <Dashboard children={page} />;

export default ManageThemes;

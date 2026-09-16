import Dashboard from "@/Layouts/Dashboard";
import { Head, Link, router } from "@inertiajs/react";
import { ReactNode } from "react";
import PageHeader from "@/Components/Panel/PageHeader";
import PanelCard from "@/Components/Panel/PanelCard";
import EmptyState from "@/Components/Panel/EmptyState";
import Page from "@/Components/Icons/Page";
import Delete from "@/Components/Icons/Delete";
import EditPen from "@/Components/Icons/EditPen";

type BlogPost = {
   id: number;
   title: string;
   slug: string;
   excerpt: string;
   focus_keyword?: string | null;
   is_published: boolean;
   published_at?: string | null;
};

type PaginatedPosts = {
   data: BlogPost[];
};

const Show = ({ posts }: { posts: PaginatedPosts }) => {
   return (
      <>
         <Head title="Blog Yönetimi" />
         <PageHeader
            title="Blog Yönetimi"
            description="SEO ve yapay zekâ arama motorları için rehber içeriklerini yönetin."
            actions={
               <Link
                  href={route("admin.blog.create")}
                  className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
               >
                  Yeni Yazı
               </Link>
            }
         />

         {posts.data.length === 0 ? (
            <PanelCard>
               <EmptyState
                  icon={<Page className="h-6 w-6" />}
                  title="Blog yazısı yok"
                  description="İlk rehber içeriğini oluşturarak başlayın."
                  action={
                     <Link
                        href={route("admin.blog.create")}
                        className="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                     >
                        Yeni Yazı
                     </Link>
                  }
               />
            </PanelCard>
         ) : (
            <div className="grid grid-cols-1 gap-5 lg:grid-cols-2">
               {posts.data.map((post) => (
                  <PanelCard key={post.id}>
                     <div className="flex items-start justify-between gap-4">
                        <div className="min-w-0">
                           <div className="mb-2 flex flex-wrap items-center gap-2">
                              <span className={`rounded-full px-2.5 py-1 text-xs font-semibold ${post.is_published ? "bg-emerald-50 text-emerald-700" : "bg-slate-100 text-slate-600"}`}>
                                 {post.is_published ? "Yayında" : "Taslak"}
                              </span>
                              {post.focus_keyword && (
                                 <span className="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                    {post.focus_keyword}
                                 </span>
                              )}
                           </div>
                           <h2 className="text-lg font-semibold text-slate-900">{post.title}</h2>
                           <p className="mt-1 text-sm text-slate-500">/blog/{post.slug}</p>
                           <p className="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{post.excerpt}</p>
                        </div>
                        <div className="flex shrink-0 gap-2">
                           <Link
                              href={route("admin.blog.edit", post.id)}
                              className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100"
                              aria-label="Düzenle"
                           >
                              <EditPen className="h-4 w-4" />
                           </Link>
                           <button
                              type="button"
                              onClick={() => {
                                 if (window.confirm("Bu blog yazısını silmek istiyor musunuz?")) {
                                    router.delete(route("admin.blog.delete", post.id));
                                 }
                              }}
                              className="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100"
                              aria-label="Sil"
                           >
                              <Delete className="h-4 w-4" />
                           </button>
                        </div>
                     </div>
                  </PanelCard>
               ))}
            </div>
         )}
      </>
   );
};

Show.layout = (page: ReactNode) => <Dashboard children={page} />;
export default Show;

import Dashboard from "@/Layouts/Dashboard";
import Input from "@/Components/Input";
import NativeRichTextEditor from "@/Components/NativeRichTextEditor";
import PageHeader from "@/Components/Panel/PageHeader";
import { Head, useForm } from "@inertiajs/react";
import { ReactNode } from "react";

type BlogPost = {
   id: number;
   title: string;
   slug: string;
   excerpt: string;
   content: string;
   meta_title?: string | null;
   meta_description?: string | null;
   focus_keyword?: string | null;
   author_name?: string | null;
   is_published: boolean;
   published_at?: string | null;
};

const Edit = ({ post }: { post: BlogPost | null }) => {
   const editing = Boolean(post?.id);
   const { data, setData, post: createPost, put, errors, processing } = useForm({
      title: post?.title ?? "",
      slug: post?.slug ?? "",
      excerpt: post?.excerpt ?? "",
      content: post?.content ?? "",
      meta_title: post?.meta_title ?? "",
      meta_description: post?.meta_description ?? "",
      focus_keyword: post?.focus_keyword ?? "",
      author_name: post?.author_name ?? "LinkProfilde Editör Ekibi",
      is_published: post?.is_published ?? false,
      published_at: post?.published_at ? post.published_at.slice(0, 16) : "",
   });

   const submit = (e: React.FormEvent<HTMLFormElement>) => {
      e.preventDefault();
      if (editing && post) {
         put(route("admin.blog.update", post.id));
      } else {
         createPost(route("admin.blog.store"));
      }
   };

   return (
      <>
         <Head title={editing ? "Blog Yazısını Düzenle" : "Yeni Blog Yazısı"} />
         <PageHeader
            title={editing ? "Blog Yazısını Düzenle" : "Yeni Blog Yazısı"}
            description="Arama niyetini net cevaplayan, kaynak gösterilebilir ve kolay taranabilir içerikler oluşturun."
         />

         <form onSubmit={submit} className="mx-auto max-w-[1200px] space-y-6">
            <div className="card p-5 sm:p-6">
               <div className="grid grid-cols-1 gap-5 lg:grid-cols-2">
                  <Input type="text" label="Başlık" name="title" value={data.title} error={errors.title} onChange={(e) => setData("title", e.target.value)} fullWidth required />
                  <Input type="text" label="URL Yolu" name="slug" value={data.slug} error={errors.slug} onChange={(e) => setData("slug", e.target.value.toLowerCase().replace(/\s+/g, "-").replace(/[^a-z0-9-]/g, ""))} fullWidth placeholder="instagram-bio-link-olusturma" />
                  <Input type="text" label="Odak Anahtar Kelime" name="focus_keyword" value={data.focus_keyword} error={errors.focus_keyword} onChange={(e) => setData("focus_keyword", e.target.value)} fullWidth placeholder="ücretsiz bio link oluşturma" />
                  <Input type="text" label="Yazar" name="author_name" value={data.author_name} error={errors.author_name} onChange={(e) => setData("author_name", e.target.value)} fullWidth />
               </div>

               <div className="mt-5">
                  <label className="mb-2 block text-sm font-medium text-slate-700">Özet</label>
                  <textarea value={data.excerpt} onChange={(e) => setData("excerpt", e.target.value)} rows={4} className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                  {errors.excerpt && <p className="mt-1 text-sm text-red-600">{errors.excerpt}</p>}
               </div>
            </div>

            <div className="card p-5 sm:p-6">
               <label className="mb-2 block text-sm font-medium text-slate-700">Yazı İçeriği</label>
               <NativeRichTextEditor value={data.content} onChange={(html) => setData("content", html)} error={errors.content} />
            </div>

            <div className="card p-5 sm:p-6">
               <h2 className="text-lg font-semibold text-slate-900">SEO / GEO</h2>
               <p className="mt-1 text-sm text-slate-500">Meta alanları Google sonuçları ve yapay zekâ tabanlı arama sistemlerinin sayfayı daha net anlamasına yardımcı olur.</p>
               <div className="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                  <Input type="text" label="Meta Başlık" name="meta_title" value={data.meta_title} error={errors.meta_title} onChange={(e) => setData("meta_title", e.target.value)} fullWidth />
                  <Input label="Yayın Tarihi" type="datetime-local" name="published_at" value={data.published_at} error={errors.published_at} onChange={(e) => setData("published_at", e.target.value)} fullWidth />
               </div>
               <div className="mt-5">
                  <label className="mb-2 block text-sm font-medium text-slate-700">Meta Açıklama</label>
                  <textarea value={data.meta_description} onChange={(e) => setData("meta_description", e.target.value)} rows={3} maxLength={320} className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" />
                  {errors.meta_description && <p className="mt-1 text-sm text-red-600">{errors.meta_description}</p>}
               </div>
               <label className="mt-5 inline-flex items-center gap-3 text-sm font-medium text-slate-700">
                  <input type="checkbox" checked={data.is_published} onChange={(e) => setData("is_published", e.target.checked)} className="h-4 w-4 rounded border-slate-300 text-blue-600" />
                  Yayında
               </label>
            </div>

            <div className="flex justify-end">
               <button type="submit" disabled={processing} className="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                  {processing ? "Kaydediliyor…" : editing ? "Değişiklikleri Kaydet" : "Yazıyı Oluştur"}
               </button>
            </div>
         </form>
      </>
   );
};

Edit.layout = (page: ReactNode) => <Dashboard children={page} />;
export default Edit;

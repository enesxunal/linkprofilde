import { useEffect, useRef } from "react";

interface Props {
   value: string;
   onChange: (html: string) => void;
   error?: string;
}

const ToolbarButton = ({
   label,
   title,
   onClick,
}: {
   label: string;
   title: string;
   onClick: () => void;
}) => (
   <button
      type="button"
      title={title}
      aria-label={title}
      onMouseDown={(event) => event.preventDefault()}
      onClick={onClick}
      className="inline-flex h-8 min-w-8 items-center justify-center rounded-md border border-slate-200 bg-white px-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
   >
      {label}
   </button>
);

const NativeRichTextEditor = ({ value, onChange, error }: Props) => {
   const editorRef = useRef<HTMLDivElement>(null);

   useEffect(() => {
      const editor = editorRef.current;
      if (!editor || document.activeElement === editor) return;
      if (editor.innerHTML !== value) {
         editor.innerHTML = value || "";
      }
   }, [value]);

   const sync = () => {
      onChange(editorRef.current?.innerHTML || "");
   };

   const command = (name: string, commandValue?: string) => {
      editorRef.current?.focus();
      document.execCommand(name, false, commandValue);
      sync();
   };

   const formatBlock = (tag: "p" | "h2" | "h3" | "blockquote" | "pre") => {
      command("formatBlock", tag);
   };

   const addLink = () => {
      const selected = window.getSelection()?.toString().trim();
      if (!selected) {
         window.alert("Önce bağlantı verilecek metni seçin.");
         return;
      }

      const raw = window.prompt("Bağlantı adresi (https:// veya mailto:)");
      if (!raw) return;

      const href = raw.trim();
      if (!/^(https?:\/\/|mailto:)/i.test(href)) {
         window.alert("Yalnızca http, https veya mailto bağlantıları kullanılabilir.");
         return;
      }

      command("createLink", href);
   };

   const handlePaste = (event: React.ClipboardEvent<HTMLDivElement>) => {
      event.preventDefault();
      const text = event.clipboardData.getData("text/plain");
      document.execCommand("insertText", false, text);
      sync();
   };

   return (
      <div>
         <div className="flex flex-wrap gap-1.5 rounded-t-xl border border-b-0 border-slate-200 bg-slate-50 p-2.5">
            <ToolbarButton label="P" title="Normal metin" onClick={() => formatBlock("p")} />
            <ToolbarButton label="H2" title="Başlık 2" onClick={() => formatBlock("h2")} />
            <ToolbarButton label="H3" title="Başlık 3" onClick={() => formatBlock("h3")} />
            <span className="mx-1 h-8 w-px bg-slate-200" aria-hidden="true" />
            <ToolbarButton label="B" title="Kalın" onClick={() => command("bold")} />
            <ToolbarButton label="I" title="İtalik" onClick={() => command("italic")} />
            <ToolbarButton label="U" title="Altı çizili" onClick={() => command("underline")} />
            <ToolbarButton label="S" title="Üstü çizili" onClick={() => command("strikeThrough")} />
            <span className="mx-1 h-8 w-px bg-slate-200" aria-hidden="true" />
            <ToolbarButton label="• Liste" title="Madde işaretli liste" onClick={() => command("insertUnorderedList")} />
            <ToolbarButton label="1. Liste" title="Numaralı liste" onClick={() => command("insertOrderedList")} />
            <ToolbarButton label="❝" title="Alıntı" onClick={() => formatBlock("blockquote")} />
            <ToolbarButton label="</>" title="Kod bloğu" onClick={() => formatBlock("pre")} />
            <span className="mx-1 h-8 w-px bg-slate-200" aria-hidden="true" />
            <ToolbarButton label="Bağlantı" title="Bağlantı ekle" onClick={addLink} />
            <ToolbarButton label="Bağı kaldır" title="Bağlantıyı kaldır" onClick={() => command("unlink")} />
            <ToolbarButton label="Temizle" title="Biçimlendirmeyi temizle" onClick={() => command("removeFormat")} />
         </div>

         <div
            ref={editorRef}
            contentEditable
            suppressContentEditableWarning
            role="textbox"
            aria-multiline="true"
            aria-label="Sayfa içeriği"
            onInput={sync}
            onBlur={sync}
            onPaste={handlePaste}
            className={`min-h-[340px] rounded-b-xl border bg-white p-4 text-sm leading-7 text-slate-800 outline-none focus:ring-1 ${
               error
                  ? "border-red-400 focus:border-red-500 focus:ring-red-500"
                  : "border-slate-200 focus:border-blue-500 focus:ring-blue-500"
            }`}
         />

         <div className="mt-2 flex items-start justify-between gap-4">
            <p className="text-xs text-slate-500">
               Güvenlik için yapıştırılan içerik düz metin olarak alınır. Script, iframe ve tehlikeli HTML sunucuda ayrıca temizlenir.
            </p>
            <span className="shrink-0 text-xs text-slate-400">
               {value.replace(/<[^>]*>/g, "").trim().length} karakter
            </span>
         </div>
         {error ? <p className="mt-2 text-sm font-medium text-red-600">{error}</p> : null}
      </div>
   );
};

export default NativeRichTextEditor;

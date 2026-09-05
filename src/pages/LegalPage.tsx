import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import { LEGAL_DOCS } from "@/lib/routes";

type LegalPageProps = {
  doc: keyof typeof LEGAL_DOCS;
};

export default function LegalPage({ doc }: LegalPageProps) {
  const { title, src } = LEGAL_DOCS[doc];

  return (
    <div className="min-h-screen pt-24 pb-20">
      <div className="site-container">
        <Breadcrumbs items={[homeCrumb(), { name: title }]} />
        <h1 className="text-section text-[#1A1A2E] mb-6">{title}</h1>
        <div className="glass-strong rounded-[28px] overflow-hidden">
          <iframe src={src} title={title} className="w-full min-h-[80vh] border-0" />
        </div>
      </div>
    </div>
  );
}

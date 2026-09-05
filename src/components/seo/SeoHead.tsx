import { Helmet } from "react-helmet-async";
import type { SeoData, SeoLink, SeoMetaTag } from "@/lib/seo";

type Props = {
  data: SeoData;
};

function renderMeta(tag: SeoMetaTag, index: number) {
  if (tag.name) {
    return <meta key={`n-${index}`} name={tag.name} content={tag.content} />;
  }
  if (tag.property) {
    return <meta key={`p-${index}`} property={tag.property} content={tag.content} />;
  }
  if (tag.itemprop) {
    return <meta key={`i-${index}`} itemProp={tag.itemprop} content={tag.content} />;
  }
  if (tag.httpEquiv) {
    return <meta key={`h-${index}`} httpEquiv={tag.httpEquiv} content={tag.content} />;
  }
  return null;
}

function renderLink(link: SeoLink, index: number) {
  return (
    <link
      key={`l-${index}`}
      rel={link.rel}
      href={link.href}
      {...(link.type ? { type: link.type } : {})}
      {...(link.title ? { title: link.title } : {})}
    />
  );
}

export default function SeoHead({ data }: Props) {
  return (
    <Helmet prioritizeSeoTags>
      <title>{data.title}</title>
      {data.meta.map(renderMeta)}
      {data.links.filter((l) => l.href).map(renderLink)}
      {data.jsonLd.map((schema, index) => (
        <script key={`ld-${index}`} type="application/ld+json">
          {JSON.stringify(schema)}
        </script>
      ))}
    </Helmet>
  );
}

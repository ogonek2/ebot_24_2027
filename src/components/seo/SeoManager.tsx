import { useEffect, useState } from "react";
import { useLocation } from "react-router-dom";
import SeoHead from "./SeoHead";
import { FALLBACK_SEO, fetchSeo, type SeoData } from "@/lib/seo";

export default function SeoManager() {
  const { pathname, search } = useLocation();
  const [seo, setSeo] = useState<SeoData>(FALLBACK_SEO);

  useEffect(() => {
    let cancelled = false;

    fetchSeo(pathname || "/", search)
      .then((data) => {
        if (!cancelled) setSeo(data);
      })
      .catch(() => {
        if (!cancelled) setSeo(FALLBACK_SEO);
      });

    return () => {
      cancelled = true;
    };
  }, [pathname, search]);

  return <SeoHead data={seo} />;
}

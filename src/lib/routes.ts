export const ROUTES = {
  home: "/",
  services: "/poslugi-ta-cini",
  b2b: "/dlya-biznesu",
  courier: "/viklikati-kuryera",
  cart: "/koshyk",
  checkout: "/oformlennya-zamovlennya",
  delivery: "/dostavka",
  locations: "/lokatsii",
  promotions: "/aktsii",
  contacts: "/kontakty",
  blog: "/blog",
  oferta: "/oferta",
  privacy: "/privacy-policy",
  umovy: "/umovy",
  orderSuccess: "/order-success",
} as const;

export function serviceUrl(categoryHref: string, serviceHref: string) {
  return `/poslugi-ta-cini/${categoryHref}/posluga/${serviceHref}`;
}

export function categoryUrl(categoryHref: string) {
  return `/poslugi-ta-cini/${categoryHref}`;
}

export function b2bUrl(href: string) {
  return `/dlya-biznesu/${href}`;
}

export function promotionUrl(id: number) {
  return `/aktsii/${id}`;
}

export function blogPostUrl(slug: string) {
  return `/blog/${slug}`;
}

export const LEGAL_DOCS = {
  oferta: {
    title: "Публічна оферта",
    src: "https://docs.google.com/document/d/1PXa7_SgRVjitoEGtNi0vIa9fQ7Q_Jy0S/edit?usp=sharing&ouid=116808565778515480883&rtpof=true&sd=true",
  },
  privacy: {
    title: "Політика конфіденційності",
    src: "https://docs.google.com/document/d/1mx5IMvA-TNEBwmj_PwF-WL_ZSyfqC2nu/edit?usp=sharing&ouid=116808565778515480883&rtpof=true&sd=true",
  },
  umovy: {
    title: "Умови використання",
    src: "https://docs.google.com/document/d/14MAuekt3zwNflgD7yWtg7kQ5wpggeBFuYfKxrzdWojI/edit?usp=sharing",
  },
} as const;

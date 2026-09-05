export type SpaCatalogItem = {
  id: number;
  name: string;
  href: string;
  categoryHref: string;
  price: string;
  priceBatch?: string;
  individualPrice?: string | null;
  oldPrice?: string | null;
  promo?: boolean;
  marker?: string | null;
};

export type SpaCatalogCategory = {
  id: string;
  title: string;
  parentId?: string | null;
  parentTitle?: string | null;
  iconUrl?: string | null;
  serviceCount?: number;
  items: SpaCatalogItem[];
};

export type SpaDiscount = {
  id: number;
  name: string;
  discountAction?: string | null;
  locations?: string | null;
  banner?: string | null;
  color?: string | null;
  textColor?: string | null;
  discountColor?: string | null;
  url: string;
};

export type SpaBranch = {
  id: number;
  city: string;
  address: string;
  workingHours: string;
  image?: string | null;
  linkMap?: string | null;
  lat?: number | null;
  lng?: number | null;
  value?: string | null;
  seoLink?: string | null;
};

export type SpaLocationPoint = {
  id: number;
  street: string;
  value?: string | null;
  workingHours?: string | null;
  linkMap?: string | null;
  lat?: number | null;
  lng?: number | null;
  image?: string | null;
  seoLink?: string | null;
};

export type SpaLocationCity = {
  id: number;
  name: string;
  locations: SpaLocationPoint[];
};

export type SpaBlogPost = {
  slug: string;
  title: string;
  publishedAt?: string | null;
  image?: string | null;
  url: string;
  excerpt?: string | null;
};

export type SpaCtaHeader = {
  id?: number;
  title: string;
  subtitle?: string | null;
  url: string;
  iconUrl?: string | null;
};

export type SpaServiceDetail = SpaCatalogItem & {
  description?: string | null;
  content?: string | null;
  excerpt?: string | null;
  image?: string | null;
  categoryTitle?: string | null;
  categoryHref?: string | null;
  parentCategoryTitle?: string | null;
  parentCategoryHref?: string | null;
  url?: string | null;
  faq?: Array<{ question?: string; answer?: string }>;
};

export type SpaBootstrap = {
  route?: string;
  categories?: SpaCatalogCategory[];
  discounts?: SpaDiscount[];
  branches?: SpaBranch[];
  locationCities?: SpaLocationCity[];
  blogPosts?: SpaBlogPost[];
  ctaHeaders?: SpaCtaHeader[];
  assets?: {
    logo?: string;
    logoFull?: string;
    linesPattern?: string;
    storageBase?: string;
  };
  activeCategory?: string;
  categoryHref?: string;
  serviceHref?: string;
};

export const emptyBootstrap: SpaBootstrap = {
  categories: [],
  discounts: [],
  branches: [],
  blogPosts: [],
  ctaHeaders: [],
  assets: {},
};

declare global {
  interface Window {
    __ENOT__?: SpaBootstrap;
  }
}

export function getBootstrap(): SpaBootstrap {
  return window.__ENOT__ ?? {};
}

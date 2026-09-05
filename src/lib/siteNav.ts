import { ROUTES } from "./routes";

export const footerNavigationLinks = [
  { label: "Послуги та ціни", href: ROUTES.services },
  { label: "B2B", href: ROUTES.b2b },
  { label: "Акції", href: ROUTES.promotions },
  { label: "Блог", href: ROUTES.blog },
  { label: "Локації", href: ROUTES.locations },
  { label: "Доставка та кур'єр", href: ROUTES.delivery },
  { label: "Контакти", href: ROUTES.contacts },
] as const;

export const companyLinks = [
  { label: "B2B рішення", href: ROUTES.b2b },
  { label: "Локації", href: ROUTES.locations },
  { label: "Акції", href: ROUTES.promotions },
  { label: "Блог", href: ROUTES.blog },
  { label: "Контакти", href: ROUTES.contacts },
] as const;

export const serviceLinks = [
  { label: "Послуги та ціни", href: ROUTES.services },
  { label: "Кошик", href: ROUTES.cart },
  { label: "Викликати кур'єра", href: ROUTES.courier },
  { label: "Доставка", href: ROUTES.delivery },
] as const;

export const legalLinks = [
  { label: "Політика конфіденційності", href: ROUTES.privacy },
  { label: "Публічна оферта", href: ROUTES.oferta },
  { label: "Умови використання", href: ROUTES.umovy },
] as const;

export const headerLinks = [
  { label: "B2B", href: ROUTES.b2b },
  { label: "Акції", href: ROUTES.promotions },
  { label: "Доставка", href: ROUTES.delivery },
  { label: "Локації", href: ROUTES.locations },
  { label: "Блог", href: ROUTES.blog },
  { label: "Контакти", href: ROUTES.contacts },
] as const;

export function isInternalHref(href: string): boolean {
  return href.startsWith("/") && !href.startsWith("//");
}

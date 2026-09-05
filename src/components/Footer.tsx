import { Link } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faCalendar,
  faClock,
  faEnvelope,
  faHeadset,
  faPhone,
} from "@fortawesome/free-solid-svg-icons";
import { faInstagram, faTelegram, faViber } from "@fortawesome/free-brands-svg-icons";
import BrandMark from "./BrandMark";
import { useBootstrap } from "@/context/BootstrapContext";
import { categoryUrl, ROUTES } from "@/lib/routes";
import { topLevelCategories } from "@/lib/categories";
import { footerNavigationLinks, legalLinks } from "@/lib/siteNav";

const linkClass = "text-white/45 hover:text-white text-[13px] no-underline transition-colors";

const socialClass =
  "w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center hover:bg-[#f97171]/40 text-white no-underline transition-colors";

export default function Footer() {
  const { categories = [] } = useBootstrap();
  const footerCategories = topLevelCategories(categories)
    .filter((cat) => (cat.serviceCount ?? 0) > 0)
    .slice(0, 4);

  return (
    <footer className="pb-6 site-container px-3 sm:px-4" id="contacts">
      <div className="glass-dark rounded-[32px] px-6 sm:px-10 py-12 sm:py-14">
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
          <div className="col-span-2 lg:col-span-1">
            <div className="mb-4">
              <BrandMark size={36} variant="dark" />
            </div>
            <p className="text-white/50 text-[13px] leading-relaxed mb-5">
              Професійна хімчистка одягу та домашнього текстилю з кур&apos;єрською доставкою. Швидко, зручно, якісно!
            </p>
            <div className="flex gap-2">
              <a
                href="https://instagram.com/enot24cleaner"
                target="_blank"
                rel="noopener noreferrer"
                className={socialClass}
                aria-label="Instagram"
              >
                <FontAwesomeIcon icon={faInstagram} className="text-[14px]" />
              </a>
              <a
                href="https://t.me/enot24ServiceBot"
                target="_blank"
                rel="noopener noreferrer"
                className={socialClass}
                aria-label="Telegram"
              >
                <FontAwesomeIcon icon={faTelegram} className="text-[14px]" />
              </a>
              <a
                href="https://t.me/servisenot24"
                target="_blank"
                rel="noopener noreferrer"
                className={socialClass}
                aria-label="Служба підтримки в Telegram"
              >
                <FontAwesomeIcon icon={faHeadset} className="text-[13px]" />
              </a>
              <a
                href="viber://pa?chatURI=enot24"
                className={socialClass}
                aria-label="Viber"
              >
                <FontAwesomeIcon icon={faViber} className="text-[14px]" />
              </a>
            </div>
          </div>

          <div>
            <h4 className="font-bold text-[13px] text-white mb-4 uppercase tracking-wider">Навігація</h4>
            <ul className="space-y-2.5">
              {footerNavigationLinks.map((link) => (
                <li key={link.href}>
                  <Link to={link.href} className={linkClass}>
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="font-bold text-[13px] text-white mb-4 uppercase tracking-wider">Послуги</h4>
            <ul className="space-y-2.5 pr-1">
              {footerCategories.map((cat) => (
                <li key={cat.id}>
                  <Link to={categoryUrl(cat.id)} className={linkClass}>
                    {cat.title}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="font-bold text-[13px] text-white mb-4 uppercase tracking-wider">Контакти</h4>
            <ul className="space-y-3 text-[13px] text-white/50 mb-6">
              <li>
                <a
                  href="tel:+380678872233"
                  className="inline-flex items-center gap-2.5 hover:text-white no-underline transition-colors"
                >
                  <span className="w-7 h-7 rounded-lg bg-[#f97171]/20 flex items-center justify-center shrink-0">
                    <FontAwesomeIcon icon={faPhone} className="text-[#f97171] text-[11px]" />
                  </span>
                  067 887 22 33
                </a>
              </li>
              <li>
                <a
                  href="tel:+380443372233"
                  className="inline-flex items-center gap-2.5 hover:text-white no-underline transition-colors"
                >
                  <span className="w-7 h-7 rounded-lg bg-[#f97171]/20 flex items-center justify-center shrink-0">
                    <FontAwesomeIcon icon={faPhone} className="text-[#f97171] text-[11px]" />
                  </span>
                  044 337 22 33
                </a>
              </li>
              <li>
                <a
                  href="mailto:office.enot24@gmail.com"
                  className="inline-flex items-center gap-2.5 hover:text-white no-underline transition-colors break-all"
                >
                  <span className="w-7 h-7 rounded-lg bg-[#f97171]/20 flex items-center justify-center shrink-0">
                    <FontAwesomeIcon icon={faEnvelope} className="text-[#f97171] text-[11px]" />
                  </span>
                  office.enot24@gmail.com
                </a>
              </li>
            </ul>

            <h4 className="font-bold text-[13px] text-white mb-3 uppercase tracking-wider">Графік роботи</h4>
            <ul className="space-y-2 text-[13px] text-white/50">
              <li className="flex items-center gap-2">
                <FontAwesomeIcon icon={faClock} className="text-[#f97171] text-[12px]" />
                10:00 - 20:00
              </li>
              <li className="flex items-center gap-2">
                <FontAwesomeIcon icon={faCalendar} className="text-[#f97171] text-[12px]" />
                Без вихідних
              </li>
            </ul>
          </div>
        </div>

        <div className="border-t border-white/10 pt-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 text-[12px] text-white/35">
          <div className="space-y-2">
            <p>© {new Date().getFullYear()} ЄНОТ 24. Всі права захищені.</p>
            <div className="flex flex-wrap gap-x-4 gap-y-1">
              {legalLinks.map((link) => (
                <Link key={link.href} to={link.href} className="hover:text-white/70 no-underline text-white/35">
                  {link.label}
                </Link>
              ))}
            </div>
          </div>
          <div className="flex flex-wrap gap-x-5 gap-y-2">
            <Link to={ROUTES.contacts} className="hover:text-white/70 no-underline text-white/35">
              Контакти
            </Link>
            <Link to={ROUTES.locations} className="hover:text-white/70 no-underline text-white/35">
              Локації
            </Link>
          </div>
        </div>
      </div>
    </footer>
  );
}

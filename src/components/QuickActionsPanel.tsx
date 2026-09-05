import { useEffect, useRef, useState, type ReactNode } from "react";

import { Link } from "react-router-dom";

import { Swiper, SwiperSlide } from "swiper/react";

import type { Swiper as SwiperType } from "swiper";

import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

import {

  faAddressBook,

  faArrowUpRightFromSquare,

  faBolt,

  faCommentDots,

  faEnvelope,

  faHeadset,

  faLocationDot,

  faPhone,

  faShirt,

  faXmark,

} from "@fortawesome/free-solid-svg-icons";

import { faInstagram, faTelegram, faViber } from "@fortawesome/free-brands-svg-icons";

import type { IconDefinition } from "@fortawesome/fontawesome-svg-core";

import RaccoonLogo from "./RaccoonLogo";

import CategoryIcon from "./CategoryIcon";

import { useBootstrap } from "@/context/BootstrapContext";

import { topLevelCategories } from "@/lib/categories";

import { ROUTES, categoryUrl } from "@/lib/routes";

import { openFeedbackModal } from "@/context/FeedbackContext";



import "swiper/css";



type QuickAction = {

  id: string;

  label: string;

  desc: string;

  accent?: boolean;

  fa: IconDefinition;

  action: () => void;

};



type SlideMeta = {

  id: string;

  icon: IconDefinition;

  title: string;

};



const SLIDES: SlideMeta[] = [

  { id: "actions", icon: faBolt, title: "Швидкі дії" },

  { id: "branches", icon: faLocationDot, title: "Приймальні пункти" },

  { id: "categories", icon: faShirt, title: "Категорії" },

  { id: "bots", icon: faCommentDots, title: "Месенджери" },

  { id: "contacts", icon: faAddressBook, title: "Контакти" },

];



const CONTACT_ITEMS = [

  { icon: faPhone, label: "Телефон", value: "067 887 22 33", href: "tel:+380678872233" },

  { icon: faPhone, label: "Телефон", value: "044 337 22 33", href: "tel:+380443372233" },

  { icon: faEnvelope, label: "Email", value: "office.enot24@gmail.com", href: "mailto:office.enot24@gmail.com" },

  {

    icon: faInstagram,

    label: "Instagram",

    value: "@enot24cleaner",

    href: "https://instagram.com/enot24cleaner",

    external: true,

  },

] as const;



type Props = {

  onClose: () => void;

  quickActions: QuickAction[];

};



type PanelCardProps = {

  title: string;

  onClose: () => void;

  onConsultation: () => void;

  children: ReactNode;

};



function PanelCard({ title, onClose, onConsultation, children }: PanelCardProps) {

  return (

    <div className="quick-actions-panel__card glass-strong rounded-[28px] p-3.5 shadow-[0_20px_56px_rgba(26,26,46,0.18)] border border-white/60">

      <div className="quick-actions-panel__head">

        <div className="flex items-center gap-2 min-w-0">

          <RaccoonLogo size={28} />

          <div className="min-w-0">

            <div className="font-bold text-[13px] text-[#1A1A2E] leading-tight">{title}</div>

            <div className="text-[11px] text-[#1A1A2E]/45">ЄНОТ 24 · швидкий доступ</div>

          </div>

        </div>

        <button

          type="button"

          onClick={onClose}

          className="w-8 h-8 rounded-full bg-white/60 flex items-center justify-center text-[#1A1A2E]/55"

          aria-label="Закрити"

        >

          <FontAwesomeIcon icon={faXmark} />

        </button>

      </div>



      <div className="quick-actions-slide">{children}</div>



      <button

        type="button"

        onClick={onConsultation}

        className="flex items-center justify-center gap-2 w-full rounded-2xl bg-[#1A1A2E] text-white py-3 text-[13px] font-bold mt-3"

      >

        <FontAwesomeIcon icon={faPhone} className="text-[12px] text-[#f97171]" />

        Замовити консультацію

      </button>

    </div>

  );

}



export default function QuickActionsPanel({ onClose, quickActions }: Props) {

  const swiperRef = useRef<SwiperType | null>(null);

  const [activeIndex, setActiveIndex] = useState(0);

  const { branches = [], categories = [] } = useBootstrap();

  const navCategories = topLevelCategories(categories).slice(0, 8);

  const branchList = branches.slice(0, 6);



  useEffect(() => {

    return () => {

      swiperRef.current = null;

    };

  }, []);



  const progress = ((activeIndex + 1) / SLIDES.length) * 100;

  const remaining = SLIDES.length - activeIndex - 1;



  const openConsultation = () => {

    onClose();

    openFeedbackModal();

  };



  return (

    <div className="quick-actions-panel orbital-panel-in">

      <div className="quick-actions-nav">

        <div className="quick-actions-nav__icons" role="tablist" aria-label="Розділи швидких дій">

          {SLIDES.map((slide, index) => (

            <button

              key={slide.id}

              type="button"

              role="tab"

              aria-selected={activeIndex === index}

              aria-label={slide.title}

              title={slide.title}

              className={`quick-actions-nav__icon ${activeIndex === index ? "is-active" : ""}`}

              onClick={() => swiperRef.current?.slideTo(index)}

            >

              <FontAwesomeIcon icon={slide.icon} />

            </button>

          ))}

        </div>

        <div className="quick-actions-nav__progress" aria-hidden>

          <div className="quick-actions-nav__progress-fill" style={{ width: `${progress}%` }} />

        </div>

        <div className="quick-actions-nav__meta">

          <span>

            {activeIndex + 1} / {SLIDES.length}

          </span>

          <span>{remaining > 0 ? `Ще ${remaining} свайп${remaining === 1 ? "" : "и"} →` : "Готово ✓"}</span>

        </div>

      </div>



      <Swiper

        className="quick-actions-swiper"

        slidesPerView={1}

        spaceBetween={10}

        speed={320}

        autoHeight

        onSwiper={(s) => {

          swiperRef.current = s;

        }}

        onSlideChange={(s) => setActiveIndex(s.activeIndex)}

      >

        <SwiperSlide>

          <PanelCard title={SLIDES[0].title} onClose={onClose} onConsultation={openConsultation}>

            <div className="grid grid-cols-2 gap-2">

              {quickActions.map((a) => (

                <button

                  key={a.id}

                  type="button"

                  onClick={a.action}

                  className={`text-left rounded-2xl px-3 py-3 transition-all active:scale-[0.98] ${

                    a.accent

                      ? "bg-[#f97171] text-white shadow-md shadow-[#f97171]/30 col-span-2 flex items-center gap-3"

                      : "bg-white/55 hover:bg-white/80 border border-white/50"

                  }`}

                >

                  <span

                    className={`w-9 h-9 rounded-xl flex items-center justify-center shrink-0 ${

                      a.accent ? "bg-white/20" : "bg-white/70 text-[#f97171]"

                    }`}

                  >

                    <FontAwesomeIcon icon={a.fa} className="text-[14px]" />

                  </span>

                  <span className="min-w-0">

                    <span className={`block text-[13px] font-bold ${a.accent ? "" : "text-[#1A1A2E]"}`}>

                      {a.label}

                    </span>

                    <span className={`block text-[11px] mt-0.5 ${a.accent ? "text-white/75" : "text-[#1A1A2E]/45"}`}>

                      {a.desc}

                    </span>

                  </span>

                </button>

              ))}

            </div>

          </PanelCard>

        </SwiperSlide>



        <SwiperSlide>

          <PanelCard title={SLIDES[1].title} onClose={onClose} onConsultation={openConsultation}>

            <p className="quick-actions-slide__lead">Знайдіть найближчий пункт прийому або замовте кур&apos;єра.</p>

            <div className="quick-actions-branches">

              {branchList.length > 0 ? (

                branchList.map((branch) => (

                  <div key={branch.id} className="quick-actions-branch">

                    <div className="min-w-0 flex-1">

                      <div className="font-bold text-[11px] text-[#1A1A2E]">{branch.city}</div>

                      <div className="text-[12px] text-[#1A1A2E]/55 truncate">{branch.address}</div>

                      {branch.workingHours && (

                        <div className="text-[10px] text-[#1A1A2E]/40 mt-0.5">{branch.workingHours}</div>

                      )}

                    </div>

                    {branch.linkMap && (

                      <a

                        href={branch.linkMap}

                        target="_blank"

                        rel="noopener noreferrer"

                        className="quick-actions-branch__map"

                        aria-label="Відкрити на карті"

                      >

                        <FontAwesomeIcon icon={faArrowUpRightFromSquare} />

                      </a>

                    )}

                  </div>

                ))

              ) : (

                <p className="text-[12px] text-[#1A1A2E]/50 px-1">Завантаження локацій…</p>

              )}

            </div>

            <Link to={ROUTES.locations} onClick={onClose} className="quick-actions-slide__link">

              Усі локації на карті →

            </Link>

          </PanelCard>

        </SwiperSlide>



        <SwiperSlide>

          <PanelCard title={SLIDES[2].title} onClose={onClose} onConsultation={openConsultation}>

            <p className="quick-actions-slide__lead">Оберіть категорію послуг — ціни та оформлення на сайті.</p>

            <div className="quick-actions-categories">

              {navCategories.map((cat) => (

                <Link key={cat.id} to={categoryUrl(cat.id)} onClick={onClose} className="quick-actions-category">

                  <span className="quick-actions-category__icon">

                    {cat.iconUrl ? (

                      <CategoryIcon src={cat.iconUrl} size={22} alt="" />

                    ) : (

                      <CategoryIcon name="tshirt" size={22} alt="" />

                    )}

                  </span>

                  <span className="quick-actions-category__title">{cat.title}</span>

                </Link>

              ))}

            </div>

            <Link to={ROUTES.services} onClick={onClose} className="quick-actions-slide__link">

              Весь каталог послуг →

            </Link>

          </PanelCard>

        </SwiperSlide>



        <SwiperSlide>

          <PanelCard title={SLIDES[3].title} onClose={onClose} onConsultation={openConsultation}>

            <p className="quick-actions-slide__lead">Замовлення, статус і консультації — у зручному месенджері.</p>

            <div className="quick-actions-bots">

              <div className="quick-actions-bots__col">

                <div className="quick-actions-bots__label">

                  <FontAwesomeIcon icon={faTelegram} />

                  Telegram

                </div>

                <a

                  href="https://t.me/enot24ServiceBot"

                  target="_blank"

                  rel="noopener noreferrer"

                  className="quick-actions-bot-card quick-actions-bot-card--tg"

                >

                  <FontAwesomeIcon icon={faTelegram} className="quick-actions-bot-card__brand" />

                  <div className="min-w-0 flex-1">

                    <div className="font-bold text-[12px] text-[#1A1A2E]">@enot24ServiceBot</div>

                    <div className="text-[10px] text-[#1A1A2E]/50">Основний бот</div>

                  </div>

                  <FontAwesomeIcon icon={faArrowUpRightFromSquare} className="text-[10px] text-[#1A1A2E]/30" />

                </a>

                <a

                  href="https://t.me/servisenot24"

                  target="_blank"

                  rel="noopener noreferrer"

                  className="quick-actions-bot-card"

                >

                  <FontAwesomeIcon icon={faHeadset} className="quick-actions-bot-card__support" />

                  <div className="min-w-0 flex-1">

                    <div className="font-bold text-[12px] text-[#1A1A2E]">Підтримка</div>

                    <div className="text-[10px] text-[#1A1A2E]/50">Допомога та консультації</div>

                  </div>

                  <FontAwesomeIcon icon={faArrowUpRightFromSquare} className="text-[10px] text-[#1A1A2E]/30" />

                </a>

              </div>



              <div className="quick-actions-bots__col">

                <div className="quick-actions-bots__label">

                  <FontAwesomeIcon icon={faViber} />

                  Viber

                </div>

                <a href="viber://pa?chatURI=enot24" className="quick-actions-bot-card quick-actions-bot-card--vb">

                  <FontAwesomeIcon

                    icon={faViber}

                    className="quick-actions-bot-card__brand quick-actions-bot-card__brand--vb"

                  />

                  <div className="min-w-0 flex-1">

                    <div className="font-bold text-[12px] text-[#1A1A2E]">ЄНОТ 24</div>

                    <div className="text-[10px] text-[#1A1A2E]/50">Натисніть для відкриття</div>

                  </div>

                  <FontAwesomeIcon icon={faArrowUpRightFromSquare} className="text-[10px] text-[#1A1A2E]/30" />

                </a>

                <p className="quick-actions-bots__note">Відкриється в додатку Viber</p>

              </div>

            </div>

          </PanelCard>

        </SwiperSlide>



        <SwiperSlide>

          <PanelCard title={SLIDES[4].title} onClose={onClose} onConsultation={openConsultation}>

            <p className="quick-actions-slide__lead">Ми на зв&apos;язку щодня — оберіть зручний канал.</p>

            <div className="quick-actions-contacts">

              {CONTACT_ITEMS.map((item) => (

                <a

                  key={item.href}

                  href={item.href}

                  target={"external" in item && item.external ? "_blank" : undefined}

                  rel={"external" in item && item.external ? "noopener noreferrer" : undefined}

                  className="quick-actions-contact"

                >

                  <span className="quick-actions-contact__icon">

                    <FontAwesomeIcon icon={item.icon} />

                  </span>

                  <span className="min-w-0 flex-1">

                    <span className="block text-[10px] text-[#1A1A2E]/45 uppercase tracking-wide">{item.label}</span>

                    <span className="block text-[12px] font-bold text-[#1A1A2E] truncate">{item.value}</span>

                  </span>

                </a>

              ))}

            </div>

            <Link to={ROUTES.contacts} onClick={onClose} className="quick-actions-slide__link">

              Сторінка контактів →

            </Link>

          </PanelCard>

        </SwiperSlide>

      </Swiper>

    </div>

  );

}


import type { IconName } from "@/storage/icons";

export type CatalogItem = {
  name: string;
  price: string;
  promo?: boolean;
  /** Ціна до знижки (для акцій) */
  oldPrice?: string;
  /** Потокова чистка; якщо немає — рахується автоматично */
  priceBatch?: string;
  /** Окрема індивідуальна ціна з API (null — тип недоступний) */
  individualPrice?: string | null;
};

export type CatalogCategory = {
  id: string;
  title: string;
  icon: IconName;
  items: CatalogItem[];
};

function parseUah(value: string): number | null {
  if (!value || value.includes("запитом")) return null;
  const n = Number(value.replace(/[^\d]/g, ""));
  return Number.isFinite(n) && n > 0 ? n : null;
}

function formatUah(n: number): string {
  return `${n.toLocaleString("uk-UA")}₴`;
}

/** Витягує стару ціну з назви на кшталт «… 550-20%» */
export function inferOldPrice(item: CatalogItem): string | undefined {
  if (item.oldPrice) return item.oldPrice;
  const m = item.name.match(/(\d+)\s*-\s*\d+%/);
  if (m) return `${Number(m[1]).toLocaleString("uk-UA")}₴`;
  return undefined;
}

export function resolveItemPricing(item: CatalogItem) {
  const oldPrice = item.promo ? inferOldPrice(item) : item.oldPrice;
  let batch = item.priceBatch;
  if (!batch) {
    if (item.price === "Ціна за запитом" || !item.price) {
      batch = item.price;
    } else {
      const n = parseUah(item.price);
      batch = n ? formatUah(Math.round(n * 0.85)) : item.price;
    }
  }
  return {
    individual: item.individualPrice ?? null,
    batch,
    oldPrice,
    promo: Boolean(item.promo || oldPrice),
    discountLabel: item.promo ? "Акція" : undefined,
  };
}


export const catalog: CatalogCategory[] = [
  {
    "id": "чистка-та-ремонт-взуття",
    "title": "Чистка та ремонт взуття",
    "icon": "shoe",
    "items": [
      {
        "name": "UGG",
        "price": "1,900₴",
        "promo": false
      },
      {
        "name": "Балетки",
        "price": "1,950₴",
        "promo": false
      },
      {
        "name": "Босоніжки",
        "price": "1,950₴",
        "promo": false
      },
      {
        "name": "Ботфорди",
        "price": "3,300₴",
        "promo": false
      },
      {
        "name": "Кросівки",
        "price": "1,550₴",
        "promo": false
      },
      {
        "name": "Лофери",
        "price": "1,950₴",
        "promo": false
      },
      {
        "name": "Мокасини",
        "price": "1,950₴",
        "promo": false
      },
      {
        "name": "Напівчеревики",
        "price": "2,200₴",
        "promo": false
      },
      {
        "name": "Туфлі",
        "price": "1,950₴",
        "promo": false
      },
      {
        "name": "Черевики",
        "price": "2,500₴",
        "promo": false
      },
      {
        "name": "UGG",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Балетки",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Босоніжки",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Ботфорди",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Кросівки",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Лофери",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Мокасини",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Напівчеревики",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Туфлі",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Черевики",
        "price": "Ціна за запитом",
        "promo": false
      }
    ]
  },
  {
    "id": "верхній-одяг",
    "title": "Верхній одяг",
    "icon": "jacket",
    "items": [
      {
        "name": "Вітровка",
        "price": "830₴",
        "promo": false
      },
      {
        "name": "Дублянка штучна від 70 см",
        "price": "1,400₴",
        "promo": false
      },
      {
        "name": "Дублянка штучна до 70 см",
        "price": "1,150₴",
        "promo": false
      },
      {
        "name": "Жилет синтепон/пух",
        "price": "890₴",
        "promo": false
      },
      {
        "name": "Жилет штучне хутро",
        "price": "750₴",
        "promo": false
      },
      {
        "name": "Комбінезон лижний",
        "price": "1,500₴",
        "promo": false
      },
      {
        "name": "Куртка джинсова",
        "price": "980₴",
        "promo": false
      },
      {
        "name": "Куртка лижна",
        "price": "1,400₴",
        "promo": false
      },
      {
        "name": "Куртка пуховик/синтепон",
        "price": "1,200₴",
        "promo": false
      },
      {
        "name": "Пальто довге синтепон/пух",
        "price": "1,400₴",
        "promo": false
      },
      {
        "name": "Пальто текстильне коротке",
        "price": "920₴",
        "promo": false
      },
      {
        "name": "Пальто текстильне подовжене",
        "price": "1,040₴",
        "promo": false
      },
      {
        "name": "Парка",
        "price": "1,400₴",
        "promo": false
      },
      {
        "name": "Підстібка штучне хутро",
        "price": "850₴",
        "promo": false
      },
      {
        "name": "Плащ/тренч",
        "price": "900₴",
        "promo": false
      },
      {
        "name": "Штани лижні",
        "price": "930₴",
        "promo": false
      },
      {
        "name": "Шуба штучна від 70 см (додатково)",
        "price": "1,400₴",
        "promo": false
      },
      {
        "name": "Шуба штучна до 70 см",
        "price": "1,150₴",
        "promo": false
      }
    ]
  },
  {
    "id": "домашній-текстиль",
    "title": "Домашній текстиль",
    "icon": "pillow",
    "items": [
      {
        "name": "Білизна (комплект)",
        "price": "400₴",
        "promo": false
      },
      {
        "name": "Білизна кг ( мінімальне завантаження 10 кг )",
        "price": "65₴",
        "promo": false
      },
      {
        "name": "Жалюзі з текстилю",
        "price": "580₴",
        "promo": false
      },
      {
        "name": "Ковдра двоспальна",
        "price": "1,200₴",
        "promo": false
      },
      {
        "name": "Ковдра полуторна",
        "price": "980₴",
        "promo": false
      },
      {
        "name": "Ламбрекен за кг",
        "price": "470₴",
        "promo": false
      },
      {
        "name": "Наматрацник на блискавці (шт.)",
        "price": "1,750₴",
        "promo": false
      },
      {
        "name": "Наматрацник на резинці (шт.)",
        "price": "1,100₴",
        "promo": false
      },
      {
        "name": "Плед двохспальний",
        "price": "950₴",
        "promo": false
      },
      {
        "name": "Плед полуторний",
        "price": "830₴",
        "promo": false
      },
      {
        "name": "Подушка з синтетичним наповнювачем",
        "price": "830₴",
        "promo": false
      },
      {
        "name": "Подушка пух-перо без заміни напірника (аквачистка)",
        "price": "850₴",
        "promo": false
      },
      {
        "name": "Подушка пух-перо з заміною напірника (перенаповнення)",
        "price": "920₴",
        "promo": false
      },
      {
        "name": "Подушки меблеві",
        "price": "998₴",
        "promo": false
      },
      {
        "name": "Подушки меблеві (попередня консультація технолога, ціна від)",
        "price": "1,000₴",
        "promo": false
      },
      {
        "name": "Прання речей за кг (мінімальне завантаження від 3 кг)",
        "price": "430₴",
        "promo": false
      },
      {
        "name": "Римські штори",
        "price": "580₴",
        "promo": false
      },
      {
        "name": "Скатертина від 1 кг",
        "price": "570₴",
        "promo": false
      },
      {
        "name": "Спальний мішок (шт.)",
        "price": "1,500₴",
        "promo": false
      },
      {
        "name": "Тюль (м²)",
        "price": "270₴",
        "promo": false
      },
      {
        "name": "Чохол меблевий за кг",
        "price": "560₴",
        "promo": false
      },
      {
        "name": "Штори (м²)",
        "price": "350₴",
        "promo": false
      },
      {
        "name": "Штори подвійні (м²)",
        "price": "510₴",
        "promo": false
      }
    ]
  },
  {
    "id": "прання-килимів",
    "title": "Прання килимів",
    "icon": "carpet",
    "items": [
      {
        "name": "Автомобільні килими (до 5 шт., за шт.)",
        "price": "1,000₴",
        "promo": false
      },
      {
        "name": "Килим акрил (м²) 550-20%",
        "price": "440₴",
        "promo": true
      },
      {
        "name": "Килим віскоза (м²)",
        "price": "750₴",
        "promo": false
      },
      {
        "name": "Килим вовна (м²) 500-20%",
        "price": "400₴",
        "promo": true
      },
      {
        "name": "Килим з натурального хутра (м²)",
        "price": "1,500₴",
        "promo": false
      },
      {
        "name": "Килим Синтетика (м²) 480-20%",
        "price": "384₴",
        "promo": true
      },
      {
        "name": "Килим шеггі (м²) 570-20%",
        "price": "456₴",
        "promo": true
      },
      {
        "name": "Килим шовковий (м²)",
        "price": "950₴",
        "promo": false
      },
      {
        "name": "Килими з натуральної шкіри (м²)",
        "price": "1,500₴",
        "promo": false
      },
      {
        "name": "Килими ручної роботи-ексклюзивні (м²)",
        "price": "1,000₴",
        "promo": false
      },
      {
        "name": "Килимок для йоги (шт.)",
        "price": "780₴",
        "promo": false
      },
      {
        "name": "Невеликі килими (до 1,2 м², за шт.)",
        "price": "700₴",
        "promo": false
      }
    ]
  },
  {
    "id": "текстильний-одяг",
    "title": "Текстильний Одяг",
    "icon": "tshirt",
    "items": [
      {
        "name": "Блуза простого крою",
        "price": "560₴",
        "promo": false
      },
      {
        "name": "Блуза шовкова/з декором",
        "price": "820₴",
        "promo": false
      },
      {
        "name": "Вишита сорочка",
        "price": "870₴",
        "promo": false
      },
      {
        "name": "Вишита сукня",
        "price": "1,190₴",
        "promo": false
      },
      {
        "name": "Гольф",
        "price": "530₴",
        "promo": false
      },
      {
        "name": "Джемпер",
        "price": "580₴",
        "promo": false
      },
      {
        "name": "Джинси",
        "price": "770₴",
        "promo": false
      },
      {
        "name": "Жилет",
        "price": "500₴",
        "promo": false
      },
      {
        "name": "Капрі",
        "price": "600₴",
        "promo": false
      },
      {
        "name": "Кардиган",
        "price": "750₴",
        "promo": false
      },
      {
        "name": "Комбінезон джинсовий",
        "price": "850₴",
        "promo": false
      },
      {
        "name": "Комбінезон текстильний",
        "price": "810₴",
        "promo": false
      },
      {
        "name": "Комбінезон шовковий",
        "price": "950₴",
        "promo": false
      },
      {
        "name": "Костюм діловий (піджак + штани / спідниця)",
        "price": "1,180₴",
        "promo": false
      },
      {
        "name": "Костюм спортивний (кофта + штани)",
        "price": "1,130₴",
        "promo": false
      },
      {
        "name": "Кофта",
        "price": "650₴",
        "promo": false
      },
      {
        "name": "Майка",
        "price": "520₴",
        "promo": false
      },
      {
        "name": "Піджак",
        "price": "750₴",
        "promo": false
      },
      {
        "name": "Піжама (верх+низ)",
        "price": "700₴",
        "promo": false
      },
      {
        "name": "Реглан",
        "price": "800₴",
        "promo": false
      },
      {
        "name": "Сарафан/сукня",
        "price": "950₴",
        "promo": false
      },
      {
        "name": "Свiтшот",
        "price": "800₴",
        "promo": false
      },
      {
        "name": "Светр",
        "price": "790₴",
        "promo": false
      },
      {
        "name": "Смокінг/Фрак",
        "price": "1,290₴",
        "promo": false
      },
      {
        "name": "Сорочка",
        "price": "570₴",
        "promo": false
      },
      {
        "name": "Сорочка з декором",
        "price": "720₴",
        "promo": false
      },
      {
        "name": "Спідниця в'язана, вовняна, шовкова",
        "price": "830₴",
        "promo": false
      },
      {
        "name": "Спідниця плісе, гофре, складки",
        "price": "880₴",
        "promo": false
      },
      {
        "name": "Спідниця простого фасону",
        "price": "570₴",
        "promo": false
      },
      {
        "name": "Сукня в`язана, вовняна, шовкова",
        "price": "1,020₴",
        "promo": false
      },
      {
        "name": "Сукня весільна без декору",
        "price": "3,200₴",
        "promo": false
      },
      {
        "name": "Сукня весільна з декором",
        "price": "4,270₴",
        "promo": false
      },
      {
        "name": "Сукня вечірня/святкова",
        "price": "2,000₴",
        "promo": false
      },
      {
        "name": "Сукня вечірня/святкова коротка",
        "price": "1,600₴",
        "promo": false
      },
      {
        "name": "Сукня простого фасону",
        "price": "950₴",
        "promo": false
      },
      {
        "name": "Теніска",
        "price": "550₴",
        "promo": false
      },
      {
        "name": "Топ",
        "price": "400₴",
        "promo": false
      },
      {
        "name": "Туніка",
        "price": "640₴",
        "promo": false
      },
      {
        "name": "Футболка",
        "price": "520₴",
        "promo": false
      },
      {
        "name": "Футболка поло",
        "price": "620₴",
        "promo": false
      },
      {
        "name": "Халат махровий",
        "price": "600₴",
        "promo": false
      },
      {
        "name": "Халат шовковий",
        "price": "880₴",
        "promo": false
      },
      {
        "name": "Худі",
        "price": "800₴",
        "promo": false
      },
      {
        "name": "Шорти",
        "price": "640₴",
        "promo": false
      },
      {
        "name": "Штани класичні",
        "price": "670₴",
        "promo": false
      },
      {
        "name": "Штани спортивні",
        "price": "630₴",
        "promo": false
      }
    ]
  },
  {
    "id": "сумки-та-рюкзаки",
    "title": "Сумки та рюкзаки",
    "icon": "bag",
    "items": [
      {
        "name": "Валіза (ціна від)",
        "price": "2,300₴",
        "promo": false
      },
      {
        "name": "Рюкзак текстильний (ціна від)",
        "price": "1,500₴",
        "promo": false
      },
      {
        "name": "Рюкзак шкіряний (ціна від)",
        "price": "3,500₴",
        "promo": false
      },
      {
        "name": "Сумка текстильна (ціна від)",
        "price": "1,700₴",
        "promo": false
      },
      {
        "name": "Сумка текстильна дорожня з декором (ціна від)",
        "price": "3,000₴",
        "promo": false
      },
      {
        "name": "Сумка шкіряна (ціна від)",
        "price": "4,000₴",
        "promo": false
      },
      {
        "name": "Сумка шкіряна дорожня з декором (ціна від)",
        "price": "5,000₴",
        "promo": false
      }
    ]
  },
  {
    "id": "хімчистка-аксесуарів",
    "title": "Хімчистка аксесуарів",
    "icon": "hands",
    "items": [
      {
        "name": "Берет текстильний",
        "price": "650₴",
        "promo": false
      },
      {
        "name": "Боді",
        "price": "650₴",
        "promo": false
      },
      {
        "name": "Гаманець шкіряний (ціна від)",
        "price": "1,200₴",
        "promo": false
      },
      {
        "name": "Капелюх (ціна від)",
        "price": "1,000₴",
        "promo": false
      },
      {
        "name": "Кепка",
        "price": "1,000₴",
        "promo": false
      },
      {
        "name": "Комір з натурального хутра",
        "price": "700₴",
        "promo": false
      },
      {
        "name": "Комір з штучного хутра",
        "price": "400₴",
        "promo": false
      },
      {
        "name": "Корсет",
        "price": "950₴",
        "promo": false
      },
      {
        "name": "Краватка (ціна від)",
        "price": "500₴",
        "promo": false
      },
      {
        "name": "Купальник",
        "price": "800₴",
        "promo": false
      },
      {
        "name": "Палантин",
        "price": "650₴",
        "promo": false
      },
      {
        "name": "Панама",
        "price": "700₴",
        "promo": false
      },
      {
        "name": "Ремінь із текстилю",
        "price": "550₴",
        "promo": false
      },
      {
        "name": "Рукавички лижні (пара)",
        "price": "900₴",
        "promo": false
      },
      {
        "name": "Рукавички текстильні (пара)",
        "price": "600₴",
        "promo": false
      },
      {
        "name": "Фата (ціна від)",
        "price": "700₴",
        "promo": false
      },
      {
        "name": "Хустка",
        "price": "600₴",
        "promo": false
      },
      {
        "name": "Шаль (ціна від)",
        "price": "600₴",
        "promo": false
      },
      {
        "name": "Шапка",
        "price": "700₴",
        "promo": false
      },
      {
        "name": "Шарф (ціна від)",
        "price": "700₴",
        "promo": false
      }
    ]
  },
  {
    "id": "м-які-меблі",
    "title": "М'які меблі",
    "icon": "sofa",
    "items": [
      {
        "name": "Матрац тонкий по типу Dormeo (ціна від)",
        "price": "2,500₴",
        "promo": false
      },
      {
        "name": "Матрац, чищення (ціна від)",
        "price": "3,000₴",
        "promo": false
      },
      {
        "name": "Пуф (ціна від)",
        "price": "1,000₴",
        "promo": false
      },
      {
        "name": "Чистка дивану",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Чистка крісла (ціна від)",
        "price": "1,500₴",
        "promo": false
      },
      {
        "name": "Чистка стільця (ціна від)",
        "price": "750₴",
        "promo": false
      },
      {
        "name": "Чистка текстильних елементів ліжка (ціна від)",
        "price": "2,000₴",
        "promo": false
      },
      {
        "name": "Чистка шкіряних елементів ліжка (ціна від)",
        "price": "2,800₴",
        "promo": false
      }
    ]
  },
  {
    "id": "хімчистка-одягу-з-шкіри-та-хутра",
    "title": "Хімчистка одягу з шкіри та хутра",
    "icon": "leather",
    "items": [
      {
        "name": "Дублянка довга шкіряна (від 90 см)",
        "price": "4,000₴",
        "promo": false
      },
      {
        "name": "Дублянка коротка шкіряна (до 70 см)",
        "price": "3,000₴",
        "promo": false
      },
      {
        "name": "Дублянка середня шкіряна (до 90 см)",
        "price": "3,500₴",
        "promo": false
      },
      {
        "name": "Жилет шкіряний",
        "price": "1,700₴",
        "promo": false
      },
      {
        "name": "Жилет/підстібка із натурального хутра (ціна від)",
        "price": "2,500₴",
        "promo": false
      },
      {
        "name": "Комір шкіряний",
        "price": "900₴",
        "promo": false
      },
      {
        "name": "Куртка шкіряна",
        "price": "3,500₴",
        "promo": false
      },
      {
        "name": "Напівшубок із натурального хутра (до 70 см, ціна від)",
        "price": "3,000₴",
        "promo": false
      },
      {
        "name": "Піджак шкіряний",
        "price": "3,000₴",
        "promo": false
      },
      {
        "name": "Плащ шкіряний (від 110 см)",
        "price": "4,500₴",
        "promo": false
      },
      {
        "name": "Плащ шкіряний (до 110 см)",
        "price": "4,000₴",
        "promo": false
      },
      {
        "name": "Сорочка шкіряна",
        "price": "2,500₴",
        "promo": false
      },
      {
        "name": "Спідниця шкіряна (ціна від)",
        "price": "1,500₴",
        "promo": false
      },
      {
        "name": "Сукня шкіряна (ціна від)",
        "price": "2,000₴",
        "promo": false
      },
      {
        "name": "Шорти шкіряні (ціна від)",
        "price": "1,500₴",
        "promo": false
      },
      {
        "name": "Штани шкіряні",
        "price": "2,000₴",
        "promo": false
      },
      {
        "name": "Шуба з натурального хутра (від 70 см, ціна від)",
        "price": "3,700₴",
        "promo": false
      }
    ]
  },
  {
    "id": "прайс-праски",
    "title": "Прайс праски",
    "icon": "iron",
    "items": [
      {
        "name": "Прасування БЛУЗА",
        "price": "180₴",
        "promo": false
      },
      {
        "name": "Прасування БЛУЗА ШОВКОВА. З РЮШАМИ, З ОБРОБКОЮ",
        "price": "230₴",
        "promo": false
      },
      {
        "name": "Прасування БРЮКИ",
        "price": "180₴",
        "promo": false
      },
      {
        "name": "Прасування КРАВАТКА",
        "price": "120₴",
        "promo": false
      },
      {
        "name": "Прасування КУРТКА",
        "price": "260₴",
        "promo": false
      },
      {
        "name": "Прасування ПАЛЬТО",
        "price": "260₴",
        "promo": false
      },
      {
        "name": "Прасування ПІДЖАК",
        "price": "190₴",
        "promo": false
      },
      {
        "name": "Прасування ПЛАЩ",
        "price": "260₴",
        "promo": false
      },
      {
        "name": "Прасування ПОСТІЛЬНА БІЛИЗНА",
        "price": "200₴",
        "promo": false
      },
      {
        "name": "Прасування СВЕТР",
        "price": "180₴",
        "promo": false
      },
      {
        "name": "Прасування СОРОЧКА",
        "price": "180₴",
        "promo": false
      },
      {
        "name": "Прасування СПІДНИЦЯ",
        "price": "180₴",
        "promo": false
      },
      {
        "name": "Прасування СПІДНИЦЯ ПЛІСЕ",
        "price": "320₴",
        "promo": false
      },
      {
        "name": "Прасування СУКНЯ",
        "price": "Ціна за запитом",
        "promo": false
      },
      {
        "name": "Прасування СУКНЯ ВЕСІЛЬНА, ВЕЧІРНЕ",
        "price": "800₴",
        "promo": false
      },
      {
        "name": "Прасування ТОП/МАЙКА/ФУТБОЛКА",
        "price": "90₴",
        "promo": false
      },
      {
        "name": "Прасування ФУТБОЛКА",
        "price": "140₴",
        "promo": false
      },
      {
        "name": "Прасування ШТОРИ/ТЮЛЬ",
        "price": "Ціна за запитом",
        "promo": false
      }
    ]
  },
  {
    "id": "для-улюбленців",
    "title": "Для улюбленців",
    "icon": "paw",
    "items": [
      {
        "name": "Взуття улюбленця",
        "price": "350₴",
        "promo": false
      },
      {
        "name": "Килим улюбленця",
        "price": "600₴",
        "promo": false
      },
      {
        "name": "Костюм улюбленця",
        "price": "650₴",
        "promo": false
      },
      {
        "name": "Лежак для улюбленця (ціна від)",
        "price": "1,300₴",
        "promo": false
      },
      {
        "name": "Лежанка/Диван улюбленця",
        "price": "570₴",
        "promo": false
      },
      {
        "name": "Матрац улюбленця",
        "price": "1,100₴",
        "promo": false
      },
      {
        "name": "Нашийник/Поводок",
        "price": "390₴",
        "promo": false
      },
      {
        "name": "Рюкзак/сумка-переноска (шт.)",
        "price": "1,300₴",
        "promo": false
      },
      {
        "name": "Светр улюбленця",
        "price": "240₴",
        "promo": false
      },
      {
        "name": "Сукня улюбленця",
        "price": "210₴",
        "promo": false
      },
      {
        "name": "Утеплений жилет для улюбленця",
        "price": "335₴",
        "promo": false
      },
      {
        "name": "Утеплений куртка/комбез улюбленця",
        "price": "335₴",
        "promo": false
      },
      {
        "name": "Хатинка (шт.)",
        "price": "900₴",
        "promo": false
      },
      {
        "name": "Штани улюбленця",
        "price": "210₴",
        "promo": false
      }
    ]
  },
  {
    "id": "іграшки-та-дитячі-речі",
    "title": "Іграшки та дитячі речі",
    "icon": "baby",
    "items": [
      {
        "name": "Автокрісло (ціна від)",
        "price": "2,500₴",
        "promo": false
      },
      {
        "name": "Дитячий візок (ціна від)",
        "price": "4,500₴",
        "promo": false
      },
      {
        "name": "Дитячий конверт",
        "price": "800₴",
        "promo": false
      },
      {
        "name": "Іграшка від 1 м (ціна від)",
        "price": "1,900₴",
        "promo": false
      },
      {
        "name": "Іграшка від 50 см до 1 м",
        "price": "800₴",
        "promo": false
      },
      {
        "name": "Комбінезон дитячий утеплений",
        "price": "900₴",
        "promo": false
      },
      {
        "name": "Послуга демонтаж/монтаж дитячого візка",
        "price": "500₴",
        "promo": false
      }
    ]
  }
] as CatalogCategory[];

export const serviceHighlights = [
  {
    icon: "tshirt" as IconName,
    title: "Хімчистка та екочистка одягу",
    desc: "Гіпоалергенний догляд за текстилем і верхнім одягом",
  },
  {
    icon: "carpet" as IconName,
    title: "Чистка та оновлення килимів",
    desc: "Прання килимів із відновленням кольору та ворсу",
  },
  {
    icon: "shoe" as IconName,
    title: "Ремонт та відновлення взуття",
    desc: "Чистка і ремонт пари будь-якого типу",
  },
  {
    icon: "hands" as IconName,
    title: "Послуги хімчистки для бізнесу",
    desc: "Особливі умови для корпоративних клієнтів",
    page: "b2b" as const,
  },
];

export const blogPosts = [
  {
    title: "Евакуація гардеробу: як врятувати одяг та текстиль після затоплення квартири або прориву каналізації",
    tag: "Догляд",
  },
  {
    title: "Як підготувати спальний мішок та намет до сезону або зберігання",
    tag: "Сезон",
  },
  {
    title: "Текстильні абажури та світильники: як безпечно прибрати пил, кіптяву та сліди комах",
    tag: "Поради",
  },
  {
    title: "Як доглядати за шовковою постільною білизною та наволочками",
    tag: "Текстиль",
  },
  {
    title: "Як очистити текстильні кухонні серветки та прихватки від застарілого жиру",
    tag: "Кухня",
  },
];

export const promos = [
  { pct: "−20%", title: "Килим акрил", price: "440₴/м²", note: "замість 550₴" },
  { pct: "−20%", title: "Килим вовна", price: "400₴/м²", note: "замість 500₴" },
  { pct: "−20%", title: "Килим синтетика", price: "384₴/м²", note: "замість 480₴" },
  { pct: "−20%", title: "Килим шеггі", price: "456₴/м²", note: "замість 570₴" },
];

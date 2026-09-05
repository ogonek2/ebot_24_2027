import dress from "./dress.png";
import sofa from "./sofa.png";
import paw from "./paw.png";
import baby from "./baby.png";
import leather from "./leather.png";
import pillow from "./pillow.png";
import chat from "./chat.png";
import hands from "./hands.png";
import jacket from "./jacket.png";
import raccoon from "./raccoon.png";
import shoe from "./shoe.png";
import carpet from "./carpet.png";
import tshirt from "./tshirt.png";
import promo from "./promo.png";
import bag from "./bag.png";
import iron from "./iron.png";
import eco from "./eco.png";
import price from "./price.png";
import washer from "./washer.png";
import delivery from "./delivery.png";
import location from "./location.png";

export const icons = {
  dress,
  sofa,
  paw,
  baby,
  leather,
  pillow,
  chat,
  hands,
  jacket,
  raccoon,
  shoe,
  carpet,
  tshirt,
  promo,
  bag,
  iron,
  eco,
  price,
  washer,
  delivery,
  location,
} as const;

export type IconName = keyof typeof icons;

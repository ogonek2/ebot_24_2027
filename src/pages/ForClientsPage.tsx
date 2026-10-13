import { useState } from "react";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import { submitConsumerReview } from "@/lib/api";
import {
  formatUaPhoneInput,
  isUaPhoneComplete,
  phoneDisplayPlaceholder,
} from "@/lib/phoneMask";
import { ROUTES } from "@/lib/routes";

const NAV = [
  { id: "result", label: "Результат хімчистки" },
  { id: "formats", label: "Формати чищення" },
  { id: "photos", label: "Фотофіксація" },
  { id: "labels", label: "Речі без маркування" },
  { id: "replicas", label: "Репліки та склад виробу" },
  { id: "before", label: "Перед замовленням" },
  { id: "feedback", label: "Звернення та претензії" },
  { id: "corner", label: "Куточок споживача" },
  { id: "laws", label: "Документи та стандарти" },
  { id: "rights", label: "Захист ваших прав" },
] as const;

type ReviewType = "review" | "claim" | "suggestion";

export default function ForClientsPage() {
  const [sent, setSent] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [form, setForm] = useState({
    name: "",
    phone: "+380 (",
    email: "",
    order_number: "",
    type: "review" as ReviewType,
    message: "",
  });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");

    if (!form.name.trim()) {
      setError("Введіть ім'я");
      return;
    }
    if (!isUaPhoneComplete(form.phone)) {
      setError("Введіть коректний номер телефону");
      return;
    }
    if (!form.message.trim()) {
      setError("Напишіть текст звернення");
      return;
    }

    setLoading(true);
    try {
      const res = await submitConsumerReview({
        name: form.name.trim(),
        phone: form.phone,
        email: form.email.trim() || undefined,
        order_number: form.order_number.trim() || undefined,
        type: form.type,
        message: form.message.trim(),
      });
      if (res.success !== true) {
        setError(res.message ?? "Помилка відправки. Спробуйте ще раз.");
        return;
      }
      setSent(true);
    } catch {
      setError("Помилка відправки. Спробуйте ще раз.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen pt-24 pb-20 clients-page">
      <div className="site-container">
        <Breadcrumbs items={[homeCrumb(), { name: "Для клієнтів", url: ROUTES.forClients }]} />

        <div className="clients-page__intro">
          <p className="clients-page__eyebrow">Інформація для споживачів</p>
          <h1 className="text-section text-[#1A1A2E]">Для клієнтів</h1>
          <p className="clients-page__lead">
            Що варто знати про чищення речей, умови обслуговування та захист ваших прав.
          </p>
        </div>

        <div className="clients-page__layout">
          <aside className="clients-page__aside" aria-label="Розділи сторінки">
            <nav className="clients-page__nav">
              {NAV.map((item) => (
                <a key={item.id} href={`#${item.id}`} className="clients-page__nav-link">
                  {item.label}
                </a>
              ))}
            </nav>
          </aside>

          <article className="clients-page__article">
            <section id="result" className="clients-page__section">
              <div className="clients-page__feature">
                <p className="clients-page__eyebrow clients-page__eyebrow--on-dark">
                  Про очікування та результат
                </p>
                <h2>
                  Дбайливий догляд
                  <br />
                  за вашими речами
                </h2>
                <p>
                  Ми дбаємо про ваші речі та прагнемо до найкращого результату, який дозволяють їхній стан і
                  властивості матеріалу.
                </p>
              </div>
              <p>
                Професійне чищення допомагає позбутися забруднень, освіжити річ і покращити її вигляд. Водночас навіть
                делікатна обробка не може усунути природне зношення, відновити вигорілий колір або пошкоджені волокна.
              </p>
              <p>
                Кожна річ має свою історію, тому результат залежить не лише від чищення, а й від матеріалу, давності
                плям, попереднього догляду та рекомендацій виробника. Якщо ми бачимо особливості, які можуть вплинути
                на результат, обов&apos;язково обговорюємо їх із вами.
              </p>
            </section>

            <section id="formats" className="clients-page__section">
              <h2>Два формати чищення — відповідно до потреб ваших речей</h2>
              <p>
                Ми пропонуємо загальне потокове та індивідуальне чищення. В обох випадках технолог обирає спосіб
                обробки з урахуванням особливостей виробу.
              </p>
              <div className="clients-page__grid">
                <div>
                  <h3>Загальне потокове чищення</h3>
                  <p>Практичний варіант для повсякденних речей, які допускають стандартну професійну обробку.</p>
                  <p>
                    Речі проходять чищення у складі підібраної технологом партії. Ми групуємо їх за сумісністю кольорів,
                    складом і структурою матеріалів, рекомендаціями на маркуванні та допустимим режимом обробки.
                  </p>
                  <p>
                    Це не означає, що всі речі чистяться разом: до однієї партії потрапляють лише вироби, для яких
                    підходять сумісні умови чищення.
                  </p>
                </div>
                <div>
                  <h3>Індивідуальне чищення</h3>
                  <p>
                    Формат для речей, які потребують більше часу та окремого підходу: виробів зі складним кроєм,
                    делікатних тканин, поєднання різних матеріалів або контрастних кольорів, декоративних елементів, а
                    також окремих брендових і дизайнерських речей.
                  </p>
                  <p>
                    Технолог окремо оцінює виріб, підбирає послідовність обробки та приділяє додаткову увагу плямам,
                    деталям і ділянкам, що потребують делікатного догляду. За потреби та можливості проводяться локальні
                    проби стійкості матеріалу й барвника.
                  </p>
                </div>
              </div>
              <p>
                Індивідуальний догляд коштує дорожче, оскільки потребує більше часу та ручної роботи. Він дозволяє
                точніше врахувати особливості речі й у відповідних випадках досягти кращого результату. Водночас цей
                формат також має технологічні межі: він не гарантує видалення кожної плями або відновлення зношеного
                матеріалу.
              </p>
              <p className="clients-page__note">
                Якщо ви не впевнені, який формат обрати, ми допоможемо визначитися та погодимо з вами вартість до
                початку роботи.
              </p>
            </section>

            <section id="photos" className="clients-page__section">
              <h2>Фіксуємо стан речей, щоб усе було зрозуміло</h2>
              <p>
                Під час приймання ми фотографуємо кожну річ, фіксуємо дату, видимі плями, пошкодження та інші
                особливості. Це допомагає точніше спланувати догляд і, за потреби, порівняти стан виробу до та після
                чищення.
              </p>
              <p>
                Фотографії та відомості про замовлення використовуються для його виконання, контролю якості й розгляду
                можливих звернень. Ми забезпечуємо їх конфіденційність та обробляємо персональні дані відповідно до
                законодавства України про захист персональних даних. Доступ до цих матеріалів мають лише уповноважені
                особи в межах своїх обов&apos;язків.
              </p>
            </section>

            <section id="labels" className="clients-page__section">
              <h2>Якщо на речі немає маркування виробника</h2>
              <p>
                Бірка з рекомендаціями щодо догляду допомагає визначити допустимі способи чищення. Якщо вона відсутня,
                пошкоджена або не містить необхідної інформації, точно встановити склад матеріалу та всі обмеження
                виробника може бути складно.
              </p>
              <p>
                У такому випадку технолог оцінює виріб і, якщо його можна прийняти в роботу, обирає спосіб обробки за
                відповідною технологічною картою з урахуванням доступної інформації та результатів попередніх проб,
                коли вони можливі.
              </p>
              <p>
                Перед чищенням ми пояснюємо виявлені ризики — зокрема можливість зміни кольору, розміру, фактури або
                поведінки декоративних елементів — та погоджуємо їх із вами. Робота розпочинається після вашої згоди,
                зафіксованої в замовленні.
              </p>
              <p className="clients-page__note">
                Погодження стосується саме зазначених ризиків, пов&apos;язаних із невідомими властивостями виробу. Воно
                не звільняє нас від відповідальності за дотримання погодженої технології та належне виконання роботи.
                Якщо ризик пошкодження надто високий, ми можемо рекомендувати відмовитися від чищення.
              </p>
            </section>

            <section id="replicas" className="clients-page__section">
              <h2>Репліки та вироби з непідтвердженим складом</h2>
              <p>
                Для вибору способу чищення важливі фактичні властивості речі: склад тканини, стійкість барвника,
                якість фурнітури, покриттів і клейових з&apos;єднань.
              </p>
              <p>
                У реплік та виробів невідомого походження маркування може не відповідати реальному складу матеріалів.
                Через це складніше передбачити їхню реакцію навіть на делікатну обробку: можливі перенесення кольору,
                усадка, відшарування покриття або деформація деталей.
              </p>
              <p>
                Саме тому для таких виробів ми не можемо заздалегідь гарантувати конкретний результат чи незмінність
                усіх елементів. Можливість чищення та виявлені ризики обговорюються індивідуально до початку роботи.
              </p>
              <p>
                Наявність відомого логотипа сама по собі не визначає спосіб догляду. Ми орієнтуємося на властивості
                конкретної речі та не проводимо експертизу її автентичності.
              </p>
              <p className="clients-page__note">
                У будь-якому випадку ми зберігаємо відповідальність за належне виконання погоджених робіт і поважаємо
                ваші права як споживача.
              </p>
            </section>

            <section id="before" className="clients-page__section">
              <h2>Перед замовленням</h2>
              <p>
                Розкажіть нам про відомі пошкодження, походження плям і попередні спроби їх видалення. Повідомте, які
                засоби вже використовувалися, та покажіть рекомендації виробника щодо догляду, якщо вони збереглися.
              </p>
              <p>
                Перед початком роботи погоджуються перелік послуг, їхня вартість і строк виконання, а також виявлені
                недоліки та застереження щодо результату. Додаткові платні роботи потребують вашого погодження.
              </p>
              <h3>Перевірте результат разом із нами</h3>
              <p>
                Під час отримання речей або завершення робіт рекомендуємо оглянути результат із нашим працівником і
                повідомити про зауваження. Якщо недолік виявлено пізніше, ви також можете звернутися до нас у порядку та
                строки, передбачені законодавством.
              </p>
            </section>

            <section id="feedback" className="clients-page__section">
              <h2>Звернення, пропозиції та претензії</h2>
              <div className="clients-page__contact glass-strong">
                <p>
                  Маєте пропозицію щодо сервісу або зауваження до виконаного замовлення? Напишіть нам на електронну
                  пошту або залиште звернення в формі нижче.
                </p>
                <p className="clients-page__email">
                  <a href="mailto:office.enot24@gmail.com">office.enot24@gmail.com</a>
                </p>
                <a
                  className="btn-primary inline-flex px-5 py-2.5 no-underline text-[14px]"
                  href="mailto:office.enot24@gmail.com?subject=%D0%97%D0%B2%D0%B5%D1%80%D0%BD%D0%B5%D0%BD%D0%BD%D1%8F%20%D1%89%D0%BE%D0%B4%D0%BE%20%D0%B7%D0%B0%D0%BC%D0%BE%D0%B2%D0%BB%D0%B5%D0%BD%D0%BD%D1%8F"
                >
                  Написати нам
                </a>
              </div>
              <p className="mt-6">Щоб ми могли швидше розібратися в ситуації, додайте до звернення:</p>
              <ul>
                <li>ваше ім&apos;я та контакти для відповіді;</li>
                <li>номер і дату замовлення, якщо вони відомі;</li>
                <li>опис ситуації та вашу пропозицію або вимогу;</li>
                <li>за наявності — фотографії, квитанцію чи інші документи щодо замовлення.</li>
              </ul>
              <p>
                Кожне звернення розглядається індивідуально, з урахуванням обставин замовлення. Відповідь надається за
                вказаними вами контактами у строки, встановлені законодавством.
              </p>
            </section>

            <section id="corner" className="clients-page__section">
              <h2>Куточок споживача</h2>
              <p>
                Залиште відгук, пропозицію або претензію — ми збережемо звернення та відповімо вам. Повідомлення також
                надходить команді ЄНОТ-24.
              </p>

              <div className="clients-page__form glass-strong">
                {sent ? (
                  <div className="clients-page__form-success">
                    <h3>Дякуємо!</h3>
                    <p>Ваше звернення отримано. Ми зв&apos;яжемося з вами найближчим часом.</p>
                  </div>
                ) : (
                  <form onSubmit={(e) => void handleSubmit(e)} className="space-y-4">
                    <div className="grid sm:grid-cols-2 gap-4">
                      <div>
                        <label htmlFor="cr-name" className="cart-form-label">
                          Ім&apos;я <span className="text-[#f97171]">*</span>
                        </label>
                        <input
                          id="cr-name"
                          required
                          value={form.name}
                          onChange={(e) => setForm({ ...form, name: e.target.value })}
                          className="cart-form-input"
                          placeholder="Ваше ім'я"
                        />
                      </div>
                      <div>
                        <label htmlFor="cr-phone" className="cart-form-label">
                          Телефон <span className="text-[#f97171]">*</span>
                        </label>
                        <input
                          id="cr-phone"
                          required
                          type="tel"
                          value={form.phone}
                          onChange={(e) => setForm({ ...form, phone: formatUaPhoneInput(e.target.value) })}
                          className="cart-form-input"
                          placeholder={phoneDisplayPlaceholder}
                        />
                      </div>
                    </div>

                    <div className="grid sm:grid-cols-2 gap-4">
                      <div>
                        <label htmlFor="cr-email" className="cart-form-label">
                          Email
                        </label>
                        <input
                          id="cr-email"
                          type="email"
                          value={form.email}
                          onChange={(e) => setForm({ ...form, email: e.target.value })}
                          className="cart-form-input"
                          placeholder="email@example.com"
                        />
                      </div>
                      <div>
                        <label htmlFor="cr-order" className="cart-form-label">
                          Номер замовлення
                        </label>
                        <input
                          id="cr-order"
                          value={form.order_number}
                          onChange={(e) => setForm({ ...form, order_number: e.target.value })}
                          className="cart-form-input"
                          placeholder="Якщо відомий"
                        />
                      </div>
                    </div>

                    <div>
                      <label htmlFor="cr-type" className="cart-form-label">
                        Тип звернення
                      </label>
                      <select
                        id="cr-type"
                        value={form.type}
                        onChange={(e) => setForm({ ...form, type: e.target.value as ReviewType })}
                        className="cart-form-input"
                      >
                        <option value="review">Відгук</option>
                        <option value="suggestion">Пропозиція</option>
                        <option value="claim">Претензія</option>
                      </select>
                    </div>

                    <div>
                      <label htmlFor="cr-message" className="cart-form-label">
                        Текст звернення <span className="text-[#f97171]">*</span>
                      </label>
                      <textarea
                        id="cr-message"
                        required
                        rows={5}
                        value={form.message}
                        onChange={(e) => setForm({ ...form, message: e.target.value })}
                        className="cart-form-input cart-form-textarea"
                        placeholder="Опишіть ситуацію, відгук або пропозицію…"
                      />
                    </div>

                    {error && <p className="cart-form-error">{error}</p>}

                    <button type="submit" disabled={loading} className="btn-primary w-full sm:w-auto px-8 py-3.5">
                      {loading ? "Надсилаємо…" : "Надіслати звернення"}
                    </button>
                  </form>
                )}
              </div>
            </section>

            <section id="laws" className="clients-page__section">
              <h2>Нормативно-правові акти та стандарти</h2>
              <p>
                Ми прагнемо, щоб інформація про догляд за вашими речами та права споживача була зрозумілою й доступною.
                Нижче зібрано основні законодавчі документи та профільні стандарти у сфері професійного догляду за
                текстильними виробами.
              </p>
              <h3>Законодавство та правила обслуговування</h3>
              <ul className="clients-page__documents">
                <li>
                  <a href="https://zakon.rada.gov.ua/laws/show/1023-12#Text" target="_blank" rel="noopener noreferrer">
                    Закон України «Про захист прав споживачів»
                    <small>
                      Права споживача на інформацію про послугу, належну якість обслуговування та захист у разі
                      порушення його прав.
                    </small>
                  </a>
                </li>
                <li>
                  <a
                    href="https://zakon.rada.gov.ua/laws/show/614-2023-%D0%BF#Text"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    Правила побутового обслуговування населення
                    <small>
                      Постанова КМУ від 16.06.2023 №614. Загальні вимоги до організації побутових послуг і
                      взаємовідносин між виконавцем та замовником.
                    </small>
                  </a>
                </li>
                <li>
                  <a href="https://zakon.rada.gov.ua/laws/show/435-15#Text" target="_blank" rel="noopener noreferrer">
                    Цивільний кодекс України
                    <small>
                      Загальні положення про договірні зобов&apos;язання, виконання робіт, надання послуг і
                      відповідальність сторін.
                    </small>
                  </a>
                </li>
                <li>
                  <a
                    href="https://zakon.rada.gov.ua/laws/show/393/96-%D0%B2%D1%80#Text"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    Закон України «Про звернення громадян»
                    <small>Порядок подання та розгляду заяв, пропозицій і скарг.</small>
                  </a>
                </li>
                <li>
                  <a href="https://zakon.rada.gov.ua/laws/show/2297-17#Text" target="_blank" rel="noopener noreferrer">
                    Закон України «Про захист персональних даних»
                    <small>
                      Правила обробки та захисту персональних даних, зокрема відомостей про клієнта, пов&apos;язаних із
                      його замовленням.
                    </small>
                  </a>
                </li>
              </ul>

              <h3 className="mt-8">Профільні стандарти догляду за текстильними виробами</h3>
              <div className="clients-page__standard">
                <h3>ДСТУ EN ISO 3758:2022</h3>
                <p>
                  <strong>Маркування щодо догляду</strong>
                </p>
                <p>
                  Стандарт стосується системи символів на ярликах текстильних виробів. Такі позначення допомагають
                  визначити рекомендовані способи прання, відбілювання, сушіння, прасування та професійного чищення.
                </p>
              </div>
              <div className="clients-page__standard">
                <h3>ДСТУ EN ISO 3175-1:2022</h3>
                <p>
                  <strong>Оцінювання виробів після професійного чищення</strong>
                </p>
                <p>
                  Стандарт із серії про професійний догляд за тканинами й одягом, присвячений оцінюванню результатів
                  чищення та завершального оброблення.
                </p>
              </div>
              <div className="clients-page__standard">
                <h3>ДСТУ EN ISO 3175-2:2022</h3>
                <p>
                  <strong>Випробування під час чищення з використанням тетрахлоретену</strong>
                </p>
                <p>
                  Стосується процедур перевірки поведінки текстильних виробів під час професійного чищення та оброблення
                  із застосуванням цього розчинника.
                </p>
              </div>
              <div className="clients-page__standard">
                <h3>ДСТУ EN ISO 3175-3:2022</h3>
                <p>
                  <strong>Випробування під час чищення вуглеводневими розчинниками</strong>
                </p>
                <p>
                  Стосується процедур перевірки поведінки текстильних виробів під час чищення та оброблення із
                  застосуванням вуглеводневих розчинників.
                </p>
              </div>
              <p className="clients-page__note mt-6">
                Наведені стандарти мають різні сфери застосування. Їх перелік подано для ознайомлення: він не означає,
                що кожна річ проходить усі описані процедури або лабораторні випробування.
              </p>
              <h3>Як обирається спосіб догляду</h3>
              <p>
                Для конкретного замовлення технолог враховує маркування виробника, склад і конструкцію виробу, його
                стан, характер забруднень та погоджений формат чищення.
              </p>
              <p>
                Послідовність і режими обробки визначаються відповідними технологічними картами та робочими
                інструкціями. Особливості догляду, можливі обмеження результату та виявлені ризики погоджуються з
                клієнтом до початку роботи.
              </p>
            </section>

            <section id="rights" className="clients-page__section">
              <h2>Захист ваших прав</h2>
              <h3>Держпродспоживслужба</h3>
              <p className="clients-page__authority">
                Державна служба України з питань безпечності харчових продуктів та захисту споживачів
              </p>
              <p>
                01001, м. Київ, вул. Бориса Грінченка, 1
                <br />
                <a href="mailto:econsumer-info@dpss.gov.ua">econsumer-info@dpss.gov.ua</a>
                <br />
                <a href="tel:+380442448202">(044) 244-82-02</a> · <a href="tel:+380502300428">(050) 230-04-28</a>
              </p>
              <div className="clients-page__links">
                <a href="https://consumer.dpss.gov.ua/e-form/" target="_blank" rel="noopener noreferrer">
                  Подати електронне звернення
                </a>
                <a href="https://dpss.gov.ua/sluzhba/teritorialniorgani" target="_blank" rel="noopener noreferrer">
                  Територіальні органи
                </a>
                <a href="https://dpss.gov.ua/" target="_blank" rel="noopener noreferrer">
                  Офіційний сайт
                </a>
              </div>
              <p className="clients-page__note mt-6">
                Звернення до нас не позбавляє вас права звернутися до уповноважених державних органів або до суду.
              </p>
            </section>
          </article>
        </div>
      </div>
    </div>
  );
}

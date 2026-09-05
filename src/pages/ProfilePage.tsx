import { useState } from "react";
import { useAppNavigate } from "../lib/navigation";

const orders = [
  { id: "ЄН-2408-001", date: "18 серп. 2026", items: "Пальто жіноче, шарф", status: "completed", price: 640, badge: "Завершено" },
  { id: "ЄН-2408-002", date: "14 серп. 2026", items: "Костюм чоловічий (2 пр.)", status: "processing", price: 650, badge: "В роботі" },
  { id: "ЄН-2407-004", date: "28 лип. 2026", items: "Килим 6 м², подушки (2 шт.)", status: "completed", price: 510, badge: "Завершено" },
  { id: "ЄН-2407-001", date: "10 лип. 2026", items: "Куртка пухова", status: "completed", price: 480, badge: "Завершено" },
];

const statusColors: Record<string, { bg: string; text: string }> = {
  completed: { bg: "#E8FBF4", text: "#1A7A55" },
  processing: { bg: "#FFF9E6", text: "#B07800" },
  delivery: { bg: "#EEF3FF", text: "#4F6EF7" },
};

const tabs = ["Замовлення", "Адреси", "Налаштування"];

export default function ProfilePage() {
  const { goHome, goOrder } = useAppNavigate();
  const [activeTab, setActiveTab] = useState("Замовлення");

  return (
    <div className="min-h-screen pt-24 pb-20">
      <div className="site-container">
        <div className="grid lg:grid-cols-4 gap-8">
          {/* Sidebar */}
          <div className="lg:col-span-1">
            <div className="glass-strong rounded-[28px] p-6 mb-4">
              <div className="w-16 h-16 rounded-2xl bg-[#f97171] flex items-center justify-center text-white text-2xl font-black mb-4" >
                О
              </div>
              <div className="font-black text-[18px] text-[#1A1A2E] mb-0.5" >
                Олена Петренко
              </div>
              <div className="text-[13px] text-[#1A1A2E]/50 mb-4">+380 99 123 45 67</div>
              <div className="flex items-center gap-2 text-[12px] text-[#f97171] glass-icon rounded-xl px-3 py-2 w-fit">
                <span>⭐</span>
                <span className="font-semibold">Постійний клієнт</span>
              </div>
            </div>

            <div className="glass-strong rounded-[28px] p-3">
              {tabs.map((tab) => (
                <button
                  key={tab}
                  onClick={() => setActiveTab(tab)}
                  className={`w-full px-4 py-3 rounded-xl text-[14px] font-semibold text-left transition-all ${
                    activeTab === tab
                      ? "bg-[#f97171]/8 text-[#f97171]"
                      : "text-[#1A1A2E] hover:bg-[#faf8f6]"
                  }`}
                  
                >
                  {tab}
                </button>
              ))}
              <div className="border-t border-[#f3eeeb] mt-2 pt-2">
                <button
                  onClick={goHome}
                  className="w-full px-4 py-3 rounded-xl text-[14px] font-semibold text-left text-[#1A1A2E]/50 hover:bg-[#faf8f6] transition-all"
                  
                >
                  Вийти
                </button>
              </div>
            </div>
          </div>

          {/* Main content */}
          <div className="lg:col-span-3">
            {activeTab === "Замовлення" && (
              <div>
                <div className="flex items-center justify-between mb-6">
                  <h2 className="font-black text-[24px] text-[#1A1A2E]" >
                    Мої замовлення
                  </h2>
                  <button
                    onClick={goOrder}
                    className="btn-primary px-5 py-2.5 text-sm"
                  >
                    + Нове замовлення
                  </button>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-3 gap-4 mb-6">
                  {[
                    { v: "4", l: "Замовлень" },
                    { v: "2 280 ₴", l: "Витрачено" },
                    { v: "15%", l: "Знижка клієнта" },
                  ].map((s) => (
                    <div key={s.l} className="glass-card p-4 text-center">
                      <div className="font-black text-[22px] text-[#f97171] mb-0.5" >
                        {s.v}
                      </div>
                      <div className="text-[12px] text-[#1A1A2E]/50 font-medium">{s.l}</div>
                    </div>
                  ))}
                </div>

                <div className="space-y-3">
                  {orders.map((order) => (
                    <div
                      key={order.id}
                      className="glass-card p-5 hover:border-[#f97171]/20 transition-colors"
                    >
                      <div className="flex items-start justify-between mb-2">
                        <div>
                          <div className="flex items-center gap-2 mb-1">
                            <span className="font-bold text-[13px] text-[#1A1A2E]/40" >
                              #{order.id}
                            </span>
                            <span
                              className="text-[11px] font-semibold px-2.5 py-0.5 rounded-full"
                              style={{ background: statusColors[order.status].bg, color: statusColors[order.status].text }}
                            >
                              {order.badge}
                            </span>
                          </div>
                          <div className="font-semibold text-[14px] text-[#1A1A2E]" >
                            {order.items}
                          </div>
                        </div>
                        <div className="text-right">
                          <div className="font-black text-[18px] text-[#f97171]" >
                            {order.price} ₴
                          </div>
                          <div className="text-[12px] text-[#1A1A2E]/40">{order.date}</div>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {activeTab === "Адреси" && (
              <div>
                <h2 className="font-black text-[24px] text-[#1A1A2E] mb-6" >
                  Збережені адреси
                </h2>
                <div className="space-y-4">
                  {[
                    { label: "Вдома", address: "вул. Хрещатик, 15, кв. 32, Київ", default: true },
                    { label: "Офіс", address: "вул. Велика Васильківська, 72, оф. 4, Київ", default: false },
                  ].map((addr) => (
                    <div key={addr.label} className="glass-card p-5 flex items-start justify-between">
                      <div className="flex items-start gap-3">
                        <div className="w-10 h-10 rounded-xl glass-icon flex items-center justify-center text-xl shrink-0">
                          {addr.label === "Вдома" ? "🏠" : "🏢"}
                        </div>
                        <div>
                          <div className="font-bold text-[14px] text-[#1A1A2E] flex items-center gap-2 mb-0.5" >
                            {addr.label}
                            {addr.default && (
                              <span className="text-[11px] bg-[#E8FBF4] text-[#1A7A55] px-2 py-0.5 rounded-full font-semibold">
                                За замовч.
                              </span>
                            )}
                          </div>
                          <div className="text-[13px] text-[#1A1A2E]/55">{addr.address}</div>
                        </div>
                      </div>
                      <button className="text-[12px] text-[#f97171] font-semibold hover:underline">
                        Редагувати
                      </button>
                    </div>
                  ))}
                  <button className="w-full py-4 rounded-2xl border-2 border-dashed border-[#f3eeeb] text-[14px] font-semibold text-[#1A1A2E]/40 hover:border-[#f97171]/30 hover:text-[#f97171] transition-all" >
                    + Додати адресу
                  </button>
                </div>
              </div>
            )}

            {activeTab === "Налаштування" && (
              <div>
                <h2 className="font-black text-[24px] text-[#1A1A2E] mb-6" >
                  Налаштування профілю
                </h2>
                <div className="glass-strong rounded-[28px] p-6 space-y-5">
                  {[
                    { label: "Ім'я та прізвище", value: "Олена Петренко", type: "text" },
                    { label: "Телефон", value: "+380 99 123 45 67", type: "tel" },
                    { label: "Email", value: "o.petrenko@gmail.com", type: "email" },
                  ].map((field) => (
                    <div key={field.label}>
                      <label className="block text-[13px] font-semibold text-[#1A1A2E]/60 mb-2">{field.label}</label>
                      <input
                        type={field.type}
                        defaultValue={field.value}
                        className="w-full px-4 py-3.5 rounded-2xl border border-[#f3eeeb] bg-[#faf8f6] text-[#1A1A2E] text-[14px] focus:outline-none focus:border-[#f97171] focus:ring-2 focus:ring-[#f97171]/20 transition-all"
                      />
                    </div>
                  ))}
                  <button className="btn-primary px-8 py-3.5 text-sm w-full sm:w-auto">
                    Зберегти зміни
                  </button>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

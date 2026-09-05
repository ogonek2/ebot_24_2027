import logo from "@/logo/logo.svg";
import logoWh from "@/logo/logo_wh.svg";

interface BrandMarkProps {
  size?: number;
  className?: string;
  variant?: "light" | "dark";
  showTagline?: boolean;
}

export default function BrandMark({
  size = 42,
  className = "",
  variant = "light",
  showTagline = true,
}: BrandMarkProps) {
  const isDark = variant === "dark";

  return (
    <div className={`flex items-center gap-2.5 ${className}`}>
      <img
        src={isDark ? logoWh : logo}
        alt="ЄНОТ 24"
        width={size}
        height={size}
        style={{ width: size, height: size, objectFit: "contain" }}
        draggable={false}
      />
      <div className="flex flex-col leading-none text-left">
        <span
          className={`font-display text-[20px] xl:text-[22px] tracking-tight ${
            isDark ? "text-white" : "text-[#1A1A2E]"
          }`}
          style={{ fontWeight: 900 }}
        >
          ЄНОТ 24
        </span>
        {showTagline && (
          <span
            className={`text-[11px] font-semibold tracking-widest uppercase ${
              isDark ? "text-white/70" : "text-[#f97171]"
            }`}
          >
            Хімчистка
          </span>
        )}
      </div>
    </div>
  );
}

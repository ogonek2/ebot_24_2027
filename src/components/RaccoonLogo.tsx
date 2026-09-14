import { useBootstrap } from "@/context/BootstrapContext";
import { resolveStorageUrl } from "@/lib/assets";
import logo from "@/logo/logo.svg";
import logoWh from "@/logo/logo_wh.svg";

interface RaccoonLogoProps {
  size?: number;
  className?: string;
  variant?: "default" | "white";
}

export default function RaccoonLogo({
  size = 40,
  className = "",
  variant = "default",
}: RaccoonLogoProps) {
  const { assets } = useBootstrap();
  const remote =
    variant === "white"
      ? resolveStorageUrl(assets?.logoFull)
      : resolveStorageUrl(assets?.logo);
  const src = remote ?? (variant === "white" ? logoWh : logo);

  return (
    <img
      src={src}
      alt="ЄНОТ 24"
      width={size}
      height={size}
      className={className}
      style={{ width: size, height: size, objectFit: "contain" }}
      draggable={false}
    />
  );
}

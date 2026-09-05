import { Link } from "react-router-dom";
import { ROUTES } from "@/lib/routes";
import { openFeedbackModal } from "@/context/FeedbackContext";
import { useBootstrap } from "@/context/BootstrapContext";
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
  const src =
    variant === "white"
      ? assets?.logoFull ?? logoWh
      : assets?.logo ?? logo;

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

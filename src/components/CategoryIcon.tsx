import { resolveStorageUrl } from "@/lib/assets";
import { icons, type IconName } from "@/storage/icons";

interface CategoryIconProps {
  /** Stock PNG fallback — only for hardcoded marketing blocks (`frontend/src/storage/icons`). */
  name?: IconName;
  /** URL from API: CTA `iconUrl` (icons table) or category `category_img`. */
  src?: string | null;
  size?: number;
  className?: string;
  alt?: string;
  /** When true, use bundled PNG if `src` is missing. Default false for API-driven UI. */
  fallback?: boolean;
}

export default function CategoryIcon({
  name = "tshirt",
  src,
  size = 40,
  className = "",
  alt = "",
  fallback = false,
}: CategoryIconProps) {
  const resolvedSrc = resolveStorageUrl(src);
  const imageSrc = resolvedSrc || (fallback && name ? icons[name] : null);

  if (!imageSrc) {
    return (
      <span
        className={`inline-block rounded-full bg-[#1A1A2E]/06 ${className}`}
        style={{ width: size, height: size }}
        aria-hidden={!alt}
        title={alt || undefined}
      />
    );
  }

  return (
    <img
      src={imageSrc}
      alt={alt}
      width={size}
      height={size}
      className={className}
      style={{ width: size, height: size, objectFit: "contain" }}
      draggable={false}
      loading="lazy"
    />
  );
}

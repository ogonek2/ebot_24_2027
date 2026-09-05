type SkeletonProps = {
  className?: string;
  rounded?: "md" | "lg" | "xl" | "2xl" | "3xl" | "full";
};

const roundedMap = {
  md: "rounded-xl",
  lg: "rounded-2xl",
  xl: "rounded-[20px]",
  "2xl": "rounded-[28px]",
  "3xl": "rounded-[32px]",
  full: "rounded-full",
};

export default function Skeleton({ className = "", rounded = "2xl" }: SkeletonProps) {
  return (
    <div
      className={`skeleton-glass ${roundedMap[rounded]} ${className}`}
      aria-hidden="true"
    />
  );
}

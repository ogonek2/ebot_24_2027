import type { CareSymbolKind } from "./careSymbols";

type Props = {
  kind: CareSymbolKind;
  className?: string;
};

/** Спрощені ISO 3758 — чорні піктограми на світлому полі */
export default function CareSymbolIcon({ kind, className = "" }: Props) {
  return (
    <svg
      viewBox="0 0 48 48"
      className={className}
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      aria-hidden
    >
      {kind === "dryclean-p" && (
        <>
          <circle cx="24" cy="24" r="18" />
          <text x="24" y="30" textAnchor="middle" fill="currentColor" stroke="none" fontSize="16" fontWeight="800">
            P
          </text>
        </>
      )}
      {kind === "dryclean-f" && (
        <>
          <circle cx="24" cy="24" r="18" />
          <text x="24" y="30" textAnchor="middle" fill="currentColor" stroke="none" fontSize="16" fontWeight="800">
            F
          </text>
        </>
      )}
      {kind === "wash-30" && (
        <>
          <path d="M10 14h28l-3 26H13L10 14z" />
          <path d="M16 14c0-4 3-7 8-7s8 3 8 7" />
          <text x="24" y="34" textAnchor="middle" fill="currentColor" stroke="none" fontSize="11" fontWeight="800">
            30
          </text>
        </>
      )}
      {kind === "wash-hand" && (
        <>
          <path d="M10 14h28l-3 26H13L10 14z" />
          <path d="M16 14c0-4 3-7 8-7s8 3 8 7" />
          <path d="M28 22v8M24 26h8" strokeWidth="2.5" />
        </>
      )}
      {kind === "no-wash" && (
        <>
          <path d="M10 14h28l-3 26H13L10 14z" />
          <path d="M16 14c0-4 3-7 8-7s8 3 8 7" />
          <path d="M14 12l20 28" strokeWidth="2.5" />
        </>
      )}
      {kind === "bleach" && <path d="M24 8 L38 38 H10 Z" />}
      {kind === "no-bleach" && (
        <>
          <path d="M24 8 L38 38 H10 Z" />
          <path d="M14 14l20 22" strokeWidth="2.5" />
        </>
      )}
      {kind === "iron-low" && (
        <>
          <path d="M8 32h28l-4-12a6 6 0 0 0-5.5-4H17a6 6 0 0 0-5.5 4L8 32z" />
          <circle cx="20" cy="34" r="1.5" fill="currentColor" stroke="none" />
        </>
      )}
      {kind === "iron-medium" && (
        <>
          <path d="M8 32h28l-4-12a6 6 0 0 0-5.5-4H17a6 6 0 0 0-5.5 4L8 32z" />
          <circle cx="18" cy="34" r="1.5" fill="currentColor" stroke="none" />
          <circle cx="22" cy="34" r="1.5" fill="currentColor" stroke="none" />
        </>
      )}
      {kind === "no-iron" && (
        <>
          <path d="M8 32h28l-4-12a6 6 0 0 0-5.5-4H17a6 6 0 0 0-5.5 4L8 32z" />
          <path d="M10 12l28 28" strokeWidth="2.5" />
        </>
      )}
      {kind === "tumble-dry" && (
        <>
          <rect x="10" y="10" width="28" height="28" rx="3" />
          <circle cx="24" cy="24" r="8" />
          <circle cx="24" cy="24" r="2" fill="currentColor" stroke="none" />
        </>
      )}
      {kind === "no-tumble" && (
        <>
          <rect x="10" y="10" width="28" height="28" rx="3" />
          <circle cx="24" cy="24" r="8" />
          <path d="M14 14l20 20" strokeWidth="2.5" />
        </>
      )}
      {kind === "dry-flat" && (
        <>
          <rect x="10" y="10" width="28" height="28" rx="3" />
          <path d="M14 28h20M14 32h20M14 36h16" />
        </>
      )}
    </svg>
  );
}

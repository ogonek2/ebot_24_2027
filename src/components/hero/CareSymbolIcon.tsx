import type { ReactNode } from "react";
import type { CareSymbolKind } from "./careSymbols";

type Props = {
  kind: CareSymbolKind;
  className?: string;
};

function WashBasin({ children }: { children?: ReactNode }) {
  return (
    <>
      <path d="M10 14h28l-3 26H13L10 14z" />
      <path d="M16 14c0-4 3-7 8-7s8 3 8 7" />
      {children}
    </>
  );
}

function IronBase({ children }: { children?: ReactNode }) {
  return (
    <>
      <path d="M8 32h28l-4-12a6 6 0 0 0-5.5-4H17a6 6 0 0 0-5.5 4L8 32z" />
      {children}
    </>
  );
}

function CircleLetter({ letter, underline = false }: { letter: string; underline?: boolean }) {
  return (
    <>
      <circle cx="24" cy="24" r="18" />
      <text x="24" y="30" textAnchor="middle" fill="currentColor" stroke="none" fontSize="16" fontWeight="800">
        {letter}
      </text>
      {underline && <path d="M16 36h16" strokeWidth="2.2" />}
    </>
  );
}

function TumbleSquare({ children }: { children?: ReactNode }) {
  return (
    <>
      <rect x="10" y="10" width="28" height="28" rx="3" />
      <circle cx="24" cy="24" r="8" />
      {children}
    </>
  );
}

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
      {kind === "dryclean-p" && <CircleLetter letter="P" />}
      {kind === "dryclean-p-mild" && <CircleLetter letter="P" underline />}
      {kind === "dryclean-f" && <CircleLetter letter="F" />}
      {kind === "dryclean-f-mild" && <CircleLetter letter="F" underline />}
      {kind === "dryclean-a" && <CircleLetter letter="A" />}
      {kind === "no-dryclean" && (
        <>
          <circle cx="24" cy="24" r="18" />
          <path d="M12 12l24 24" strokeWidth="2.5" />
        </>
      )}
      {kind === "wet-clean" && <CircleLetter letter="W" />}
      {kind === "wet-clean-mild" && <CircleLetter letter="W" underline />}
      {kind === "no-wet-clean" && (
        <>
          <circle cx="24" cy="24" r="18" />
          <text x="24" y="30" textAnchor="middle" fill="currentColor" stroke="none" fontSize="16" fontWeight="800">
            W
          </text>
          <path d="M12 12l24 24" strokeWidth="2.5" />
        </>
      )}
      {kind === "wash-30" && (
        <WashBasin>
          <text x="24" y="34" textAnchor="middle" fill="currentColor" stroke="none" fontSize="11" fontWeight="800">
            30
          </text>
        </WashBasin>
      )}
      {kind === "wash-30-mild" && (
        <WashBasin>
          <text x="24" y="32" textAnchor="middle" fill="currentColor" stroke="none" fontSize="11" fontWeight="800">
            30
          </text>
          <path d="M16 38h16" strokeWidth="2" />
        </WashBasin>
      )}
      {kind === "wash-40" && (
        <WashBasin>
          <text x="24" y="34" textAnchor="middle" fill="currentColor" stroke="none" fontSize="11" fontWeight="800">
            40
          </text>
        </WashBasin>
      )}
      {kind === "wash-60" && (
        <WashBasin>
          <text x="24" y="34" textAnchor="middle" fill="currentColor" stroke="none" fontSize="11" fontWeight="800">
            60
          </text>
        </WashBasin>
      )}
      {kind === "wash-95" && (
        <WashBasin>
          <text x="24" y="34" textAnchor="middle" fill="currentColor" stroke="none" fontSize="11" fontWeight="800">
            95
          </text>
        </WashBasin>
      )}
      {kind === "wash-hand" && (
        <WashBasin>
          <path d="M28 22v8M24 26h8" strokeWidth="2.5" />
        </WashBasin>
      )}
      {kind === "no-wash" && (
        <WashBasin>
          <path d="M14 12l20 28" strokeWidth="2.5" />
        </WashBasin>
      )}
      {kind === "bleach" && <path d="M24 8 L38 38 H10 Z" />}
      {kind === "bleach-oxygen" && (
        <>
          <path d="M24 8 L38 38 H10 Z" />
          <path d="M18 30h12M20 26h8M22 22h4" />
        </>
      )}
      {kind === "no-bleach" && (
        <>
          <path d="M24 8 L38 38 H10 Z" />
          <path d="M14 14l20 22" strokeWidth="2.5" />
        </>
      )}
      {kind === "iron-low" && (
        <IronBase>
          <circle cx="20" cy="34" r="1.5" fill="currentColor" stroke="none" />
        </IronBase>
      )}
      {kind === "iron-medium" && (
        <IronBase>
          <circle cx="18" cy="34" r="1.5" fill="currentColor" stroke="none" />
          <circle cx="22" cy="34" r="1.5" fill="currentColor" stroke="none" />
        </IronBase>
      )}
      {kind === "iron-high" && (
        <IronBase>
          <circle cx="16" cy="34" r="1.5" fill="currentColor" stroke="none" />
          <circle cx="20" cy="34" r="1.5" fill="currentColor" stroke="none" />
          <circle cx="24" cy="34" r="1.5" fill="currentColor" stroke="none" />
        </IronBase>
      )}
      {kind === "no-iron" && (
        <IronBase>
          <path d="M10 12l28 28" strokeWidth="2.5" />
        </IronBase>
      )}
      {kind === "tumble-dry" && (
        <TumbleSquare>
          <circle cx="24" cy="24" r="2" fill="currentColor" stroke="none" />
        </TumbleSquare>
      )}
      {kind === "tumble-low" && (
        <TumbleSquare>
          <circle cx="24" cy="24" r="2" fill="currentColor" stroke="none" />
          <path d="M20 36h8" strokeWidth="2" />
        </TumbleSquare>
      )}
      {kind === "tumble-high" && (
        <TumbleSquare>
          <circle cx="24" cy="24" r="2" fill="currentColor" stroke="none" />
          <path d="M18 35h12M20 38h8" strokeWidth="1.8" />
        </TumbleSquare>
      )}
      {kind === "no-tumble" && (
        <TumbleSquare>
          <path d="M14 14l20 20" strokeWidth="2.5" />
        </TumbleSquare>
      )}
      {kind === "line-dry" && (
        <>
          <rect x="10" y="10" width="28" height="28" rx="3" />
          <path d="M14 18h20M24 18v16" />
        </>
      )}
      {kind === "drip-dry" && (
        <>
          <rect x="10" y="10" width="28" height="28" rx="3" />
          <path d="M14 16h20M24 16v10" />
          <path d="M20 30c0 2 4 2 4 0s-2-4-2-4-2 2-2 4z" fill="currentColor" stroke="none" />
        </>
      )}
      {kind === "dry-flat" && (
        <>
          <rect x="10" y="10" width="28" height="28" rx="3" />
          <path d="M14 28h20M14 32h20M14 36h16" />
        </>
      )}
      {kind === "dry-shade" && (
        <>
          <rect x="10" y="10" width="28" height="28" rx="3" />
          <path d="M14 18h20M24 18v16" />
          <path d="M32 14c3 2 3 6 0 8" />
        </>
      )}
    </svg>
  );
}

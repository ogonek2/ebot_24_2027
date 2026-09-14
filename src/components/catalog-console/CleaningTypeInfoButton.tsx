import { useEffect, useId, useRef, useState } from "react";
import { createPortal } from "react-dom";
import {
  CLEANING_TYPE_INFO,
  type CleaningTypeInfoKey,
} from "./cleaningTypeInfo";

type Props = {
  type: CleaningTypeInfoKey;
  /** Align header label + icon */
  align?: "start" | "end";
  className?: string;
};

export default function CleaningTypeInfoButton({ type, align = "end", className = "" }: Props) {
  const info = CLEANING_TYPE_INFO[type];
  const [open, setOpen] = useState(false);
  const titleId = useId();
  const closeRef = useRef<HTMLButtonElement>(null);

  useEffect(() => {
    if (!open) return;
    const prev = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    closeRef.current?.focus();

    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") setOpen(false);
    };
    window.addEventListener("keydown", onKey);
    return () => {
      document.body.style.overflow = prev;
      window.removeEventListener("keydown", onKey);
    };
  }, [open]);

  return (
    <>
      <span className={`cc-clean-info ${align === "end" ? "cc-clean-info--end" : ""} ${className}`}>
        <span className="cc-clean-info__label">{info.shortLabel}</span>
        <button
          type="button"
          className="cc-clean-info__btn"
          aria-label={`Що таке ${info.title.toLowerCase()}?`}
          aria-haspopup="dialog"
          aria-expanded={open}
          title="Детальніше"
          onClick={(e) => {
            e.preventDefault();
            e.stopPropagation();
            setOpen(true);
          }}
        >
          <InfoIcon />
        </button>
      </span>

      {open &&
        createPortal(
          <div
            className="cc-clean-popup"
            role="presentation"
            onClick={() => setOpen(false)}
          >
            <div
              className="cc-clean-popup__dialog glass-strong"
              role="dialog"
              aria-modal="true"
              aria-labelledby={titleId}
              onClick={(e) => e.stopPropagation()}
            >
              <button
                ref={closeRef}
                type="button"
                className="cc-clean-popup__close"
                aria-label="Закрити"
                onClick={() => setOpen(false)}
              >
                ×
              </button>

              <div className="cc-clean-popup__badge">Довідка</div>
              <h2 id={titleId} className="cc-clean-popup__title">
                {info.title}
              </h2>

              <div className="cc-clean-popup__body cc-scroll">
                <p className="cc-clean-popup__lead">{info.lead}</p>
                {info.body.map((p) => (
                  <p key={p.slice(0, 48)} className="cc-clean-popup__text">
                    {p}
                  </p>
                ))}
                {info.includes && info.includes.length > 0 && (
                  <>
                    <p className="cc-clean-popup__subtitle">Послуга включає:</p>
                    <ul className="cc-clean-popup__list">
                      {info.includes.map((item) => (
                        <li key={item}>{item}</li>
                      ))}
                    </ul>
                  </>
                )}
                <p className="cc-clean-popup__text">{info.recommended}</p>
              </div>

              <button type="button" className="cc-clean-popup__ok btn-primary" onClick={() => setOpen(false)}>
                Зрозуміло
              </button>
            </div>
          </div>,
          document.body,
        )}
    </>
  );
}

function InfoIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden>
      <circle cx="12" cy="12" r="9" stroke="currentColor" strokeWidth="2.2" />
      <path d="M12 10.5v6" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" />
      <circle cx="12" cy="7.4" r="1.25" fill="currentColor" />
    </svg>
  );
}

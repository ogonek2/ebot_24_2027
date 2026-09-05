const DIGIT_SLOTS = 9;

/** Extract up to 9 national digits (without country code). */
export function extractUaPhoneDigits(raw: string): string {
  let digits = raw.replace(/\D/g, "");
  if (digits.startsWith("380")) digits = digits.slice(3);
  else if (digits.startsWith("80")) digits = digits.slice(2);
  else if (digits.startsWith("0")) digits = digits.slice(1);
  return digits.slice(0, DIGIT_SLOTS);
}

/** Format as +380 (XX) XXX-XX-XX — same mask as Laravel inputmask. */
export function formatUaPhone(digits: string): string {
  const d = digits.slice(0, DIGIT_SLOTS);
  if (!d.length) return "+380 (";

  let out = "+380 (";
  out += d.slice(0, 2);
  if (d.length <= 2) return out;

  out += ") " + d.slice(2, 5);
  if (d.length <= 5) return out;

  out += "-" + d.slice(5, 7);
  if (d.length <= 7) return out;

  out += "-" + d.slice(7, 9);
  return out;
}

export function formatUaPhoneInput(raw: string): string {
  return formatUaPhone(extractUaPhoneDigits(raw));
}

export function isUaPhoneComplete(value: string): boolean {
  return /^\+380 \(\d{2}\) \d{3}-\d{2}-\d{2}$/.test(value);
}

export function phoneDisplayPlaceholder(): string {
  return "+380 (__) ___-__-__";
}

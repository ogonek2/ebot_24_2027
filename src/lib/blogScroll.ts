const BLOG_SCROLL_KEY = "enot:blog-list-scroll";

export function saveBlogListScroll() {
  sessionStorage.setItem(BLOG_SCROLL_KEY, String(window.scrollY));
}

export function peekBlogListScroll(): number | null {
  const raw = sessionStorage.getItem(BLOG_SCROLL_KEY);
  if (raw === null) return null;
  const y = Number(raw);
  return Number.isFinite(y) ? y : null;
}

export function consumeBlogListScroll(): number | null {
  const y = peekBlogListScroll();
  if (y !== null) sessionStorage.removeItem(BLOG_SCROLL_KEY);
  return y;
}

export function clearBlogListScroll() {
  sessionStorage.removeItem(BLOG_SCROLL_KEY);
}

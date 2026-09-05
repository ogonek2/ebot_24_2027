import { useEffect, useLayoutEffect, useRef } from "react";
import { useLocation } from "react-router-dom";
import { clearBlogListScroll } from "@/lib/blogScroll";
import { ROUTES } from "@/lib/routes";

function isBlogPostPath(pathname: string) {
  return pathname.startsWith("/blog/") && pathname !== ROUTES.blog;
}

function scrollToHash(hash: string) {
  const id = hash.replace(/^#/, "");
  if (!id) return;
  requestAnimationFrame(() => {
    document.getElementById(id)?.scrollIntoView({ behavior: "smooth" });
  });
}

export default function ScrollManager() {
  const location = useLocation();
  const prevPathRef = useRef(location.pathname);

  useEffect(() => {
    if ("scrollRestoration" in history) {
      history.scrollRestoration = "manual";
    }
  }, []);

  useLayoutEffect(() => {
    const prev = prevPathRef.current;
    const curr = location.pathname;
    prevPathRef.current = curr;

    if (location.hash) {
      scrollToHash(location.hash);
      return;
    }

    const enteringBlogList = curr === ROUTES.blog;
    const leavingBlogListForPost = prev === ROUTES.blog && isBlogPostPath(curr);
    const returningToBlogList = enteringBlogList && isBlogPostPath(prev);

    if (returningToBlogList) {
      return;
    }

    if (leavingBlogListForPost || isBlogPostPath(curr)) {
      window.scrollTo(0, 0);
      return;
    }

    if (enteringBlogList) {
      clearBlogListScroll();
    }

    window.scrollTo(0, 0);
  }, [location.pathname, location.hash, location.key]);

  return null;
}

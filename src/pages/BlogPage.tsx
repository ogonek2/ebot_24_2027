import { useEffect, useMemo } from "react";
import { Link } from "react-router-dom";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import { BlogFeaturedPost, BlogListRow } from "@/components/blog/BlogList";
import PageSkeleton from "@/components/skeleton/PageSkeleton";
import { useBootstrap } from "@/context/BootstrapContext";
import { fetchBlogAllCached } from "@/lib/api";
import type { BlogPostListItem } from "@/lib/blog";
import { consumeBlogListScroll } from "@/lib/blogScroll";
import { useCachedQuery } from "@/lib/useCachedQuery";

export default function BlogPage() {
  const bootstrap = useBootstrap();
  const { data, loading, error } = useCachedQuery("api:blog:all", () => fetchBlogAllCached());

  const posts = useMemo<BlogPostListItem[]>(() => {
    if (data?.data?.length) return data.data;
    return bootstrap.blogPosts ?? [];
  }, [data?.data, bootstrap.blogPosts]);

  const [featured, ...rest] = posts;
  const showSkeleton = loading && posts.length === 0;
  const showEmpty = !loading && posts.length === 0;

  useEffect(() => {
    if (showSkeleton) return;
    const y = consumeBlogListScroll();
    if (y === null) return;

    const restore = () => window.scrollTo(0, y);
    restore();
    requestAnimationFrame(restore);
  }, [showSkeleton, posts.length]);

  if (showSkeleton) {
    return <PageSkeleton cards={6} />;
  }

  return (
    <div className="min-h-screen pt-24 pb-20 blog-page">
      <div className="site-container">
        <Breadcrumbs items={[homeCrumb(), { name: "Блог" }]} />

        <header className="blog-page__header">
          <h1 className="text-section text-[#1A1A2E] mb-2">Блог</h1>
          <p className="text-[16px] text-[#1A1A2E]/55">Догляд за речами</p>
        </header>

        {showEmpty ? (
          <div className="blog-shell blog-shell--empty">
            <p className="text-[#1A1A2E]/55 text-center py-8">
              {error ? "Не вдалося завантажити статті. Спробуйте оновити сторінку." : "Статей поки немає"}
            </p>
          </div>
        ) : (
          <div className="blog-shell">
            {featured && <BlogFeaturedPost post={featured} />}
            {rest.length > 0 && (
              <>
                <div className="blog-shell__divider" />
                <div className="blog-list">
                  {rest.map((post) => (
                    <BlogListRow key={post.slug} post={post} />
                  ))}
                </div>
              </>
            )}
            {loading && posts.length > 0 && (
              <p className="blog-shell__updating">Оновлюємо список…</p>
            )}
          </div>
        )}
      </div>
    </div>
  );
}

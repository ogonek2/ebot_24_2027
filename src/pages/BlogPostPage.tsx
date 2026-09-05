import { Link, useParams } from "react-router-dom";
import Breadcrumbs, { homeCrumb } from "@/components/Breadcrumbs";
import DetailSkeleton from "@/components/skeleton/DetailSkeleton";
import { fetchBlogPostCached } from "@/lib/api";
import { formatBlogDateLong } from "@/lib/blog";
import { useCachedQuery } from "@/lib/useCachedQuery";
import { ROUTES, blogPostUrl, serviceUrl } from "@/lib/routes";

type PostData = {
  slug: string;
  title: string;
  publishedAt?: string | null;
  image?: string | null;
  content?: string;
  excerpt?: string | null;
};

type BlogPostResponse = {
  post?: PostData | null;
  relatedPosts?: PostData[];
  relatedServices?: Array<{ id: number; name: string; href?: string; categoryHref?: string }>;
};

export default function BlogPostPage() {
  const { slug = "" } = useParams();
  if (!slug) {
    return <div className="site-container py-24 text-center text-[#1A1A2E]/45">Статтю не знайдено</div>;
  }
  return <BlogPostContent slug={slug} />;
}

function BlogPostContent({ slug }: { slug: string }) {
  const { data, loading, error } = useCachedQuery<BlogPostResponse>(
    `api:blog:post:${slug}`,
    () => fetchBlogPostCached(slug),
  );

  const post = data?.post ?? null;
  const relatedPosts = data?.relatedPosts ?? [];
  const relatedServices = data?.relatedServices ?? [];

  if (loading && !post) return <DetailSkeleton />;
  if (!post) {
    return <div className="site-container py-24 text-center text-[#1A1A2E]/45">{error ?? "Статтю не знайдено"}</div>;
  }

  return (
    <div className="min-h-screen pt-24 pb-20 blog-post-page">
      <div className="site-container">
        <Breadcrumbs items={[homeCrumb(), { name: "Блог", url: ROUTES.blog }, { name: post.title }]} />

        <div className="flex flex-col lg:flex-row gap-8">
          <article className="blog-article font-reading">
            <p className="blog-article__eyebrow">«Блог»</p>
            {post.publishedAt && (
              <time className="blog-article__date">{formatBlogDateLong(post.publishedAt)}</time>
            )}
            <h1 className="blog-article__title">{post.title}</h1>
            {post.excerpt && <p className="blog-article__lead">{post.excerpt}</p>}

            {post.image && (
              <figure className="blog-article__figure">
                <img src={post.image} alt="" className="blog-article__image" />
              </figure>
            )}

            <div className="blog-article__content rich-text-content" dangerouslySetInnerHTML={{ __html: post.content ?? "" }} />
          </article>

          {relatedPosts.length > 0 && (
            <aside className="blog-related">
              <h2 className="blog-related__title">Інші матеріали</h2>
              <ol className="blog-related__list">
                {relatedPosts.map((p) => (
                  <li key={p.slug}>
                    <Link to={blogPostUrl(p.slug)} className="blog-related__link no-underline group">
                      {p.title}
                    </Link>
                  </li>
                ))}
              </ol>
              {relatedServices.length > 0 && (
                <div className="blog-article__services">
                  <span className="blog-article__services-label">Пов&apos;язані послуги</span>
                  <div className="blog-article__services-links">
                    {relatedServices.map((s, i) => (
                      <span key={s.id} className="inline-flex items-center gap-2">
                        {i > 0 && <span className="text-[#1A1A2E]/25">·</span>}
                        {s.href && s.categoryHref ? (
                          <Link to={serviceUrl(s.categoryHref, s.href)} className="blog-article__service-link no-underline">
                            {s.name}
                          </Link>
                        ) : (
                          <span>{s.name}</span>
                        )}
                      </span>
                    ))}
                  </div>
                </div>
              )}
            </aside>
          )}
        </div>
      </div>
    </div>
  );
}

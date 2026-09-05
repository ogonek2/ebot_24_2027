import { Link } from "react-router-dom";
import type { BlogPostListItem } from "@/lib/blog";
import { formatBlogDateCompact, formatBlogDateShort } from "@/lib/blog";
import { blogPostUrl } from "@/lib/routes";

type Props = {
  post: BlogPostListItem;
};

export function BlogFeaturedPost({ post }: Props) {
  return (
    <Link to={blogPostUrl(post.slug)} className="blog-featured no-underline group">
      {post.image && (
        <div className="blog-featured__media">
          <img src={post.image} alt="" className="blog-featured__img" />
        </div>
      )}
      <div className="blog-featured__body">
        {post.publishedAt && (
          <time className="blog-featured__date">{formatBlogDateShort(post.publishedAt)}</time>
        )}
        <h2 className="blog-featured__title">{post.title}</h2>
        {post.excerpt && <p className="blog-featured__excerpt">{post.excerpt}</p>}
        <span className="blog-featured__cta">
          Читати
          <ArrowIcon />
        </span>
      </div>
    </Link>
  );
}

export function BlogListRow({ post }: Props) {
  return (
    <Link to={blogPostUrl(post.slug)} className="blog-row no-underline group">
      {post.publishedAt && (
        <time className="blog-row__date">{formatBlogDateCompact(post.publishedAt)}</time>
      )}
      <div className="blog-row__main">
        {post.image && (
          <img src={post.image} alt="" className="blog-row__thumb" loading="lazy" />
        )}
        <h3 className="blog-row__title">{post.title}</h3>
      </div>
      <span className="blog-row__cta">Читати</span>
    </Link>
  );
}

function ArrowIcon() {
  return (
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" aria-hidden>
      <path d="M5 12h14M13 6l6 6-6 6" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

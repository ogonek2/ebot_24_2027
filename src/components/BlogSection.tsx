import { Link } from "react-router-dom";
import Reveal from "./Reveal";
import { useBootstrap } from "@/context/BootstrapContext";
import { formatBlogDateLong } from "@/lib/blog";
import { ROUTES, blogPostUrl } from "@/lib/routes";

export default function BlogSection() {
  const { blogPosts = [] } = useBootstrap();
  if (!blogPosts.length) return null;

  const featured = blogPosts.slice(0, 2);

  return (
    <section className="blog-home-section py-16 sm:py-20" id="blog">
      <div className="site-container">
        <Reveal>
          <h2 className="blog-home-section__title">Блог</h2>
        </Reveal>

        <div className="blog-home-grid">
          {featured.map((post, i) => (
            <Reveal key={post.slug} delay={i * 60}>
              <Link to={blogPostUrl(post.slug)} className="blog-home-card no-underline group">
                {post.image && (
                  <div className="blog-home-card__media">
                    <img src={post.image} alt="" className="blog-home-card__img" loading="lazy" />
                  </div>
                )}
                <div className="blog-home-card__body">
                  <h3 className="blog-home-card__title">{post.title}</h3>
                  {post.publishedAt && (
                    <time className="blog-home-card__date">{formatBlogDateLong(post.publishedAt)}</time>
                  )}
                </div>
              </Link>
            </Reveal>
          ))}
        </div>

        <div className="blog-home-more">
          <Link to={ROUTES.blog} className="blog-home-more__link no-underline">
            Усі матеріали
          </Link>
        </div>
      </div>
    </section>
  );
}

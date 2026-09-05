import { Link } from "react-router-dom";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import { useBootstrap } from "@/context/BootstrapContext";
import { fetchBlogAllCached } from "@/lib/api";
import { formatBlogDateShort, type BlogPostListItem } from "@/lib/blog";
import { useCachedQuery } from "@/lib/useCachedQuery";
import { ROUTES, blogPostUrl } from "@/lib/routes";

import "swiper/css";
import "swiper/css/pagination";

const MAX_POSTS = 8;

export default function BlogRelatedSwiper() {
  const { blogPosts = [] } = useBootstrap();
  const { data } = useCachedQuery("api:blog:all", () => fetchBlogAllCached());

  const posts: BlogPostListItem[] = (data?.data?.length ? data.data : blogPosts).slice(0, MAX_POSTS);
  if (posts.length === 0) return null;

  return (
    <div className="blog-related-swiper">
      <h2 className="blog-related__title">Читайте також</h2>

      <Swiper
        modules={[Pagination, Autoplay]}
        slidesPerView={1}
        spaceBetween={12}
        pagination={{ clickable: true }}
        autoplay={{ delay: 5000, disableOnInteraction: true, pauseOnMouseEnter: true }}
        className="blog-related-swiper__slider"
      >
        {posts.map((post) => (
          <SwiperSlide key={post.slug}>
            <Link to={blogPostUrl(post.slug)} className="blog-related-swiper__card no-underline group">
              {post.image && (
                <div className="blog-related-swiper__media">
                  <img src={post.image} alt="" className="blog-related-swiper__img" loading="lazy" />
                </div>
              )}
              <div className="blog-related-swiper__body">
                {post.publishedAt && (
                  <time className="blog-related-swiper__date">{formatBlogDateShort(post.publishedAt)}</time>
                )}
                <h3 className="blog-related-swiper__title">{post.title}</h3>
                {post.excerpt && <p className="blog-related-swiper__excerpt">{post.excerpt}</p>}
              </div>
            </Link>
          </SwiperSlide>
        ))}
      </Swiper>

      <Link to={ROUTES.blog} className="blog-related__more no-underline">
        Усі матеріали →
      </Link>
    </div>
  );
}

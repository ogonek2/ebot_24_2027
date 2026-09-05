import Skeleton from "./Skeleton";

export default function HomeSkeleton() {
  return (
    <div className="pb-20 animate-in">
      <section className="pt-28 pb-16">
        <div className="site-container grid lg:grid-cols-2 gap-10 items-center">
          <div className="space-y-5">
            <Skeleton className="h-8 w-32" rounded="full" />
            <Skeleton className="h-14 w-full max-w-lg" />
            <Skeleton className="h-14 w-full max-w-md" />
            <Skeleton className="h-5 w-full max-w-sm" />
            <div className="flex gap-3 pt-2">
              <Skeleton className="h-12 w-40" rounded="full" />
              <Skeleton className="h-12 w-36" rounded="full" />
            </div>
          </div>
          <Skeleton className="h-[360px] w-full" rounded="3xl" />
        </div>
      </section>

      <section className="py-12">
        <div className="site-container">
          <Skeleton className="h-8 w-48 mb-8" />
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            {Array.from({ length: 8 }).map((_, i) => (
              <Skeleton key={i} className="h-28" />
            ))}
          </div>
        </div>
      </section>

      <section className="py-12">
        <div className="site-container grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {Array.from({ length: 6 }).map((_, i) => (
            <Skeleton key={i} className="h-44" />
          ))}
        </div>
      </section>
    </div>
  );
}

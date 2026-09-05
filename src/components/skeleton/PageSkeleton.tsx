import Skeleton from "./Skeleton";

type PageSkeletonProps = {
  cards?: number;
  columns?: 2 | 3;
};

export default function PageSkeleton({ cards = 6, columns = 3 }: PageSkeletonProps) {
  const gridClass =
    columns === 2 ? "grid sm:grid-cols-2 gap-4" : "grid sm:grid-cols-2 lg:grid-cols-3 gap-4";

  return (
    <div className="min-h-screen pt-24 pb-20 animate-in">
      <div className="site-container">
        <div className="flex gap-2 mb-8">
          <Skeleton className="h-4 w-16" rounded="md" />
          <Skeleton className="h-4 w-4" rounded="md" />
          <Skeleton className="h-4 w-24" rounded="md" />
        </div>
        <Skeleton className="h-8 w-40 mb-4" rounded="full" />
        <Skeleton className="h-12 w-full max-w-xl mb-3" />
        <Skeleton className="h-5 w-full max-w-lg mb-10" />

        <div className={gridClass}>
          {Array.from({ length: cards }).map((_, i) => (
            <Skeleton key={i} className="h-52" />
          ))}
        </div>
      </div>
    </div>
  );
}

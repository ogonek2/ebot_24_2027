import Skeleton from "./Skeleton";

export default function DetailSkeleton() {
  return (
    <div className="min-h-screen pt-24 pb-20 animate-in">
      <div className="site-container">
        <div className="flex gap-2 mb-8">
          <Skeleton className="h-4 w-16" rounded="md" />
          <Skeleton className="h-4 w-4" rounded="md" />
          <Skeleton className="h-4 w-32" rounded="md" />
        </div>
        <div className="grid lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2 space-y-6">
            <Skeleton className="h-64 w-full" rounded="3xl" />
            <Skeleton className="h-10 w-2/3 max-w-md" />
            <Skeleton className="h-5 w-full" />
            <Skeleton className="h-5 w-full" />
            <Skeleton className="h-5 w-4/5" />
          </div>
          <Skeleton className="h-72 w-full" rounded="3xl" />
        </div>
      </div>
    </div>
  );
}

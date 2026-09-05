import Skeleton from "./Skeleton";

export default function CheckoutSkeleton() {
  return (
    <div className="min-h-screen pt-24 pb-20 animate-in">
      <div className="site-container">
        <div className="flex gap-2 mb-8">
          <Skeleton className="h-4 w-16" rounded="md" />
          <Skeleton className="h-4 w-4" rounded="md" />
          <Skeleton className="h-4 w-40" rounded="md" />
        </div>
        <Skeleton className="h-12 w-full max-w-md mb-8" />

        <div className="grid lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2 space-y-4">
            <Skeleton className="h-72 w-full" rounded="3xl" />
          </div>
          <Skeleton className="h-64 w-full" rounded="3xl" />
        </div>
      </div>
    </div>
  );
}

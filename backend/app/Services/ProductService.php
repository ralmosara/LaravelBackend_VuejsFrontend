<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    /**
     * Cache key prefix for products.
     */
    private const CACHE_PREFIX = 'products';

    /**
     * Cache duration in seconds (5 minutes).
     */
    private const CACHE_TTL = 300;

    /**
     * Get paginated products with optional filtering and sorting.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedProducts(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::query();

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['in_stock'])) {
            $query->where('stock', '>', 0);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get all products (cached).
     *
     * @return Collection
     */
    public function getAllProducts(): Collection
    {
        return Cache::remember(
            self::CACHE_PREFIX . '.all',
            self::CACHE_TTL,
            fn() => Product::latest()->get()
        );
    }

    /**
     * Find a product by ID.
     *
     * @param int $id
     * @return Product|null
     */
    public function findProduct(int $id): ?Product
    {
        return Cache::remember(
            self::CACHE_PREFIX . ".{$id}",
            self::CACHE_TTL,
            fn() => Product::find($id)
        );
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        try {
            DB::beginTransaction();

            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'stock' => $data['stock'],
            ]);

            DB::commit();

            // Clear cache
            $this->clearCache();

            Log::info('Product created', ['product_id' => $product->id]);

            return $product->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create product', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        try {
            DB::beginTransaction();

            $product->update(array_filter([
                'name' => $data['name'] ?? $product->name,
                'description' => $data['description'] ?? $product->description,
                'price' => $data['price'] ?? $product->price,
                'stock' => $data['stock'] ?? $product->stock,
            ]));

            DB::commit();

            // Clear cache
            $this->clearCache($product->id);

            Log::info('Product updated', ['product_id' => $product->id]);

            return $product->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a product.
     *
     * @param Product $product
     * @return bool
     */
    public function deleteProduct(Product $product): bool
    {
        try {
            DB::beginTransaction();

            $productId = $product->id;
            $product->delete();

            DB::commit();

            // Clear cache
            $this->clearCache($productId);

            Log::info('Product deleted', ['product_id' => $productId]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete product', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update product stock.
     *
     * @param Product $product
     * @param int $quantity
     * @param string $operation (add|subtract|set)
     * @return Product
     */
    public function updateStock(Product $product, int $quantity, string $operation = 'set'): Product
    {
        try {
            DB::beginTransaction();

            switch ($operation) {
                case 'add':
                    $product->increment('stock', $quantity);
                    break;
                case 'subtract':
                    $product->decrement('stock', $quantity);
                    break;
                case 'set':
                default:
                    $product->update(['stock' => $quantity]);
                    break;
            }

            DB::commit();

            // Clear cache
            $this->clearCache($product->id);

            Log::info('Product stock updated', [
                'product_id' => $product->id,
                'operation' => $operation,
                'quantity' => $quantity
            ]);

            return $product->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product stock', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get low stock products.
     *
     * @param int $threshold
     * @return Collection
     */
    public function getLowStockProducts(int $threshold = 10): Collection
    {
        return Product::where('stock', '>', 0)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock', 'asc')
            ->get();
    }

    /**
     * Get out of stock products.
     *
     * @return Collection
     */
    public function getOutOfStockProducts(): Collection
    {
        return Product::where('stock', 0)->get();
    }

    /**
     * Clear product cache.
     *
     * @param int|null $productId
     * @return void
     */
    private function clearCache(?int $productId = null): void
    {
        Cache::forget(self::CACHE_PREFIX . '.all');

        if ($productId) {
            Cache::forget(self::CACHE_PREFIX . ".{$productId}");
        }
    }

    /**
     * Get product statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . '.stats',
            self::CACHE_TTL,
            function () {
                return [
                    'total_products' => Product::count(),
                    'in_stock' => Product::where('stock', '>', 0)->count(),
                    'out_of_stock' => Product::where('stock', 0)->count(),
                    'low_stock' => Product::where('stock', '>', 0)->where('stock', '<=', 10)->count(),
                    'total_value' => Product::sum(DB::raw('price * stock')),
                    'average_price' => Product::avg('price'),
                    'total_stock' => Product::sum('stock'),
                ];
            }
        );
    }
}

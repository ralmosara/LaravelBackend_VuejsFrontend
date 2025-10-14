<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Product service instance.
     *
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * Create a new controller instance.
     *
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a paginated listing of products.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get paginated products",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", description="Search term", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_by", in="query", description="Sort by field", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_order", in="query", description="Sort order (asc/desc)", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 10);
            $filters = $request->only(['search', 'min_price', 'max_price', 'in_stock', 'sort_by', 'sort_order']);

            $products = $this->productService->getPaginatedProducts($filters, $perPage);

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => new ProductCollection($products)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve products');
        }
    }

    /**
     * Store a newly created product in storage.
     *
     * @param StoreProductRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","stock"},
     *             @OA\Property(property="name", type="string", example="Laptop"),
     *             @OA\Property(property="description", type="string", example="High-performance laptop"),
     *             @OA\Property(property="price", type="number", format="float", example=999.99),
     *             @OA\Property(property="stock", type="integer", example=50)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => new ProductResource($product)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to create product');
        }
    }

    /**
     * Display the specified product.
     *
     * @param string $id
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a specific product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = $this->productService->findProduct((int) $id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => new ProductResource($product)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve product');
        }
    }

    /**
     * Update the specified product in storage.
     *
     * @param UpdateProductRequest $request
     * @param string $id
     * @return JsonResponse
     *
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Laptop"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="price", type="number", format="float", example=1099.99),
     *             @OA\Property(property="stock", type="integer", example=45)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated successfully"),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        try {
            $product = $this->productService->findProduct((int) $id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $updatedProduct = $this->productService->updateProduct($product, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => new ProductResource($updatedProduct)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update product');
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param string $id
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product deleted successfully"),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $product = $this->productService->findProduct((int) $id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $this->productService->deleteProduct($product);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
                'data' => null
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete product');
        }
    }

    /**
     * Update product stock.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     *
     * @OA\Patch(
     *     path="/api/products/{id}/stock",
     *     summary="Update product stock",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity","operation"},
     *             @OA\Property(property="quantity", type="integer", example=10),
     *             @OA\Property(property="operation", type="string", enum={"add","subtract","set"}, example="add")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Stock updated successfully"),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function updateStock(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'operation' => 'required|in:add,subtract,set'
        ]);

        try {
            $product = $this->productService->findProduct((int) $id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $updatedProduct = $this->productService->updateStock(
                $product,
                $request->quantity,
                $request->operation
            );

            return response()->json([
                'success' => true,
                'message' => 'Product stock updated successfully',
                'data' => new ProductResource($updatedProduct)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update product stock');
        }
    }

    /**
     * Get products with low stock.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/products/low-stock",
     *     summary="Get products with low stock",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="threshold", in="query", description="Stock threshold", required=false, @OA\Schema(type="integer", default=10)),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function lowStock(Request $request): JsonResponse
    {
        try {
            $threshold = (int) $request->get('threshold', 10);
            $products = $this->productService->getLowStockProducts($threshold);

            return response()->json([
                'success' => true,
                'message' => 'Low stock products retrieved successfully',
                'data' => ProductResource::collection($products)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve low stock products');
        }
    }

    /**
     * Get product statistics.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/products/statistics",
     *     summary="Get product statistics",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->productService->getStatistics();

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve statistics');
        }
    }

    /**
     * Handle exceptions and return appropriate JSON response.
     *
     * @param \Exception $exception
     * @param string $message
     * @return JsonResponse
     */
    private function handleException(\Exception $exception, string $message): JsonResponse
    {
        // Log the exception
        \Log::error($message, [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Return appropriate response based on exception type
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $exception->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Generic error response
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => config('app.debug') ? $exception->getMessage() : 'An error occurred'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

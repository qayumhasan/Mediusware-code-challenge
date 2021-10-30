<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $products = Product::with('variantPrices')->orderBy("created_at", 'desc')->paginate(2);
        $varients = ProductVariant::with('variantitem')->get();
        $varients = $varients->groupBy('variantitem.title');

        return view('products.index', compact('products', 'varients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {

        $variants = Variant::all();
        $product_varients = ProductVariant::where('product_id', $product->id)->orderBy("created_at", 'desc')->get();
        $product_varients = $product_varients->groupBy('variant_id');

        $product_varients_price = ProductVariantPrice::where('product_id', $product->id)->get();

        return view('products.edit', compact('variants', 'product', 'product_varients', 'product_varients_price'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }


    public function product_search(Request $request)
    {
        $title = $request->input('title');
        $variant = $request->input('variant');
        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');
        $date = $request->input('date');

        if (isset($price_from) && isset($price_to)) {
            $products = Product::when(isset($title), function ($q) use ($title) {
                return $q->where('title', $title);
            })

                ->when(isset($variant), function ($q) use ($variant) {
                    return $q->whereHas('variantPrices', function ($variant_query) use ($variant) {
                        return $variant_query->orWhere('product_variant_one', $variant)->orWhere('product_variant_two', $variant)->orWhere('product_variant_three', $variant);
                    });
                })
                ->when(isset($price_from) && isset($price_to), function ($q) use ($price_from, $price_to) {
                    return $q->whereHas('variantPrices', function ($variant_query) use ($price_from, $price_to) {
                        return $variant_query->whereBetween('price', [$price_from, $price_to]);
                    });
                })
                ->when(isset($date), function ($q) use ($date) {
                    return $q->whereDate('created_at', $date);
                })
                ->with(['variantPrices' => function ($q) use ($price_from, $price_to) {
                    return $q->whereBetween('price', [$price_from, $price_to]);
                },])
                ->orderBy("created_at", 'desc')
                ->paginate(2);
        } elseif (isset($variant)) {
            
            $products = Product::when(isset($title), function ($q) use ($title) {
                return $q->where('title', $title);
            })
                ->when(isset($variant), function ($q) use ($variant) {
                    return $q->whereHas('variantPrices', function ($variant_query) use ($variant) {
                        return $variant_query->orWhere('product_variant_one', $variant)->orWhere('product_variant_two', $variant)->orWhere('product_variant_three', $variant);
                    });
                })
                ->when(isset($price_from) && isset($price_to), function ($q) use ($price_from, $price_to) {
                    return $q->whereHas('variantPrices', function ($variant_query) use ($price_from, $price_to) {
                        return $variant_query->whereBetween('price', [$price_from, $price_to]);
                    });
                })
                ->when(isset($date), function ($q) use ($date) {
                    return $q->whereDate('created_at', $date);
                })
                ->with(['variantPrices' => function ($q) use ($variant) {
                    return $q->where('product_variant_one', $variant)->orWhere('product_variant_two', $variant)->orWhere('product_variant_three', $variant);
                }])
                ->orderBy("created_at", 'desc')
                ->paginate(2);
        } else {

            $products = Product::when(isset($title), function ($q) use ($title) {
                return $q->where('title', $title);
            })

                ->when(isset($variant), function ($q) use ($variant) {
                    return $q->whereHas('variantPrices', function ($variant_query) use ($variant) {
                        return $variant_query->orWhere('product_variant_one', $variant)->orWhere('product_variant_two', $variant)->orWhere('product_variant_three', $variant);
                    });
                })
                ->when(isset($price_from) && isset($price_to), function ($q) use ($price_from, $price_to) {
                    return $q->whereHas('variantPrices', function ($variant_query) use ($price_from, $price_to) {
                        return $variant_query->whereBetween('price', [$price_from, $price_to]);
                    });
                })
                ->when(isset($date), function ($q) use ($date) {
                    return $q->whereDate('created_at', $date);
                })
                ->with(['variantPrices' => function ($q) use ($price_from, $price_to) {
                    return $q->whereBetween('price', [$price_from, $price_to]);
                }, 'variantPrices'])
                ->orderBy("created_at", 'desc')
                ->paginate(2);
        }




        $varients = ProductVariant::with('variantitem')->get();
        $varients = $varients->groupBy('variantitem.title');

        return view('products.index', compact('products', 'varients'));
    }
}

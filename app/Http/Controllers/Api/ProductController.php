<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'sku' => 'required|unique:products',
            'description' => 'required',
        ]);
        DB::beginTransaction();
        try {

            $products = new Product();
            $products->title = $request->title;
            $products->sku = $request->sku;
            $products->description = $request->description;
            $products->save();

            $product_variant = $request->product_variant;
            foreach ($product_variant as  $items) {
                $variant_items['variant_id'] = $items['option'];
                foreach ($items['tags'] as $tag) {
                    $variant_items['variant'] = $tag;
                    $variant_items['product_id'] = $products->id;
                    $variant_items['created_at'] = Carbon::now();
                    ProductVariant::create($variant_items);
                }
            }



            $product_variant_prices = $request->product_variant_prices;

            foreach ($product_variant_prices as $row) {
                $titles = explode('/', $row['title']);
                $titles = array_filter($titles);


                foreach ($titles as $key => $title) {

                    $product_variant = ProductVariant::where('variant', $title)->where('product_id',$products->id)->select('id')->first();
                    switch ($key) {
                        case 0:
                            $product_variant_item['product_variant_one'] = $product_variant->id;
                            break;
                        case 1:
                            $product_variant_item['product_variant_two'] = $product_variant->id;
                            break;
                        case 2:
                            $product_variant_item['product_variant_three'] = $product_variant->id;
                            break;
                    }
                }
                $product_variant_item['price'] = $row['price'];
                $product_variant_item['stock'] = $row['stock'];
                $product_variant_item['product_id'] = $products->id;
                ProductVariantPrice::create($product_variant_item);
            }
            
            DB::commit();
            return response()->json([
                'msg' => 'Product Insert Succseefully!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => "error below" . $e->getMessage(),
                'status' => 500,
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'title' => 'required',
            Rule::unique('sku')->ignore($id),
            'description' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $products = Product::findOrFail($id);
            $products->title = $request->title;
            $products->sku = $request->sku;
            $products->description = $request->description;
            $products->save();

            ProductVariant::where('product_id', $id)->delete();
            $product_variant = $request->product_variant;
            foreach ($product_variant as  $items) {
                $variant_items['variant_id'] = $items['option'];
                foreach ($items['tags'] as $tag) {
                    $variant_items['variant'] = $tag;
                    $variant_items['product_id'] = $products->id;
                    $variant_items['created_at'] = Carbon::now();
                    ProductVariant::create($variant_items);
                }
            }



            $product_variant_prices = $request->product_variant_prices;
            ProductVariantPrice::where('product_id', $id)->delete();

            foreach ($product_variant_prices as $row) {
                $titles = explode('/', $row['title']);
                $titles = array_filter($titles);


                foreach ($titles as $key => $title) {

                    $product_variant = ProductVariant::where('variant', $title)->where('product_id',$id)->select('id')->first();
                    switch ($key) {
                        case 0:
                            $product_variant_item['product_variant_one'] = $product_variant->id;
                            break;
                        case 1:
                            $product_variant_item['product_variant_two'] = $product_variant->id;
                            break;
                        case 2:
                            $product_variant_item['product_variant_three'] = $product_variant->id;
                            break;
                    }
                }
                $product_variant_item['price'] = $row['price'];
                $product_variant_item['stock'] = $row['stock'];
                $product_variant_item['product_id'] = $products->id;

                ProductVariantPrice::create($product_variant_item);
            }

            DB::commit();

            return response()->json([
                'msg' => 'Product Updated Succseefully!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => "error below" . $e->getMessage(),
                'status' => 500,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

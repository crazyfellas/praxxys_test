<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{

  
    public function index()
    {
        $paginate = request('paginate',10);
        $search_term = request('q','');

        $selectedCategory = request('selectedCategory');
        $selectedName = request('selectedName');

        $sort_direction = request('sort_direction','desc');
        if(in_array($sort_direction,['name','datetime'])){
            $sort_direction = 'datetime ';
        }

        $sort_field = request('sort_field','datetime');
        if(in_array($sort_field,['asc','desc'])){
            $sort_direction = 'desc';
        }


        $products = Product::with(['category'])
        ->when($selectedCategory,function($query) use ($selectedCategory) {
            $query->where('category_id', $selectedCategory);
        })
        ->search(trim($search_term))
        ->orderBy($sort_field,$sort_direction)
        ->paginate($paginate);

        return ProductResource::collection($products);

    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'datetime' => 'required'
        ]);

       
   
        $productNumber = date('mdYHis'). uniqid(); //Generate Product Number
        
        $files= $request->file('images');
        if($request->file('images')){
            //Save Product Item
            $productNumber = date('mdYHis'). uniqid(); //Generate Product Number
            $product::create([
                'category_id' => $request->category_id,
                'productNumber' => $productNumber,
                'name' => $request->name,
                'description' => $request->description,
                'datetime' => $request->datetime
            ]);

            if(!is_array($files)){
                $files = [$files];
            }
            //Loop Save Images
            for ($i=0; $i < count($files); $i++) { 
                $file = $files[$i];
                $imageName = date('mdYHis'). uniqid(); //FILE NAME
                $desinationPath = public_path(). '/images';
                $file->move($desinationPath, $imageName);

                Image::create([
                    'productNumber' => $productNumber,
                    'imageName' => $imageName,
                    'path' => env('APP_URL'). '/images/'. $imageName
                ]);
            }
            return response()->json('Product created!');
        }
        else{
            $productNumber = date('mdYHis'). uniqid(); //Generate Product Number
            $product::create([
                'category_id' => $request->category_id,
                'productNumber' => $productNumber,
                'name' => $request->name,
                'description' => $request->description,
                'datetime' => $request->datetime
            ]);
        }
    }

    public function show($id)
    {
        $product = Product::find($id);
        return response()->json($product);
        
    }

    public function update($id, Request $request)
    {
      

        $product = Product::find($id);
        $product->update($request->all());

       
       
    }

    public function allProducts()
    {
        return Product::pluck('id');
    }

    public function destroy(Product $product)
    {
    
      
        $imageUpload = Image::where('productNumber',$product->productNumber)->get(); //SEARCH PRODUCT NUMBER

        foreach ($imageUpload as $image) { //LOOP DELETE FILES

            File::delete([

                public_path(('/images/').$image->imageName)

            ]);
        }
        $product->delete(); //DELETE PRODUCT
    
        return response()->noContent();
    }

    public function massDestroy($products)
    {
    
        $productsArray = explode(',', $products);
        Product::whereKey($productsArray)->delete();
        return response()->noContent();
    }

    

    public function updateImage(Request $request){
 
        $files= $request->file('images');
        if($request->file('images')){
            //Save Product Item
            $productNumber = $request->productNumber; //GET PRODUCT NUMBER
            

            if(!is_array($files)){
                $files = [$files];
            }
            //Loop Save Images
            for ($i=0; $i < count($files); $i++) { 
                $file = $files[$i];
                $imageName = date('mdYHis'). uniqid(); //FILE NAME
                $desinationPath = public_path(). '/images';
                $file->move($desinationPath, $imageName);

                Image::create([
                    'productNumber' => $productNumber,
                    'imageName' => $imageName,
                    'path' => env('APP_URL'). '/images/'. $imageName
                ]);
            }
            return response()->json('Product created!');
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class ProductController extends Controller
{

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
        $this->middleware('auth');
        $this->middleware('can:view_products')->only('index');
        $this->middleware('can:create_products')->only('create', 'store');
        $this->middleware('can:edit_products')->only('edit', 'update');
        $this->middleware('can:delete_products')->only('destroy');
    
    
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return view('backend.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // Validate the incoming data
         $request->validate([
            'product' => 'required|string|max:255',
           
        ]);

        // Store the data in the database
        Product::create($request->all());

        // Redirect back with success message
        return redirect()->route('backend.product.index')->with('success', 'product saved successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $products = Product::findOrFail($id);
        return view('backend.product.update', compact('products'));

    }

    public function update(Request $request, $id)
    {
        // Find the data entry by ID or fail if not found
        $products = Product::findOrFail($id);
    
        // Validate the request
        $request->validate([
            'product' => 'required|string|max:255',
            
        ]);
    
        // Update the data entries
        $products->product = $request->input('product');
       
        $products->save();
    
        // Redirect with a success message
        return redirect()->route('backend.product.index')->with('success', 'product updated successfully');
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $products = Product::findOrFail($id);

        

        // Delete the comment from the database
        $products->delete();

        // Redirect with success message
        return redirect()->route('backend.product.index')->with('success', 'product deleted successfully');
    }
}

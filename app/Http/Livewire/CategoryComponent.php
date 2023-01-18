<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

use Gloudemans\Shoppingcart\Facades\Cart; //or you can use these below
// use Cart;
// use Gloudemans\Shoppingcart\Cart;
class CategoryComponent extends Component
{
    use WithPagination;
    public $pageSize= 12;
    public $orderBy= 'Default Sorting';
    public $slug;

    public function store($product_id, $product_name,$product_price)
    {
        Cart::add($product_id,$product_name,1,$product_price)->associate('\App\Models\Product');
        session()->flash('success_message', 'Item added in Cart');
        return redirect()->route('shop.cart');
    }
    //change page size,
    public function changePagesize($size)
    {
        $this->pageSize =$size;
    }

    public function changeOrderBy($orderBy)
    {
        $this->orderBy =$orderBy;
    }
    public function mount($slug)
    {
        $this->$slug =$slug;
    }
    public function render()
    {
        $category =Category::where('slug',$this->slug)->first();

        $category_id = $category->id;
        $category_name = $category->name;

        if($this->orderBy =='Price: Low to High')
        {
            $products =Product::where('category_id',$category_id)->orderBY('regular_price', 'ASC')->paginate($this->pageSize);
        }
        else if($this->orderBy =='Price: High to Low')
        {
            $products =Product::where('category_id',$category_id)->orderBY('regular_price', 'DESC')->paginate($this->pageSize);
        }
        else if($this->orderBy =='Sort: by Newest')
        {
            $products =Product::where('category_id',$category_id)->latest('regular_price')->paginate($this->pageSize);
            //which is the same with:
            //$products =Product::orderBY('regular_price', 'DESC')->paginate($this->pageSize);
        }
        else
        {
            $products = Product::where('category_id',$category_id)->paginate($this->pageSize);
        }

        // Order by category....
        $categories = Category::orderBy('name', 'ASC')->get();
        return view('livewire.category-component',['products'=>$products, 'categories'=> $categories, 'category_name'=> $category_name]);
    }
}


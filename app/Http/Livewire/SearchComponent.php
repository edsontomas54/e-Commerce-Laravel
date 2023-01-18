<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

use Gloudemans\Shoppingcart\Facades\Cart; //or you can use these below
// use Cart;
// use Gloudemans\Shoppingcart\Cart;
class SearchComponent extends Component
{
    use WithPagination;
    public $pageSize= 12;
    public $orderBy= 'Default Sorting';

    public $q;
    public $search_term;

    public function mount()
    {
        $this->fill(request()->only('q'));
        $this->search_term= '%'.$this->q .'%';
    }
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
    public function render()
    {
        if($this->orderBy =='Price: Low to High')
        {
            $products =Product::where('name','like',$this->search_term)->orderBY('regular_price', 'ASC')->paginate($this->pageSize);
        }
        else if($this->orderBy =='Price: High to Low')
        {
            $products =Product::where('name', 'like',$this->search_term)->orderBY('regular_price', 'DESC')->paginate($this->pageSize);
        }
        else if($this->orderBy =='Sort: by Newest')
        {
            $products =Product::where('name', 'like',$this->search_term)->latest('regular_price')->paginate($this->pageSize);
            //which is the same with:
            //  $products =Product::orderBY('regular_price', 'DESC')->paginate($this->pageSize);
        }
        else
        {
            $products = Product::where('name', 'like',$this->search_term)->paginate($this->pageSize);
        }

        // Order by category....
        $categories = Category::orderBy('name', 'ASC')->get();
        return view('livewire.search-component',['products'=>$products, 'categories'=>$categories]);
    }
}

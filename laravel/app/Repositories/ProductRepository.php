<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\Product;
use App\OfferProduct;
use App\InvoiceProduct;
use App\DispatchProduct;
use App\ContractProduct;

class ProductRepository extends UserRepository
{
    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    //get categories
    public function getCategories()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $categories = Category::select('id', 'name')
                ->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->paginate(30);

            return ['status' => 1, 'data' => $categories];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert category
    public function insertCategory($name)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $category = new Category;
            $category->company_id = $company_id;
            $category->name = $name;
            $category->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get category details
    public function getCategoryDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $category = Category::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if category doesn't exist return error status
            if (!$category)
            {
                return ['status' => 0];
            }

            return ['status' => 1, 'data' => $category];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update category
    public function updateCategory($id, $name)
    {
        try
        {
            $category = Category::find($id);
            $category->name = $name;
            $category->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //delete category
    public function deleteCategory($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $category = Category::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if category doesn't exist return error status
            if (!$category)
            {
                return ['status' => 0];
            }

            //check products categories
            $products_check = Product::where('company_id', '=', $company_id)->where('category_id', '=', $id)->count();

            //start transaction
            DB::beginTransaction();

            //if category is assigned to some product set deleted status to 'T', else delete category
            if ($products_check > 0)
            {
                //set deleted status to 'T'
                $category->deleted = 'T';
                $category->save();
            }
            else
            {
                //delete category
                $category->delete();
            }

            //commit transaction
            DB::commit();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get categories - select
    public function getCategoriesSelect($default_option = false)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set categories array
            $categories_array = [];

            $categories = Category::select('id', 'name')->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->get();

            if ($default_option)
            {
                //add default option to categories array
                $categories_array[0] = trans('main.choose_category');
            }

            //loop through all categories
            foreach ($categories as $category)
            {
                //add category to categories array
                $categories_array[$category->id] = $category->name;
            }

            return ['status' => 1, 'data' => $categories_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    //get products
    public function getProducts($search_string, $category)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $products = Product::with('category')
                ->select('id', 'category_id', 'code', 'name', 'price')
                ->where('company_id', '=', $company_id)->where('deleted', '=', 'F');

            if ($search_string)
            {
                $products->where('name', 'like', '%'.$search_string.'%');
            }

            if ($category)
            {
                $products->where('category_id', '=', $category);
            }

            $products = $products->paginate(30);

            return ['status' => 1, 'data' => $products];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert product
    public function insertProduct($category, $unit, $tax_group, $code, $name, $price, $service, $description)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $product = new Product;
            $product->company_id = $company_id;
            $product->category_id = $category;
            $product->unit_id = $unit;
            $product->tax_group_id = $tax_group;
            $product->code = $code;
            $product->name = $name;
            $product->price = $price;
            $product->service = $service;
            $product->description = $description;
            $product->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get product details
    public function getProductDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $product = Product::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if product doesn't exist return error status
            if (!$product)
            {
                return ['status' => 0];
            }

            return ['status' => 1, 'data' => $product];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update product
    public function updateProduct($id, $category, $unit, $tax_group, $code, $name, $price, $service, $description)
    {
        try
        {
            $product = Product::find($id);
            $product->category_id = $category;
            $product->unit_id = $unit;
            $product->tax_group_id = $tax_group;
            $product->code = $code;
            $product->name = $name;
            $product->price = $price;
            $product->service = $service;
            $product->description = $description;
            $product->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //delete product
    public function deleteProduct($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $product = Product::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if product doesn't exist return error status
            if (!$product)
            {
                return ['status' => 0];
            }

            //check offers products
            $offers_check = OfferProduct::where('product_id', '=', $id)->count();

            //check invoices products
            $invoices_check = InvoiceProduct::where('product_id', '=', $id)->count();

            //check dispatches products
            $dispatches_check = DispatchProduct::where('product_id', '=', $id)->count();

            //check contracts products
            $contracts_check = ContractProduct::where('product_id', '=', $id)->count();

            //start transaction
            DB::beginTransaction();

            //if product is assigned to some offer, invoice, dispatch or contract set deleted status to 'T', else delete product
            if ($offers_check > 0 || $invoices_check > 0 || $dispatches_check > 0 || $contracts_check > 0)
            {
                //set deleted status to 'T'
                $product->deleted = 'T';
                $product->save();
            }
            else
            {
                //delete product
                $product->delete();
            }

            //commit transaction
            DB::commit();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //search products
    public function searchProducts($search_string)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $products = Product::select('id', 'name', 'code')
                ->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->where('name', 'like', '%'.$search_string.'%')
                ->take(30)->get();

            foreach ($products as $product)
            {
                //add html special characters to product name
                $product->name = htmlspecialchars($product->name);
            }

            return ['status' => 1, 'products' => $products];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //check merchandise
    public function checkMerchandise($products)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set default merchandise status
            $merchandise = 'F';

            foreach ($products as $product)
            {
                $product_model = Product::select('service')->where('company_id', '=', $company_id)
                    ->where('id', '=', $product['id'])->where('deleted', '=', 'F')->first();

                if ($product_model->service == 'F')
                {
                    //set merchandise status to 'T'
                    $merchandise = 'T';
                }
            }

            return ['status' => 1, 'merchandise' => $merchandise];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }
}

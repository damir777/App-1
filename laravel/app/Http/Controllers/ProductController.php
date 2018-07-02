<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Category;
use App\Product;
use App\Repositories\ProductRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\TaxGroupRepository;

class ProductController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new ProductRepository;
    }

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    //get categories
    public function getCategories()
    {
        //call getCategories method from ProductRepository to get categories
        $categories = $this->repo->getCategories();

        //if response status = '0' show error page
        if ($categories['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.categories.list', ['categories' => $categories['data']]);
    }

    //add category
    public function addCategory()
    {
        return view('app.categories.addCategory');
    }

    //insert category
    public function insertCategory(Request $request)
    {
        $name = $request->name;

        //validate form inputs
        $validator = Validator::make($request->all(), Category::validateCategoryForm());

        if ($request->ajax() || $request->wantsJson())
        {
            //if form input is not correct return error message
            if (!$validator->passes())
            {
                return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
            }

            //call insertCategory method from ProductRepository to insert category
            $response = $this->repo->insertCategory($name);

            return response()->json($response);
        }
        else
        {
            //if form input is not correct return error message
            if (!$validator->passes())
            {
                return redirect()->route('AddCategory')->withErrors($validator)
                    ->with('error_message', trans('errors.validation_error'))->withInput();
            }

            //call insertCategory method from ProductRepository to insert category
            $response = $this->repo->insertCategory($name);

            //if response status = '0' return error message
            if ($response['status'] == 0)
            {
                return redirect()->route('AddCategory')->with('error_message', trans('errors.error'))->withInput();
            }

            return redirect()->route('GetCategories')->with('success_message', trans('main.category_insert'));
        }
    }

    //edit category
    public function editCategory($id)
    {
        //call getCategoryDetails method from ProductRepository to get category details
        $category = $this->repo->getCategoryDetails($id);

        //if response status = '0' return error message
        if ($category['status'] == 0)
        {
            return redirect()->route('GetCategories')->with('error_message', trans('errors.error'));
        }

        return view('app.categories.editCategory', ['category' => $category['data']]);
    }

    //update category
    public function updateCategory(Request $request)
    {
        $id = $request->id;
        $name = $request->name;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Category::validateCategoryForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('EditCategory', $id)->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateCategory method from ProductRepository to update category
        $response = $this->repo->updateCategory($id, $name);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('EditCategory', $id)->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetCategories')->with('success_message', trans('main.category_update'));
    }

    //delete category
    public function deleteCategory($id)
    {
        //call deleteCategory method from ProductRepository to delete category
        $response = $this->repo->deleteCategory($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetCategories')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetCategories')->with('success_message', trans('main.category_delete'));
    }

    //get categories - select
    public function getCategoriesSelect()
    {
        //call getCategoriesSelect method from ProductRepository to get categories - select
        $response = $this->repo->getCategoriesSelect();

        return response()->json($response);
    }

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    //get products
    public function getProducts(Request $request)
    {
        //get search parameters
        $search_string = $request->search_string;
        $category = $request->category;

        //call getCategoriesSelect method from ProductRepository to get categories - select
        $categories = $this->repo->getCategoriesSelect(1);

        //call getProducts method from ProductRepository to get products
        $products = $this->repo->getProducts($search_string, $category);

        //if response status = '0' show error page
        if ($categories['status'] == 0 || $products['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.products.list', ['categories' => $categories['data'], 'search_string' => $search_string,
            'category' => $category, 'products' => $products['data']]);
    }

    //add product
    public function addProduct()
    {
        //call getCategoriesSelect method from ProductRepository to get categories - select
        $categories = $this->repo->getCategoriesSelect();

        //call getUnitsSelect method from CompanyRepository to get units - select
        $this->repo = new CompanyRepository;
        $units = $this->repo->getUnitsSelect();

        //call getTaxGroupsSelect method from TaxGroupRepository to get tax groups - select
        $this->repo = new TaxGroupRepository;
        $tax_groups = $this->repo->getTaxGroupsSelect();

        //if response status = '0' show error page
        if ($categories['status'] == 0 || $units['status'] == 0 || $tax_groups['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.products.addProduct', ['categories' => $categories['data'], 'units' => $units['data'],
            'tax_groups' => $tax_groups['data']]);
    }

    //insert product
    public function insertProduct(Request $request)
    {
        $category = $request->category;
        $unit = $request->unit;
        $tax_group = $request->tax_group;
        $code = $request->code;
        $name = $request->name;
        $price = $request->price;
        $service = $request->service;
        $description = $request->description;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Product::validateProductForm($company_id));

        if ($request->ajax() || $request->wantsJson())
        {
            //if form input is not correct return error message
            if (!$validator->passes())
            {
                return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
            }

            //call insertProduct method from ProductRepository to insert product
            $response = $this->repo->insertProduct($category, $unit, $tax_group, $code, $name, $price, $service, $description);

            return response()->json($response);
        }
        else
        {
            //if form input is not correct return error message
            if (!$validator->passes())
            {
                return redirect()->route('AddProduct')->withErrors($validator)
                    ->with('error_message', trans('errors.validation_error'))->withInput();
            }

            //call insertProduct method from ProductRepository to insert product
            $response = $this->repo->insertProduct($category, $unit, $tax_group, $code, $name, $price, $service, $description);

            //if response status = '0' return error message
            if ($response['status'] == 0)
            {
                return redirect()->route('AddProduct')->with('error_message', trans('errors.error'))->withInput();
            }

            return redirect()->route('GetProducts')->with('success_message', trans('main.product_insert'));
        }
    }

    //edit product
    public function editProduct($id)
    {
        //call getCategoriesSelect method from ProductRepository to get categories - select
        $categories = $this->repo->getCategoriesSelect();

        //call getUnitsSelect method from CompanyRepository to get units - select
        $this->repo = new CompanyRepository;
        $units = $this->repo->getUnitsSelect();

        //call getTaxGroupsSelect method from TaxGroupRepository to get tax groups - select
        $this->repo = new TaxGroupRepository;
        $tax_groups = $this->repo->getTaxGroupsSelect();

        //call getProductDetails method from ProductRepository to get product details
        $this->repo = new ProductRepository;
        $product = $this->repo->getProductDetails($id);

        //if response status = '0' return error message
        if ($categories['status'] == 0 || $units['status'] == 0 || $tax_groups['status'] == 0 || $product['status'] == 0)
        {
            return redirect()->route('GetProducts')->with('error_message', trans('errors.error'));
        }

        return view('app.products.editProduct', ['categories' => $categories['data'], 'units' => $units['data'],
            'tax_groups' => $tax_groups['data'], 'product' => $product['data']]);
    }

    //update product
    public function updateProduct(Request $request)
    {
        $id = $request->id;
        $category = $request->category;
        $unit = $request->unit;
        $tax_group = $request->tax_group;
        $code = $request->code;
        $name = $request->name;
        $price = $request->price;
        $service = $request->service;
        $description = $request->description;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Product::validateProductForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('EditProduct', $id)->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateProduct method from ProductRepository to update product
        $response = $this->repo->updateProduct($id, $category, $unit, $tax_group, $code, $name, $price, $service, $description);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('EditProduct', $id)->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetProducts')->with('success_message', trans('main.product_update'));
    }

    //delete product
    public function deleteProduct($id)
    {
        //call deleteProduct method from ProductRepository to delete product
        $response = $this->repo->deleteProduct($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetProducts')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetProducts')->with('success_message', trans('main.product_delete'));
    }

    //search products
    public function searchProducts(Request $request)
    {
        $search_string = $request->search_string;

        //call searchProducts method from ProductRepository to search products
        $response = $this->repo->searchProducts($search_string);

        return response()->json($response);
    }

    //check merchandise
    public function checkMerchandise(Request $request)
    {
        $products = $request->products;

        //call checkMerchandise method from ProductRepository to check merchandise
        $response = $this->repo->checkMerchandise($products);

        return response()->json($response);
    }
}

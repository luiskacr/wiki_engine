<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index()
    {
        $categories = Category::all()->where('parent_id',1)->whereNotIn('id',1);

        return view('Admin.Category.index')->with('categories',$categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('Admin.Category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        DB::beginTransaction();
        try{
            $categoryOrder = Category::all()->sortByDesc('position')->take(1);

            $newPosition = $categoryOrder->firstOrFail()->position + 10;

            $cateory = Category::create([
                'name'=> $request->request->get('name'),
                'parent_id' => 1,
                'is_enable' => 1,
                'position' => $newPosition,
            ]);

            DB::commit();
        }catch (\Exception $e){
            DB::rollback();

            return redirect()->route('category.create')->with('error',$e->getMessage());
        }

        return redirect()->route('category.index')->with('success','Permission created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);

        return view('Admin.Category.show')->with('category',$category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view('Admin.Category.edit')->with('category',$category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        DB::beginTransaction();
        try{

            $isActive = $request->request->get('active') == 'on' ? true : false;

            Category::whereId($id)->update([
                'name' => $request->request->get('name'),
                'is_enable'=>$isActive
            ]);

            DB::commit();
        }catch (\Exception $e){

            DB::rollback();

            return redirect()->route('category.edit',$id)->with('error',$e->getMessage());
        }
        return redirect()->route('category.index')->with('success','Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $categories = Category::all()->where('parent_id',1)->whereNotIn('id',1);

            $isOldUpdated = true;

            foreach ($categories as $category)
            {
                if($category->id >= $id)
                {
                    if($category->id == $id)
                    {
                        $oldPosition = $category->position;
                        $category->delete();
                    }
                    else
                    {
                        if($isOldUpdated)
                        {
                            $category->update([
                                'position' => $oldPosition
                            ]);

                            $isOldUpdated = false;

                        }else{
                            $oldPosition = $oldPosition + 10;

                            $category->update([
                                'position' => $oldPosition
                            ]);
                        }
                    }
                }
            }
            DB::commit();
        }catch (\Exception $e){

            DB::rollback();

            return redirect()->route('category.edit',$id)->with('error',$e->getMessage());
        }
        return redirect()->route('category.index')->with('success','Category updated successfully');

    }

    // Sub Categories Options

    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function sub_Index(Category $category)
    {
        $subCategories = Category::all()->where('parent_id',$category->id);

        $parentCategory = Category::findOrFail($category->id);

        return view('Admin.Category.index_subcategory')->with('subCategories',$subCategories)->with('parentCategory',$parentCategory);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Category $category
     * @return \Illuminate\Contracts\View\View
     */
    public function sub_create(Category $category)
    {
        $parentCategory = Category::findOrFail($category->id);

        return view('Admin.Category.create_subcategory')->with('parentCategory',$parentCategory);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sub_store(CategoryRequest $request,int $id)
    {
        DB::beginTransaction();
        try{
            $categoryOrder = Category::all()->where('parent_id',$id)->sortByDesc('position')->take(1);

            if($categoryOrder->count() == 0){
                $cateory = Category::create([
                    'name'=> $request->request->get('name'),
                    'parent_id' => $id,
                    'is_enable' => 1,
                    'position' => 10,
                ]);

                DB::commit();

                return redirect()->route('category.index')->with('success','Permission created successfully');
            }

            $newPosition = $categoryOrder->firstOrFail()->position + 10;

            $cateory = Category::create([
                'name'=> $request->request->get('name'),
                'parent_id' => $id,
                'is_enable' => 1,
                'position' => $newPosition,
            ]);

            DB::commit();
        }catch (\Exception $e){
            DB::rollback();

            return redirect()->route('category.create')->with('error',$e->getMessage());
        }

        return redirect()->route('category.index')->with('success','Permission created successfully');
    }


    public function sub_show(Category $category, Category $subcategory)
    {
        $parentCategory = Category::findOrFail($category->id);

        $subcategory = Category::findOrFail($subcategory->id);

        return view('Admin.Category.show_subcategory')->with('parentCategory',$parentCategory)->with('subcategory',$subcategory);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sub_edit(Category $category, Category $subcategory)
    {
        $parentCategory = Category::findOrFail($category->id);

        $subcategory = Category::findOrFail($subcategory->id);

        return view('Admin.Category.edit')->with('parentCategory',$parentCategory)->with('subcategory',$subcategory);
    }
}

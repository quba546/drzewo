<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('parent_id', '=', 0)->get();
        $allCategories = Category::pluck('title', 'id')->all();

        return view('categoryTreeView', compact('categories', 'allCategories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:categories'
        ]);

        $input = $request->all();
        $input['parent_id'] = empty($input['parent_id']) ? 0 : $input['parent_id'];

        Category::create($input);

        return back()->with('success', 'New Category added successfully');
    }

    public function moveUp(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'parent_id' => 'required|integer'
        ]);

        $parent = Category::where('id', '=', $request->parent_id)->firstOrFail('parent_id');

        Category::where('id', '=', $request->id)->update(['parent_id' => $parent->parent_id]);

        return back()->with('success', 'Moved to upper category');
    }

    public function move(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required|integer',
            'parent_id' => 'required|integer'
        ]);

        if ($request->category_id !== $request->parent_id) {
            Category::where('id', '=', $request->category_id)
                ->update(['parent_id' => $request->parent_id]);

            return back()->with('success', 'Moved to other category');
        }

        return back()->with('error', 'Can\'t move to the same category and parent');
    }

    public function destroy(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        function delete(int $id)
        {
            Category::where('id', '=', $id)->delete();
            $ids = Category::where('parent_id', '=', $id)->get('id')->toArray();
            foreach ($ids as $id) {
                $id = $id['id'];
                Category::where('id', '=', $id)->delete();
                if (Category::where('parent_id', '=', $id)->get()) {
                    delete($id);
                }
            }
        }

        delete($request->id);

        return back()->with('success', 'Deleted selected category');
    }
}


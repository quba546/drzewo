<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'show' => 'nullable|integer'
        ]);

        $branchId = (int) $request->show ?? 0;

        $categories = Category::where('parent_id', '=', $branchId)->get();
        $allCategories = Category::get(['id', 'title', 'parent_id']);

        return view('main', [
            'categories' => $categories,
            'allCategories' => $allCategories,
            'showCategory' => $branchId === 0
                ? 'Root'
                : Category::where('id', '=', $branchId)
                    ->firstOrFail()
                    ->title
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|alpha_num|max:50'
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

        $this->delete((int) $request->id);

        return back()->with('success', 'Deleted selected category');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'newTitle' => 'required|alpha_num|max:50'
        ]);

        Category::find($request->id)->update(['title' => $request->newTitle]);

        return back()->with('success', 'Edited selected category');
    }

    private function delete(int $id)
    {
        Category::where('id', '=', $id)->delete();
        $ids = Category::where('parent_id', '=', $id)->get('id')->toArray();
        foreach ($ids as $id) {
            $id = $id['id'];
            Category::where('id', '=', $id)->delete();
            if (Category::where('parent_id', '=', $id)->get()) {
                $this->delete($id);
            }
        }
    }
}


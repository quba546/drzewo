<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request, User $user): View
    {
        $this->validate($request,
            [
            'show' => 'nullable|integer',
            ],
            [
                'integer' => 'Pole może zawierać tylko liczby całkowite'
            ]
        );

        if ($request->get('sort') === 'asc' || $request->get('sort') === 'desc') {
            if (! Gate::allows('admin', $user)) {
                abort(403);
            }

            $sortDirection = $request->get('sort');
        } else {
            $sortDirection = 'asc';
        }

        $branchId = (int) $request->show ?? 0;

        $categories = Category::where('parent_id', '=', $branchId)
            ->orderBy('title', $sortDirection)
            ->get();
        $allCategories = Category::get(['id', 'title', 'parent_id']);

        return view('main', [
            'categories' => $categories,
            'allCategories' => $allCategories,
            'branchId' => $branchId
        ]);
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $this->validate($request,
            [
            'title' => 'required|alpha_num|max:50',
            'addParentId' => 'required|integer'
            ],
            [
                'required' => 'Pole jest wymagane',
                'alpha_num' => 'Pole może zawierać tylko litery i cyfry',
                'max' => 'Pole może zawierać maksymalnie :max znaków',
                'integer' => 'Pole może zawierać tylko liczby całkowite'
            ]
        );

        if(Category::where('id', '=', $request->addParentId)->doesntExist() && $request->addParentId != 0) {
            return back()->with('error', 'Nie ma takiego rodzica');
        }

        Category::create([
            'title' => $request->title,
            'parent_id' => $request->addParentId
        ]);

        return back()->with('success', 'Nowa kategoria została dodana');
    }

    public function moveUp(Request $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $this->validate($request,
            [
            'id' => 'required|integer',
            'parent_id' => 'required|integer'
            ],
            [
                'required' => 'Pole jest wymagane',
                'integer' => 'Pole może zawierać tylko liczby całkowite'
            ]
        );

        $parent = Category::where('id', '=', $request->parent_id)->firstOrFail('parent_id');

        Category::where('id', '=', $request->id)->update(['parent_id' => $parent->parent_id]);

        return back()->with('success', 'Przeniesiono poziom wyżej');
    }

    public function move(Request $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $this->validate($request,
            [
            'moveId' => 'required|integer',
            'parentId' => 'required|integer'
            ],
            [
                'required' => 'Pole jest wymagane',
                'integer' => 'Pole może zawierać tylko liczby całkowite'
            ]
        );

        if(Category::where('id', '=', $request->moveId)->doesntExist() && $request->moveId != 0) {
            return back()->with('error', 'Nie ma takiej kategorii');
        }

        if(Category::where('id', '=', $request->parentId)->doesntExist() && $request->parentId != 0) {
            return back()->with('error', 'Nie ma takiego rodzica');
        }

        if ($request->moveId === $request->parentId) {
            return back()->with('error', 'Nie można przenieść kategorii do niej samej');
        }

        Category::where('id', '=', $request->moveId)
            ->update(['parent_id' => $request->parentId]);

        return back()->with('success', 'Przeniesiono do innej gałęzi');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $this->validate($request,
            [
            'id' => 'required|integer'
            ],
            [
                'required' => 'Pole jest wymagane',
                'integer' => 'Pole może zawierać tylko liczby całkowite'
            ]
        );

        if(Category::where('id', '=', $request->id)->doesntExist() && $request->id != 0) {
            return back()->with('error', 'Nie ma takiej kategorii');
        }

        $this->delete((int) $request->id);

        return back()->with('success', 'Usunięto kategorię');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $this->validate($request,
            [
            'editId' => 'required|integer',
            'newTitle' => 'required|alpha_num|max:50'
            ],
            [
                'required' => 'Pole jest wymagane',
                'alpha_num' => 'Pole może zawierać tylko litery i cyfry',
                'max' => 'Pole może zawierać maksymalnie :max znaków',
                'integer' => 'Pole może zawierać tylko liczby całkowite'
            ]
        );

        if(Category::where('id', '=', $request->editId)->doesntExist() && $request->editId != 0) {
            return back()->with('error', 'Nie ma takiej kategorii');
        }

        Category::find($request->editId)->update(['title' => $request->newTitle]);

        return back()->with('success', 'Zmieniono nazwę kategorii');
    }

    private function delete(int $id): void
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

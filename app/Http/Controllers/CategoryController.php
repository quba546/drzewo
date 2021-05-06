<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DestroyRequest;
use App\Http\Requests\MoveRequest;
use App\Http\Requests\MoveUpRequest;
use App\Http\Requests\ShowRequest;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(ShowRequest $request, User $user): View
    {
        $validated = $request->validated();

        if ($request->get('sort') === 'asc' || $request->get('sort') === 'desc') {
            if (! Gate::allows('admin', $user)) {
                abort(403);
            }

            $sortDirection = $request->get('sort');
        } else {
            $sortDirection = 'asc';
        }

        $branchId = $validated['show'] ?? 0;

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

    public function store(StoreRequest $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $validated = $request->validated();

        if(Category::where('id', '=', $validated['addParentId'])->doesntExist() && $validated['addParentId'] != 0) {
            return back()->with('error', 'Nie ma takiego rodzica');
        }

        Category::create([
            'title' => $validated['title'],
            'parent_id' => $validated['addParentId']
        ]);

        return back()->with('success', 'Nowa kategoria została dodana');
    }

    public function moveUp(MoveUpRequest $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $validated = $request->validated();

        $parent = Category::where('id', '=', $validated['parent_id'])->firstOrFail('parent_id');

        Category::where('id', '=', $validated['id'])->update(['parent_id' => $parent->parent_id]);

        return back()->with('success', 'Przeniesiono poziom wyżej');
    }

    public function move(MoveRequest $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $validated = $request->validated();

        if(Category::where('id', '=', $validated['moveId'])->doesntExist() && $validated['moveId'] != 0) {
            return back()->with('error', 'Nie ma takiej kategorii');
        }

        if(Category::where('id', '=', $validated['parentId'])->doesntExist() && $validated['parentId'] != 0) {
            return back()->with('error', 'Nie ma takiego rodzica');
        }

        if ($validated['moveId'] === $validated['parentId']) {
            return back()->with('error', 'Nie można przenieść kategorii do niej samej');
        }

        Category::where('id', '=', $validated['moveId'])
            ->update(['parent_id' => $validated['parentId']]);

        return back()->with('success', 'Przeniesiono do innej gałęzi');
    }

    public function destroy(DestroyRequest $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $validated = $request->validated();

        if(Category::where('id', '=', $validated['id'])->doesntExist() && $validated['id'] != 0) {
            return back()->with('error', 'Nie ma takiej kategorii');
        }

        $this->delete((int) $validated['id']);

        return back()->with('success', 'Usunięto kategorię');
    }

    public function update(UpdateRequest $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $validated = $request->validated();

        if(Category::where('id', '=', $validated['editId'])->doesntExist() && $validated['editId'] != 0) {
            return back()->with('error', 'Nie ma takiej kategorii');
        }

        Category::find($validated['editId'])->update(['title' => $validated['newTitle']]);

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

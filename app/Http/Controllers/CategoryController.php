<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DestroyRequest;
use App\Http\Requests\MoveRequest;
use App\Http\Requests\MoveUpRequest;
use App\Http\Requests\ShowRequest;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\User;
use App\Repository\CategoryRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private CategoryRepositoryInterface $repository)
    {
    }

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

        $branchId = isset($validated['show']) ? (int) $validated['show'] : 0;

        $categories = $this->repository->getAllStructured($branchId, $sortDirection);

        $allCategories = $this->repository->getAll([
            'id',
            'title',
            'parent_id'
        ]);

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

        if($this->repository->checkIfDoesntExist((int) $validated['addParentId']) && $validated['addParentId'] != 0) {
            return back()->with('error', 'Nie ma takiego rodzica');
        }

        $this->repository->insert($validated['title'], (int) $validated['addParentId']);

        return back()->with('success', 'Nowa kategoria została dodana');
    }

    public function moveUp(MoveUpRequest $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $validated = $request->validated();

        if ((int) $validated['id'] === 0) {
            return back()->with('error', 'Nie można przenieść wyżej korzenia');
        }

        if($this->repository->checkIfDoesntExist((int) $validated['id']) && $validated['id'] != 0) {
            return back()->with('error', 'Nie ma takiej kategorii');
        }

        if($this->repository->checkIfDoesntExist((int) $validated['parent_id']) && $validated['parent_id'] != 0) {
            return back()->with('error', 'Nie ma takiego rodzica');
        }

        $parent = $this->repository->getOne((int) $validated['parent_id']);

        $this->repository->edit((int) $validated['id'], 'parent_id', $parent->parent_id);

        return back()->with('success', 'Przeniesiono poziom wyżej');
    }

    public function move(MoveRequest $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $validated = $request->validated();

        if ((int) $validated['moveId'] === 0) {
            return back()->with('error', 'Nie można przenieść korzenia');
        }

        if($this->repository->checkIfDoesntExist((int) $validated['moveId']) && $validated['moveId'] != 0) {
            return back()->with('error', 'Nie ma takiej kategorii');
        }

        if($this->repository->checkIfDoesntExist((int) $validated['parentId']) && $validated['parentId'] != 0) {
            return back()->with('error', 'Nie ma takiego rodzica');
        }

        if ($validated['moveId'] === $validated['parentId']) {
            return back()->with('error', 'Nie można przenieść kategorii do niej samej');
        }

        $this->repository->edit((int) $validated['moveId'], 'parent_id', (int) $validated['parentId']);

        return back()->with('success', 'Przeniesiono do innej gałęzi');
    }

    public function destroy(DestroyRequest $request, User $user): RedirectResponse
    {
        if (! Gate::allows('admin', $user)) {
            abort(403);
        }

        $validated = $request->validated();

        if ((int) $validated['id'] === 0) {
            return back()->with('error', 'Nie można usunąć korzenia');
        }

        if($this->repository->checkIfDoesntExist((int) $validated['id']) && $validated['id'] != 0) {
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

        if ((int) $validated['editId'] === 0) {
            return back()->with('error', 'Nie można zmienić nazwy korzenia');
        }

        if($this->repository->checkIfDoesntExist((int) $validated['editId']) && $validated['editId'] != 0) {
            return back()->with('error', 'Nie ma takiej kategorii');
        }

        $this->repository->edit((int) $validated['editId'], 'title', $validated['newTitle']);

        return back()->with('success', 'Zmieniono nazwę kategorii');
    }

    private function delete(int $id): void
    {
        $this->repository->delete($id);

        $ids = $this->repository->getAllChildren($id, ['id'])->toArray();
        foreach ($ids as $id) {
            $id = $id['id'];

            $this->repository->delete($id);

            if ($this->repository->getAllChildren($id, ['id'])) {
                $this->delete($id);
            }
        }
    }
}

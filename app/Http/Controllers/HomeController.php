<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $category = new Category();
        //$result = $category->where('id','=',1)->firstOrFail(['left', 'right']);

        $result = $category
            ->selectRaw("child.name, (COUNT(parent.name) - 1) AS depth")
            ->fromRaw("categories as child, categories as parent")
            ->whereRaw("(child.left BETWEEN parent.left AND parent.right)")
            ->groupByRaw("child.name")
            ->orderByRaw("child.left")
            ->get(['name', 'depth']);

        //dd($result);

        return view('front', [
            'tree' => $result
        ]);
    }
}

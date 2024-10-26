<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchProductController extends Controller
{
    //
    public function index()
    {

        // update to filter the results based on the query

        $query_str = request('query');
        $items = Product::when($query_str, function ($query, $query_str) {
            return $query->where('name', 'LIKE', "%{$query_str}%");
        })->get();
        return view('search', compact('items', 'query_str'));
    }
}

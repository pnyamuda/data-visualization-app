<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //



    public function store(Request $request)
    {

        $request->validate([
            'categoryName' => 'required',
            'survey_id' => "required|numeric",

        ], [
            'categoryName.required' => 'Required.',
            'survey_id.required' => 'Required.'
        ]);


        Category::create([
            'category_name' => $request->categoryName,
            'survey_type_id' => $request->survey_id
        ]);
    }

    public function show(Request $request)
    {
        $categories = Category::select('category_id', 'category_name')
            ->where('survey_type_id', $request->the_survey_id)
            ->get();

        return $categories;
    }
}

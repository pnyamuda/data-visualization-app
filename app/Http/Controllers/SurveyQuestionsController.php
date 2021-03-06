<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class SurveyQuestionsController extends Controller
{
    //getting the category names for the questions the user hasn't answered yet.
    public function userCategories()
    {
        //logged in user id
        $user_id = Auth::id();
        //getting the questions the logged in user has answered
        $answered_questions = Answer::select('question_id')
            ->where('user_id', $user_id)
            ->get();

        //getting the category names for those questions
        $all_categories = Question::select(
            'category_name',
            'category_questions.category_id',
            'category_description',
            'category_questions.survey_type_id',
            'survey_type_name'
        )
            //join with the category table to get the 'type' and 'category_name'
            ->join('category_questions', 'category_questions.category_id', '=', 'questions.category_id')
            //join with the survey_types table to get the survey_type_id
            ->join('survey_types', 'survey_types.id', '=', 'category_questions.survey_type_id')
            ->whereNotIn('question_id', $answered_questions)
            ->distinct()
            ->orderBy('category_name')
            ->get();
        //using a colection to get unique values of category names.
        $collection = collect($all_categories);
        $unique = $collection->unique('category_name');
        return $unique->values()->all();
    }
    //fetching survey questions from the database
    public function show(Request $request)
    {

        //logged in user id
        $user_id = Auth::id();

        //getting the questions the logged in user has answered
        $answered_questions = Answer::select('question_id')
            ->where('user_id', $user_id)
            ->get();

        //then using that to get the questions the user hasn't answered
        $all_questions = Question::select('question_id', 'question', 'questions.category_id', 'question_type_name AS type', 'category_name')
            //join with the category table to get the 'type' and 'category_name'
            ->join('category_questions', 'category_questions.category_id', '=', 'questions.category_id')
            ->join('question_types', 'questions.question_type_id', '=', 'question_types.question_type_id')
            ->whereNotIn('question_id', $answered_questions)->get();


        return $all_questions;
    }
    //save a question to the database
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|unique:questions,question',
            'category_id' => 'required|numeric',
            'question_type_id' => 'required|numeric',
            'survey_type_id' => 'required'
        ], [
            'question.unique' => 'The question has already been added.',
            'category_id.required' => 'Required.',
            'question_type_id.required' => 'Required.',
            'survey_type_id.required' => 'Required.'
        ]);
        Question::create([
            'question' => $request->question,
            'category_id' => $request->category_id,
            'question_type_id' => $request->question_type_id
        ]);
    }


    public function add(Request $request)
    {
        $answers = $request->all();
        foreach ($answers as $key => $val) {
            $question_id = $answers[$key]['question_id'];
            $category_id = $answers[$key]['category_id'];
            $answer_value = $answers[$key]['question_answer'];
            $user_id = $answers[$key]['user_id'];

            Answer::create([
                'user_id' => $user_id,
                'question_id' => $question_id,
                'category_id' => $category_id,
                'answer_value' => $answer_value
            ]);
        }
    }
}

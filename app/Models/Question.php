<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /* since we're are using the create method to store a record in the controller */
    protected $fillable = ['question', 'category_id', 'question_type_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'question_id');
    }

    public function question_type()
    {
        return $this->belongsTo(QuestionType::class);
    }
}

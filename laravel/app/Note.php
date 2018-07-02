<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Note extends Model
{
    public $timestamps = false;

    //form validation - insert/update note
    public static function validateNoteForm($company_id = false, $id = false)
    {
        $rules = [
            'text' => 'required'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('notes', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id); })];
        }

        return $rules;
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $table
 */
abstract class EditRequest extends FormRequest
{
    function unique($column = 'name')
    {
        $unique = Rule::unique($this->table, $column);
        $id     = $this->route()->parameter('id');
        if ($id !== null) {
            $unique = $unique->ignore($id);
        }
        return $unique;
    }
}
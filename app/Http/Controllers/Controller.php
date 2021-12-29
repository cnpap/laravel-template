<?php

namespace App\Http\Controllers;

use App\Models\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @property Model $model
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function status()
    {
        $this->model::status();
        return ss();
    }

    function find($id)
    {
        $one = $this->model::query()->where('id', $id)->firstOrFail();
        return result($one);
    }

    function delete()
    {
        $this->model::del();
        return ss();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryEditRequest;
use App\Http\Requests\CategoryIndexRequest;
use App\Models\Category;
use RuntimeException;

/**
 * @property Category $categoryName
 */
abstract class CategoryController extends Controller
{
    public function tree()
    {
        $item = $this->categoryName::query()->with('parent')->get();
        return result(tree($item));
    }

    public function list(CategoryIndexRequest $request)
    {
        $many = $this->categoryName::indexFilter($request->validated())->paginate(...usePage());
        return page($many);
    }

    public function create(CategoryEditRequest $request)
    {
        $one = new $this->categoryName($request->validated());
        (new Category())->getConnection()->transaction(function () use ($one) {
            /** @var Category $one */
            $one->id = uni();
            $ok      = $one->save();
            if (!$ok) {
                throw new RuntimeException();
            }
            if ($one->pid) {
                $child = $one;
                $keys  = [];
                for ($i = 0; $i < 5; $i++) {
                    $keys[] = $child->pid;
                    $child = $child->parent;
                    if (!$child->pid || $child->status !== _NEW) {
                        break;
                    }
                }
                $this->categoryName::whereIn('id', $keys)->where('status', _NEW)->update(['status' => _USED]);
            }
        });
        return ss();
    }

    public function find($id)
    {
        $one = $this->categoryName::where('id', $id)->firstOrFail();
        return result($one);
    }

    public function update(CategoryEditRequest $request, $id)
    {
        $this->categoryName::query()->where('id', $id)->update($request->validated());
        return ss();
    }

    public function delete()
    {
        $this->categoryName::clear();
        return ss();
    }
}
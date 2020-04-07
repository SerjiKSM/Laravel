<?php

namespace App\Http\Controllers\Site;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\CategoryContract;

use Illuminate\Support\Collection;

class CategoryController extends Controller
{
    protected $categoryRepository;

    protected $categoryData = array();

    public function __construct(CategoryContract $categoryRepository, array $categoryData = array())
    {
        $this->categoryRepository = $categoryRepository;
        $this->categoryData = Category::all()->sortBy('id')->toArray();

    }

    public function show($slug)
    {
        $category = $this->categoryRepository->findBySlug($slug);

        return view('site.pages.category', compact('category'));
    }


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function showTestMenu()
    {
        $menus = '';
        $menus .= $this->multiLevelMenus();

        $selectMenus = '';
        $selectMenus .= "<select name='category'>
                            <option value='0'>Select a category</option>"
                            . $this->categoryTree() .
                        "</select>";

        $categories = Category::orderByRaw('-name ASC')->get()->nest();
//        dd($categories);
        $treeMenus = '';
        $treeMenus .= $this->treeMenus($categories);

        return view('site.pages.category_recursive', compact('menus', 'selectMenus', 'treeMenus'));

    }

    public function multiLevelMenus(int $parentId = 0)
    {
        $menu = '';
        $categories = array();
        if (empty($parentId)) {
//            $categories = Category::all()
//                ->where('parent_id', '=', '\'\'')->sortBy('id')->toArray();
            $categories = $this->findInArray(0);
        } else {
//            $categories = Category::all()
//                ->where('parent_id', '=', $parentId)->sortBy('id')->toArray();
            $categories = $this->findInArray($parentId);
        }

        foreach ($categories as $category) {
            $menu .= '<li><a href="">' . $category['name'] . '</a>';

            $menu .= '<ul class="dropdown">'
                . $this->multiLevelMenus($category['id']) . '</ul>';

            $menu .= '</li>';
        }

        return $menu;
    }

    public function findInArray($parentId)
    {
        $arrCategories = array();
        foreach ($this->categoryData as $categoryData){
            if($parentId == $categoryData['parent_id']){
                array_push($arrCategories, $categoryData);
            }
        }
        return $arrCategories;
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function categoryTree(&$output = null, $parentId = 0, $indent = null)
    {
//        $categories = Category::all()
//            ->where('parent_id', '=', $parentId)->sortBy('id')->toArray();
        $categories = $this->findInArray($parentId);
        ///////////////////
        foreach ($categories as $category) {
            $output .= '<option value=' . $category['id'] . '>' . $indent . $category['name'] . '</option>';

            if ($category['id'] != $parentId) {
                $this->categoryTree($output, $category['id'], $indent . "&nbsp;&nbsp;");
            }
        }

        return $output;
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function treeMenus(Collection $categories)
    {
        $treeMenus = '';
        foreach ($categories as $cat) {
            $treeMenus .= '<li><a href="">' . $cat['name'] . '</a></li>';
            foreach ($cat->items as $category) {

                $treeMenus .= '<li><a href="">' . $category['name'] . '</a>';

                    $treeMenus .= '<ul class="dropdown">'
                        . $this->treeMenus($category->items)
                        . '</ul>';

                $treeMenus .= '</li>';
            }
        }
        return $treeMenus;
    }
}

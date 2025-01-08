<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\PostAdminModel;
use App\Models\PostModel;
use App\Models\SettingsModel;

class CategoryController extends BaseAdminController
{
    protected $categoryModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Categories
     */
    public function addCategory()
    {
        checkPermission('categories');
        $data['title'] = trans("add_category");
        $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($this->activeLang->id);

        $data['type'] = inputGet('type');
        if (empty($data['type']) || $data['type'] != 'sub') {
            $data['type'] = 'parent';
        }
        $settingsModel = new SettingsModel();
        $data['widgets'] = $settingsModel->getWidgets();

        $postModel = new PostModel();
        $data['posts'] = $postModel->getPostsByLang($this->activeLang->id);

        echo view('admin/includes/_header', $data);
        echo view('admin/category/add_category', $data);
        echo view('admin/includes/_footer');
    }

    public function addCategory2()
    {
        checkPermission('categories');
        $data['title'] = trans("add_category");
        $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($this->activeLang->id);

        $data['type'] = inputGet('type');
        if (empty($data['type']) || $data['type'] != 'sub') {
            $data['type'] = 'parent';
        }
        $settingsModel = new SettingsModel();
        $data['widgets'] = $settingsModel->getWidgets();

        echo view('admin/includes/_header', $data);
        echo view('admin/category/multiple_category/add_category2', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Category Post
     */
    public function addCategoryPost()
    {
        checkPermission('categories');
        $type = inputPost('type');
        if (empty($type) || $type != 'sub') {
            $type = 'parent';
        }
        $val = \Config\Services::validation();
        $val->setRule('name', trans("category_name"), 'required|max_length[200]');

        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('add-category?type=' . $type))->withInput();
        } else {
            if ($this->categoryModel->addCategory($type)) {
                $this->session->setFlashdata('success', trans("msg_added"));
                resetCacheDataOnChange();
            } else {
                $this->session->setFlashdata('error', trans("msg_error"));
            }
        }
        return redirect()->to(adminUrl('add-category?type=' . $type));
    }

    public function addCategoryPost2()
    {
        checkPermission('categories');
        
        if ($this->categoryModel->addCategory2()) {
            resetCacheDataOnChange();
            return $this->response->setJSON(['success' => trans("msg_added")]);
        } else {
            return $this->response->setJSON(['error' => trans("msg_error")]);
        }
    }

    /**
     * Categories
     */
    public function categories()
    {
        checkPermission('categories');
        $data['title'] = trans("categories");

        $numRows = $this->categoryModel->getCategoriesCount();
        $pager = paginate($this->perPage, $numRows);
        $data['categories'] = $this->categoryModel->getCategoriesPaginated($this->perPage, $pager->offset);

        $langId = cleanNumber(inputGet('lang_id'));
        if(!empty($langId)){
            $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($langId);
        }else{
            $data['parentCategories'] = $this->categoryModel->getParentCategories();
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/category/categories', $data);
        echo view('admin/includes/_footer');
    }

    public function categories2()
    {
        checkPermission('categories');
        $data['title'] = trans("categories");

        $numRows = $this->categoryModel->getCategoriesCount();
        $pager = paginate($this->perPage, $numRows);
        $data['categories'] = $this->categoryModel->getCategoriesPaginated($this->perPage, $pager->offset);

        $langId = cleanNumber(inputGet('lang_id'));
        // if(!empty($langId)) {
        $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang(empty($langId) ? $this->activeLang->id : $langId);
        // }else{
        //     $data['parentCategories'] = $this->categoryModel->getParentCategories();
        // }

        echo view('admin/includes/_header', $data);
        echo view('admin/category/multiple_category/categories2', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Category
     */
    public function editCategory($id)
    {
        checkPermission('categories');
        $data['title'] = trans("update_category");

        $category = $this->categoryModel->getCategory($id);
        
        $data['category'] =$category;
        if (empty($data['category'])) {
            return redirect()->to(adminUrl('categories'));
        }
        $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($data['category']->lang_id);
        $settingsModel = new SettingsModel();
        $data['widgets'] = $settingsModel->getWidgets();

        $postAdminModel = new PostAdminModel();
        $data['selectedPost'] = $postAdminModel->getSelectedPostCategory($id);

        echo view('admin/includes/_header', $data);
        echo view('admin/category/edit_category', $data);
        echo view('admin/includes/_footer');
    }

    public function editCategory2($id)
    {
        checkPermission('categories');
        $data['title'] = trans("update_category");

        $categories = $this->categoryModel->getCategoryByDefinition($id);

        $data['categories'] =$categories;
        if (empty($data['categories'])) {
            return redirect()->to(adminUrl('categories'));
        }
        
        $categoryDataByLang = [];
        foreach ($categories as $category) {
            $categoryDataByLang[$category['lang_id']] = $category;
        }

        $data['categoryDataByLang'] = $categoryDataByLang;

        $data['definition_id'] = $id;

        
        $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($this->activeLang->id);

        $postAdminModel = new PostAdminModel();
        $data['selectedPost'] = $postAdminModel->getSelectedPostCategoryByDefinition($id);

        echo view('admin/includes/_header', $data);
        echo view('admin/category/multiple_category/edit_category2', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Category Post
     */
    public function editCategoryPost()
    {
        checkPermission('categories');
        $val = \Config\Services::validation();
        $val->setRule('name', trans("category_name"), 'required|max_length[200]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            redirectToBackURL();
        } else {
            $id = inputPost('id');
            if ($this->categoryModel->editCategory($id)) {
                $this->session->setFlashdata('success', trans("msg_updated"));
                resetCacheDataOnChange();
                redirectToBackURL();
            }
        }
        $this->session->setFlashdata('error', trans("msg_error"));
        redirectToBackURL();
    }

    public function editCategoryPost2()
    {
        if ($this->categoryModel->editCategory2()) {
            resetCacheDataOnChange();
            return $this->response->setJSON(['success' => trans("msg_updated")]);
        } else {
            return $this->response->setJSON(['error' => trans("msg_error")]);
        }
    }

    /**
     * Subcategories
     */
    public function subCategories()
    {
        checkPermission('categories');
        $data['title'] = trans("subcategories");
        $data['categories'] = $this->categoryModel->getSubCategories();
        $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($this->activeLang->id);
        $data['langSearchColumn'] = 2;

        echo view('admin/includes/_header', $data);
        echo view('admin/category/subcategories', $data);
        echo view('admin/includes/_footer');
    }

    //get parent categories by language
    public function getParentCategoriesByLang()
    {
        $langId = inputPost('lang_id');
        if (!empty($langId)):
            $categories = $this->categoryModel->getParentCategoriesByLang($langId);
            if (!empty($categories)) {
                foreach ($categories as $item) {
                    echo '<option value="' . $item->id . '">' . $item->name . '</option>';
                }
            }
        endif;
    }

    //get subcategories
    public function getSubCategories()
    {
        $parentId = inputPost('parent_id');
        if (!empty($parentId)) {
            $subCategories = $this->categoryModel->getSubCategoriesByParentId($parentId);
            foreach ($subCategories as $item) {
                echo '<option value="' . $item->id . '">' . $item->name . '</option>';
            }
        }
    }

    public function getSubCategories2()
    {
        $json = $this->request->getJSON();
        $parentId = $json->parentId ?? null;

        if (!empty($parentId)) {
            $category = $this->categoryModel->getCategory($parentId);
            $subCategories = $this->categoryModel->getSubCategoriesByDefinitionIdAndLang($category->definition_id);
            if (!empty($subCategories)) {
                return $this->response->setJSON($subCategories);
            }
        }
    
        return $this->response->setJSON([]);
    }

    /**
     * Delete Category Post
     */
    public function deleteCategoryPost()
    {
        if (!checkUserPermission('categories')) {
            exit();
        }
        $id = inputPost('id');
        if (!empty($this->categoryModel->getSubCategoriesByParentId($id))) {
            $this->session->setFlashdata('error', trans("msg_delete_subcategories"));
            exit();
        }
        $postModel = new PostModel();
        $categories = $this->categoryModel->getCategories();
        if (!empty($postModel->getPostCountByCategory($id, $categories))) {
            $this->session->setFlashdata('error', trans("msg_delete_posts"));
            exit();
        }
        if ($this->categoryModel->deleteCategory($id)) {
            $this->session->setFlashdata('success', trans("msg_deleted"));
            resetCacheDataOnChange();
        } else {
            $this->session->setFlashdata('error', trans("msg_error"));
        }
    }

    public function deleteCategoryPost2()
    {
        if (!checkUserPermission('categories')) {
            exit();
        }
        $id = inputPost('id');

        $categories = $this->categoryModel->getCategoryByDefinition($id);

        if(!empty($categories)) {
            foreach ($categories as $category) {
                if (!empty($this->categoryModel->getSubCategoriesByParentId($category['id']))) {
                    $this->session->setFlashdata('error', trans("msg_delete_subcategories"));
                    exit();
                }

                $postModel = new PostModel();
                $categories2 = $this->categoryModel->getCategories();
                if (!empty($postModel->getPostCountByCategory($category['id'], $categories2))) {
                    $this->session->setFlashdata('error', trans("msg_delete_posts"));
                    exit();
                }
                $this->categoryModel->deleteCategory($category['id']);
            }
            if ($this->categoryModel->deleteCategoryDefinition($id)) {
                $this->session->setFlashdata('success', trans("msg_deleted"));
                resetCacheDataOnChange();
            } else {
                $this->session->setFlashdata('error', trans("msg_error"));
            }
        }
    }

}

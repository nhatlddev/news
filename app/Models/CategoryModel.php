<?php namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('categories');
    }

    //input values
    public function inputValues()
    {
        $data = [
            'lang_id' => inputPost('lang_id'),
            'name' => inputPost('name'),
            'name_slug' => inputPost('name_slug'),
            'parent_id' => inputPost('parent_id'),
            'description' => inputPost('description'),
            'keywords' => inputPost('keywords'),
            'color' => inputPost('color'),
            'category_order' => inputPost('category_order'),
            'show_at_homepage' => inputPost('show_at_homepage'),
            'show_on_menu' => inputPost('show_on_menu'),
            'block_type' => inputPost('block_type'),
            'show_at_footer' => inputPost('show_at_footer'),
            'show_at_body_sort' => inputPost('show_at_body_sort')
        ];
        if (empty($data['color'])) {
            $data['color'] = '#2d65fe';
        }
        return $data;
    }

    //add category
    public function addCategory($type)
    {
        $data = $this->inputValues();
        if (empty($data["name_slug"])) {
            $data["name_slug"] = strSlug($data["name"]);
        } else {
            $data["name_slug"] = removeSpecialCharacters($data["name_slug"], true);
            if (!empty($data['name_slug'])) {
                $data['name_slug'] = str_replace(' ', '-', $data['name_slug']);
            }
        }
        if (!empty($data['parent_id'])) {
            $parent = $this->getCategory($data['parent_id']);
            if (!empty($parent)) {
                $data['color'] = $parent->color;
            }
            $data['block_type'] = '';
            // $data['show_at_homepage'] = 0;
        } else {
            $data['parent_id'] = 0;
        }

        if (!empty($_FILES['files'])) {
            $uploadModel = new UploadModel();
            $fileCount = count($_FILES['files']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if (isset($_FILES['files']['name'])) {
                    $tmpFilePath = $_FILES['files']['tmp_name'][$i];
                    if (isset($tmpFilePath)) {
                        $ext = $uploadModel->getFileExtension($_FILES['files']['name'][$i]);
                        $newName = 'temp_' . generateToken() . '.' . $ext;
                        $newPath = FCPATH . "uploads/tmp/" . $newName;
                        if (move_uploaded_file($tmpFilePath, FCPATH . "uploads/tmp/" . $newName)) {
                            if ($ext == 'gif') {
                                $gifPath = $uploadModel->uploadGIF($newName, 'category');
                                $data["file_path"] = $gifPath;
                                $data["file_name"] = $newName;
                            } else {
                                $data["file_path"] = $uploadModel->uploadCategoryImage($newPath, 1920,400);
                                $data["file_name"] = $newName;
                            }
                        }

                        $data["storage"] = $this->generalSettings->storage;

                        $uploadModel->deleteTempFile($newPath);
                        //move to s3
                        if ($data['storage'] == 'aws_s3') {
                            $awsModel = new AwsModel();
                            if (!empty($data['file_path'])) {
                                $awsModel->uploadFile($data['file_path']);
                            }
                        }
                    }
                }
            }
        }

        if ($this->builder->insert($data)) {
            $lastId = $this->db->insertID();
            $this->updateSlug($lastId);
            return true;
        }
        return false;
    }

    //edit category
    public function editCategory($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            $data = $this->inputValues();
            if (empty($data["name_slug"])) {
                $data["name_slug"] = strSlug($data["name"]);
            } else {
                $data["name_slug"] = removeSpecialCharacters($data["name_slug"], true);
                if (!empty($data['name_slug'])) {
                    $data['name_slug'] = str_replace(' ', '-', $data['name_slug']);
                }
            }
            if (!empty($data['parent_id'])) {
                $parent = $this->getCategory($data['parent_id']);
                if (!empty($parent)) {
                    $data['color'] = $parent->color;
                }
                $data['block_type'] = '';
                // $data['show_at_homepage'] = 0;
            } else {
                $data['parent_id'] = 0;
                $this->updateSubCategoriesColor($id, $data['color']);
                //update subcategories lang_id
                $this->builder->where('parent_id', $category->id)->update(['lang_id' => $data['lang_id']]);
            }

            $uploadModel = new UploadModel();
            $tempData = $uploadModel->uploadTempFile('file', true);
            if (!empty($tempData)) {
                $tempPath = $tempData['path'];
                
                if ($tempData['ext'] == 'gif') {
                    $gifPath = $uploadModel->uploadGIF($tempData['name'], 'banner');
                    $data["file_path"] = $gifPath;
                    $data["file_name"] = $tempData['name'];
                } else {
                    $data["file_path"] = $uploadModel->uploadCategoryImage($tempPath, 1920, 400);
                    $data["file_name"] = $tempData['name'];
                }
                $data["storage"] = $this->generalSettings->storage;
                $this->deleteImageFile($id);
                $uploadModel->deleteTempFile($tempPath);
                //move to s3
                if ($data['storage'] == 'aws_s3') {
                    $awsModel = new AwsModel();
                    if (!empty($data['file_path'])) {
                        $awsModel->uploadFile($data['file_path']);
                    }
                }
            }

            $this->builder->where('id', $category->id)->update($data);
            $this->updateSlug($category->id);
            return true;
        }
        return false;
    }

    public function deleteImageFile($id)
    {
        $image = $this->getCategory($id);
        if (!empty($image)) {
            if ($image->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $awsModel->deleteFile($image->file_path);
            } else {
                @unlink(FCPATH . $image->file_path);
            }
        }
    }

    public function deleteTempFile($path)
    {
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    //update slug
    public function updateSlug($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            if (empty($category->name_slug) || $category->name_slug == "-") {
                $data = [
                    'name_slug' => $category->id
                ];
                $this->builder->where('id', $category->id)->update($data);
            } else {
                if ($this->checkSlugExists($category->name_slug, $category->id)) {
                    $data = [
                        'name_slug' => $category->name_slug . "-" . $category->id
                    ];
                    $this->builder->where('id', $category->id)->update($data);
                }
            }
        }
    }

    //check slug
    public function checkSlugExists($slug, $id)
    {
        if (!empty($this->builder->where('name_slug', cleanStr($slug))->where('id !=', cleanNumber($id))->get()->getRow())) {
            return true;
        }
        return false;
    }

    //update subcategory color
    public function updateSubCategoriesColor($parentId, $color)
    {
        $categories = $this->getSubCategoriesByParentId($parentId);
        if (!empty($categories)) {
            foreach ($categories as $item) {
                $this->builder->where('id', $item->id)->update(['color' => $color]);
            }
        }
    }

    //build query
    public function buildQuery()
    {
        $this->builder->select('categories.*, (SELECT name_slug FROM categories AS tbl_categories WHERE tbl_categories.id = categories.parent_id) as parent_slug');
    }

    //get category
    public function getCategory($id)
    {
        $this->buildQuery();
        return $this->builder->where('id', cleanNumber($id))->get()->getRow();
    }

    //get category by slug
    public function getCategoryBySlug($slug)
    {
        $this->buildQuery();
        return $this->builder->where('categories.name_slug', cleanSlug($slug))->where('categories.lang_id', cleanNumber($this->activeLang->id))->orderBy('category_order')->get()->getRow();
    }

    public function getCategoryByFooter()
    {
        $this->buildQuery();
        return $this->builder
            ->where('categories.show_at_footer', 1)
            ->where('categories.lang_id', cleanNumber($this->activeLang->id))
            ->orderBy('created_at DESC')->get()->getResult();
    }

    //get categories
    public function getCategories()
    {
        $this->buildQuery();
        return $this->builder->orderBy('category_order')->get()->getResult();
    }

    //get categories by lang
    public function getCategoriesByLang($langId)
    {
        $this->buildQuery();
        return $this->builder->where('categories.lang_id', cleanNumber($langId))->orderBy('category_order')->get()->getResult();
    }


    public function getShowAtMenuCategory($langId)
    {
        $this->buildQuery();
        return $this->builder
            ->where('categories.lang_id', cleanNumber($langId))
            ->where('categories.show_at_homepage', 1)
            // ->orderBy('categories.show_at_body_sort', 'ASC')
            ->get()
            ->getResult();
    }
    
    //get parent categories
    public function getParentCategories()
    {
        return $this->builder->where('parent_id', 0)->orderBy('created_at DESC')->get()->getResult();
    }

    //get parent categories by lang
    public function getParentCategoriesByLang($langId)
    {
        return $this->builder->where('parent_id', 0)->where('lang_id', cleanNumber($langId))->orderBy('name')->get()->getResult();
    }

    //get subcategories
    public function getSubCategories()
    {
        return $this->builder->where('parent_id !=', 0)->get()->getResult();
    }

    //get subcategories by parent id
    public function getSubCategoriesByParentId($parentId)
    {
        return $this->builder->where('parent_id', cleanNumber($parentId))->orderBy('name')->get()->getResult();
    }

    //get categories count
    public function getCategoriesCount()
    {
        $this->filterCategories();
        return $this->builder->countAllResults();
    }

    //get paginated categories
    public function getCategoriesPaginated($perPage, $offset)
    {
        $this->filterCategories();
        return $this->builder->orderBy('id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter categories
    public function filterCategories()
    {
        $q = inputGet('q');
        $langId = cleanNumber(inputGet('lang_id'));
        $parentId = cleanNumber(inputGet('category'));
        if (!empty($q)) {
            $this->builder->like('name', cleanStr($q));
        }
        if (!empty($langId)) {
            $this->builder->where('lang_id', cleanNumber($langId));
        }
        if (!empty($parentId)) {
            $this->builder->where('parent_id', cleanNumber($parentId));
        }
    }

    //delete category
    public function deleteCategory($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            return $this->builder->where('id', $category->id)->delete();
        }
        return false;
    }
}
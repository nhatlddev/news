<?php namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends BaseModel
{
    protected $builder;

    protected $builderDefinition;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('categories');
        $this->builderDefinition = $this->db->table('category_definition');
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
            'show_at_body_sort' => inputPost('show_at_body_sort'),
            'post_id' => inputPost('post_id')
        ];
        if (empty($data['color'])) {
            $data['color'] = '#2d65fe';
        }
        return $data;
    }

    public function addCategory2() {
        $data = $this->inputValues();

        $CategoryDefinitionData = [
            'created_at' => date('Y-m-d H:i:s'),
            'post_definition_id' => !empty($data['post_id']) ? $data['post_id'] : null
        ];

        if($this->builderDefinition->insert($CategoryDefinitionData)) {
            $definitionId = $this->db->insertID();

            if(!empty($definitionId)) {
                $saveData = [];
                $saveData['definition_id'] = $definitionId;
                $saveData['category_order'] = inputPost('category_order') ?? 0;
                $saveData['show_on_menu'] = $data['show_on_menu'];

                $saveData['show_at_homepage'] = $data['show_at_homepage'] ?? 0;
                $saveData['show_at_body_sort'] = $data['show_at_body_sort'] ?? 0;
                
                if(empty($data['parent_id'])) {
                    $saveData['show_at_footer'] = $data['show_at_footer'] ?? 0;
                    $saveData['parent_id'] = 0;
                    $saveData['parent_definition_id'] = null;
                } else {
                    $saveData['show_at_footer'] = 0;

                    $parentCategory = $this->getCategory($data['parent_id']);
                    $saveData['parent_id'] = $data['parent_id'];

                    if($parentCategory) {
                        $saveData['parent_definition_id'] = $parentCategory->definition_id;
                    }
                    
                }

                foreach ($this->activeLanguages as $lang) {
                    $saveData['lang_id'] = inputPost('categories[' . $lang->id . '][lang_id]');
                    $saveData['name'] = inputPost('categories[' . $lang->id . '][name]');
                    $saveData['name_slug'] = inputPost('categories[' . $lang->id . '][slug]');

                    if (empty($saveData["name_slug"])) {
                        $saveData["name_slug"] = strSlug($saveData["name"]);
                    } else {
                        $saveData["name_slug"] = removeSpecialCharacters($saveData["name_slug"], true);
                        if (!empty($saveData['name_slug'])) {
                            $saveData['name_slug'] = str_replace(' ', '-', $saveData['name_slug']);
                        }
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
                                            $saveData["file_path"] = $gifPath;
                                            $saveData["file_name"] = $newName;
                                        } else {
                                            $saveData["file_path"] = $uploadModel->uploadCategoryImage($newPath, 1920,400);
                                            $saveData["file_name"] = $newName;
                                        }
                                    }
            
                                    $saveData["storage"] = $this->generalSettings->storage;
            
                                    $uploadModel->deleteTempFile($newPath);
                                    //move to s3
                                    if ($saveData['storage'] == 'aws_s3') {
                                        $awsModel = new AwsModel();
                                        if (!empty($saveData['file_path'])) {
                                            $awsModel->uploadFile($saveData['file_path']);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($this->builder->insert($saveData)) {
                        $lastId = $this->db->insertID();
                        $this->updateSlug($lastId);
                    }
                }
            }
            
            return true;
        }
    
        return false;
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

        if (empty($data['post_id'])) {
            $data['post_id'] = null;
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
            echo "<script>console.log(" . json_encode($data) . ");</script>";
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

            if (empty($data['post_id'])) {
                $data['post_id'] = null;
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

    public function editCategory2() {
        $data = $this->inputValues();

        $categoryDefinition = $this->getCategoryDefinition(inputPost('definition_id'));

        foreach ($this->activeLanguages as $lang) {
            $category = $this->getCategory(inputPost('categories[' . $lang->id . '][id]'));

            $editData['category_order'] = $data['category_order'];
            $editData['show_on_menu'] = $data['show_on_menu'];
            $editData['show_at_homepage'] = $data['show_at_homepage'] ?? 0;
            $editData['show_at_body_sort'] = $data['show_at_body_sort'] ?? 0;

            if(empty($data['parent_id'])) {
                $editData['show_at_footer'] = $data['show_at_footer'];
                $editData['parent_id'] = 0;
                $editData['parent_definition_id'] = null;
            } else {
                $editData['show_at_footer'] = 0;

                $parentCategory = $this->getCategory($data['parent_id']);

                $editData['parent_id'] = $data['parent_id'];
                if($parentCategory) {
                    $editData['parent_definition_id'] = $parentCategory->definition_id;
                }
            }

            $editData['lang_id'] = inputPost('categories[' . $lang->id . '][lang_id]');
            $editData['name'] = inputPost('categories[' . $lang->id . '][name]');
            $editData['name_slug'] = inputPost('categories[' . $lang->id . '][slug]');

            if (empty($editData["name_slug"])) {
                $editData["name_slug"] = strSlug($editData["name"]);
            } else {
                $editData["name_slug"] = removeSpecialCharacters($editData["name_slug"], true);
                if (!empty($editData['name_slug'])) {
                    $editData['name_slug'] = str_replace(' ', '-', $editData['name_slug']);
                }
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
                                    $editData["file_path"] = $gifPath;
                                    $editData["file_name"] = $newName;
                                } else {
                                    $editData["file_path"] = $uploadModel->uploadCategoryImage($newPath, 1920,400);
                                    $editData["file_name"] = $newName;
                                }
                            }
    
                            $editData["storage"] = $this->generalSettings->storage;
                            $uploadModel->deleteTempFile($newPath);
                            $this->deleteImageFile($category->id);
               
                            if ($editData['storage'] == 'aws_s3') {
                                $awsModel = new AwsModel();
                                if (!empty($editData['file_path'])) {
                                    $awsModel->uploadFile($editData['file_path']);
                                }
                            }
                        }
                    }
                }
            }

            $this->builder->where('id', $category->id)->update($editData);
            $this->updateSlug($category->id);
        }

        if(!empty($data['post_id'])) {
            $this->builderDefinition->where('id', $categoryDefinition->id)->update(['post_definition_id' => $data['post_id']]);
        }

        return true;
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

    public function getCategoryByDefinition($id)
    {
        return $this->builder->where('definition_id', cleanNumber($id))->get()->getResultArray();
    }

    public function getCategoryByDefinitionAndLang($id, $langId)
    {
        if(empty($langId)) {
            $langId = $this->activeLang->id;
        }
        return $this->builder->where('definition_id', cleanNumber($id))
        ->where('lang_id', cleanNumber($langId))    
        ->get()->getRow();
    }

    public function getSubCategoriesByDefinitionAndLang($id, $langId)
    {
        if(empty($langId)) {
            $langId = $this->activeLang->id;
        }

        $category = $this->builder->where('id', cleanNumber($id))->get()->getRow();

        return $this->builder->where('parent_definition_id', cleanNumber($category->definition_id))
        ->where('lang_id', cleanNumber($langId))    
        ->get()->getResult();
    }

    public function getCategoryDefinition($id)
    {
        return $this->builderDefinition->where('id', cleanNumber($id))->get()->getRow();
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

    public function getCategoriesAndPostByLang($langId)
    {
        $langId = cleanNumber($langId); 
        $query = "
            SELECT 
                categories.*, 
                posts.title_slug, 
                posts.id AS post_id, 
                (
                    SELECT name_slug 
                    FROM categories AS tbl_categories 
                    WHERE tbl_categories.definition_id = categories.parent_definition_id 
                    AND tbl_categories.lang_id = ?
                ) AS parent_slug
            FROM categories
            INNER JOIN news.category_definition cd ON cd.id = categories.definition_id
            LEFT JOIN post_definition pd ON pd.id = cd.post_definition_id
            LEFT JOIN posts ON (posts.definition_id = pd.id AND posts.lang_id = ?)
            WHERE categories.lang_id = ?
            ORDER BY categories.category_order
        ";

        return $this->db->query($query, [$langId, $langId, $langId])->getResult();
    }

    public function getShowAtMenuCategory($langId)
    {
        $this->buildQuery();
        return $this->builder
            ->where('categories.lang_id', cleanNumber($langId))
            ->where('categories.show_at_homepage', 1)
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
        if(empty(($langId))) {
            $langId = $this->activeLang->id;
        }

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

    public function getSubCategoriesByDefinitionIdAndLang($definitionId)
    {
        return $this->builder
            ->where('parent_definition_id', cleanNumber($definitionId))
            ->where('lang_id', cleanNumber($this->activeLang->id))
            ->orderBy('name')->get()->getResult();
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
        if(empty(($langId))) {
            $langId = $this->activeLang->id;
        }
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

    public function deleteCategoryDefinition($id)
    {
        $categoryDef = $this->getCategoryDefinition($id);
        if (!empty($categoryDef)) {
            return $this->builderDefinition->where('id', $categoryDef->id)->delete();
        }
        return false;
    }
}
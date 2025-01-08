<?php namespace App\Models;

use CodeIgniter\Model;

class PostAdminModel extends BaseModel
{
    protected $builder;
    protected $builderPostImages;
    protected $builderPostFiles;
    protected $builderPostAudios;

    protected $builderDefinition;

    protected $builderCategoryDefinition;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('posts');
        $this->builderPostImages = $this->db->table('post_images');
        $this->builderPostFiles = $this->db->table('post_files');
        $this->builderPostAudios = $this->db->table('post_audios');
        $this->builderDefinition = $this->db->table('post_definition');
        $this->builderCategoryDefinition = $this->db->table('category_definition');
    }

    //input values
    public function inputValues()
    {
        return [
            'lang_id' => inputPost('lang_id'),
            'title' => inputPost('title'),
            'title_slug' => inputPost('title_slug'),
            'summary' => inputPost('summary'),
            'category_id' => inputPost('category_id'),
            'content' => inputPost('content'),
            'optional_url' => inputPost('optional_url'),
            'need_auth' => inputPost('need_auth'),
            'is_slider' => inputPost('is_slider'),
            'is_featured' => inputPost('is_featured'),
            'is_recommended' => inputPost('is_recommended'),
            'is_breaking' => inputPost('is_breaking'),
            'visibility' => inputPost('visibility'),
            'show_right_column' => inputPost('show_right_column'),
            'keywords' => inputPost('keywords'),
            'image_description' => inputPost('image_description')
        ];
    }

    public function getPostsByTitle($searchQuery)
    {
        return $this->builder
            ->select('posts.id, posts.title')
            ->like('posts.title', $searchQuery)
            ->orderBy('posts.created_at', 'DESC')
            ->get()
            ->getResult();
    }

    public function getTopPosts($limit = 10)
    {
        return $this->builder
            ->select('posts.id, posts.title')
            ->orderBy('posts.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function getPostsByTitle2($searchQuery, $langId)
    {
        return $this->builder
            ->select('posts.id, posts.title, posts.definition_id')
            ->like('posts.title', $searchQuery)
            ->where('posts.lang_id', cleanNumber($langId))
            ->orderBy('posts.created_at', 'DESC')
            ->get()
            ->getResult();
    }

    public function getTopPosts2($limit = 10, $langId)
    {
        return $this->builder
            ->select('posts.id, posts.title, posts.definition_id')
            ->where('posts.lang_id', cleanNumber($langId))
            ->orderBy('posts.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function addPostDefinition($postDefinitionData) {
        $this->builderDefinition->insert($postDefinitionData);
        return $this->db->insertID();
    }

    public function addPost2($postData) {
        $data = [];
        
        if (empty($postData['date_published'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        } else {
            $data['created_at'] = $postData['date_published'];
        }

        $data['title'] = $postData['title'];
        $data['definition_id'] = $postData['post_definition_id'];
        $data['is_breaking'] = $postData['is_breaking'];
        $data['lang_id'] = $postData['lang_id'];
        $data['post_type'] = 'article';
        $data['content'] = $postData['content'];
        if(!empty($postData['subcategory_id']) && $postData['subcategory_id'] != "0") {
            $data['category_id'] = $postData['subcategory_id'];
        } else {
            $data['category_id'] = $postData['category_id'];
        }

        $categoryModel = new CategoryModel();
        $category = $categoryModel->getCategory($data['category_id']);
        if(!empty($category)) {
            $this->builderDefinition->where('id', $data['definition_id'])
                        ->update(['category_definition_id' => $category->definition_id]);
        }
        
        $data['user_id'] = user()->id;
        $data['visibility'] = $postData['visibility'];

        if(empty($postData['slug'])) {
            $data['title_slug'] = $postData['title'];
        } else {
            $data['title_slug'] = $postData['slug'];
        }

        if (empty($postData['slug'])) {
            $data['title_slug'] = strSlug($data['title']);
        } else {
            $data['title_slug'] = removeSpecialCharacters($postData['slug'], true);
            if (!empty($data['title_slug'])) {
                $data['title_slug'] = str_replace(' ', '-', $data['title_slug']);
            }
        }
        
        $data['show_post_url'] = 0;
        $dataToUpdate['status'] = $postData['status'] ?? 1;
        $data['show_right_column'] = 0;
        $data['is_featured'] = 0;
        $data['is_scheduled'] = 0;

        $postImageId = $postData['post_image_id'];
        if (!empty($postImageId)) {
            $fileModel = new FileModel();
            $image = $fileModel->getImage($postImageId);
            if (!empty($image)) {
                $data['image_big'] = $image->image_big;
                $data['image_default'] = $image->image_default;
                $data['image_slider'] = $image->image_slider;
                $data['image_mid'] = $image->image_mid;
                $data['image_small'] = $image->image_small;
                $data['image_mime'] = $image->image_mime;
                if ($image->storage == 'aws_s3') {
                    $data['image_storage'] = 'aws_s3';
                }
            }
        }
        if (!empty($postData['image_url'])) {
            $data['image_url'] = $postData['image_url'];
        }

        if ($this->builder->insert($data)) {
            $postId = $this->db->insertID();
            if(!empty($postId)) {
                $this->updateSlug($postId);

                $fileIds = $postData['selected_files'];
                if (!empty($fileIds)) {
                    foreach ($fileIds as $fileId) {
                        $fileModel = new FileModel();
                        $file = $fileModel->getFile($fileId);
                        if (!empty($file)) {
                            $item = [
                                'post_id' => $postId,
                                'file_id' => $file->id
                            ];
                            $this->builderPostFiles->insert($item);
                        }
                    }
                }

                $tagModel = new TagModel();
                $tagModel->addPostTags($postId, $postData['tags']);
            }
        }
    }

    //add post
    public function addPost($postType)
    {
        $data = $this->setPostData($postType);
        $isScheduled = inputPost('scheduled_post');
        $datePublished = inputPost('date_published');
        $data['is_scheduled'] = 0;
        if ($isScheduled) {
            $data['is_scheduled'] = 1;
        }
        if (!empty($datePublished)) {
            $data['created_at'] = $datePublished;
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        $data['show_post_url'] = 0;
        $data['post_type'] = $postType;
        $data['user_id'] = user()->id;
        $data['status'] = inputPost('status');
        if (empty($data['status'])) {
            $data['status'] = 1;
        }
        if (empty($data['show_right_column'])) {
            $data['show_right_column'] = 0;
        }

        // $data['is_breaking'] = 0;
        $data['is_featured'] = 0;
        //add post image
        $data['image_big'] = '';
        $data['image_default'] = '';
        $data['image_slider'] = '';
        $data['image_mid'] = '';
        $data['image_small'] = '';
        $data['image_mime'] = 'jpg';
        $data['image_url'] = '';
        $data['image_storage'] = 'local';
        $postImageId = inputPost('post_image_id');
        if (!empty($postImageId)) {
            $fileModel = new FileModel();
            $image = $fileModel->getImage($postImageId);
            if (!empty($image)) {
                $data['image_big'] = $image->image_big;
                $data['image_default'] = $image->image_default;
                $data['image_slider'] = $image->image_slider;
                $data['image_mid'] = $image->image_mid;
                $data['image_small'] = $image->image_small;
                $data['image_mime'] = $image->image_mime;
                if ($image->storage == 'aws_s3') {
                    $data['image_storage'] = 'aws_s3';
                }
            }
        }
        if (!empty(inputPost('image_url'))) {
            $data['image_url'] = inputPost('image_url');
        }
        if (empty($data['visibility'])) {
            $data['visibility'] = 0;
        }
        if ($this->builder->insert($data)) {
            return $this->db->insertID();
        }
        return false;
    }

    public function editPost2($data) {
        foreach ($data['posts'] as $postData) {
            $post = $this->getPost($postData['post_id']);

            $dataToUpdate = [];
            $dataToUpdate['created_at'] = !empty($postData['date_published']) 
                ? $postData['date_published'] 
                : date('Y-m-d H:i:s');
            $dataToUpdate['title'] = $postData['title'] ?? '';
            $dataToUpdate['is_breaking'] = $postData['is_breaking'];
            $dataToUpdate['lang_id'] = $postData['lang_id'] ?? 1; 
            $dataToUpdate['post_type'] = 'article';
            $dataToUpdate['content'] = $postData['content'] ?? '';
            $dataToUpdate['definition_id'] = $post->definition_id ?? 0;

            $dataToUpdate['category_id'] = (!empty($postData['subcategory_id']) && $postData['subcategory_id'] != "0")
            ? $postData['subcategory_id']
            : $postData['category_id'];

            $categoryModel = new CategoryModel();
            $category = $categoryModel->getCategory($dataToUpdate['category_id']);
            if(!empty($category)) {
                $this->builderDefinition->where('id', $dataToUpdate['definition_id'])
                        ->update(['category_definition_id' => $category->definition_id]);
            }

            $dataToUpdate['user_id'] = user()->id;
            $dataToUpdate['visibility'] = $postData['visibility'] ?? 1;

            $dataToUpdate['title_slug'] = empty($postData['slug'])
            ? strSlug($postData['title'])
            : str_replace(' ', '-', removeSpecialCharacters($postData['slug'], true));
            // error_log($dataToUpdate);
            $dataToUpdate['show_post_url'] = 0;
            $dataToUpdate['status'] = $postData['status'] ?? 1;
            $dataToUpdate['show_right_column'] = 0;
            $dataToUpdate['is_featured'] = 0;
            $dataToUpdate['is_scheduled'] = 0;

            $postImageId = $postData['post_image_id'];
            if (!empty($postImageId)) {
                $fileModel = new FileModel();
                $image = $fileModel->getImage($postImageId);
                if (!empty($image)) {
                    $dataToUpdate['image_big'] = $image->image_big;
                    $dataToUpdate['image_default'] = $image->image_default;
                    $dataToUpdate['image_slider'] = $image->image_slider;
                    $dataToUpdate['image_mid'] = $image->image_mid;
                    $dataToUpdate['image_small'] = $image->image_small;
                    $dataToUpdate['image_mime'] = $image->image_mime;
                    $dataToUpdate['image_url'] = '';
                    if ($image->storage == 'aws_s3') {
                        $dataToUpdate['image_storage'] = 'aws_s3';
                    }
                }
            }

            if (!empty($postData['image_url'])) {
                $dataToUpdate['image_url'] = $postData['image_url'];
                $dataToUpdate['image_big'] = '';
                $dataToUpdate['image_default'] = '';
                $dataToUpdate['image_slider'] = '';
                $dataToUpdate['image_mid'] = '';
                $dataToUpdate['image_small'] = '';
                $dataToUpdate['image_mime'] = 'jpg';
                $dataToUpdate['image_storage'] = '';
            }

            $this->builder->set($dataToUpdate)->where('id', $post->id)->update();

            $this->updateSlug($postData['post_id']);

            $fileIds = $postData['selected_files'];
            if (!empty($fileIds)) {
                foreach ($fileIds as $fileId) {
                    $fileModel = new FileModel();
                    $file = $fileModel->getFile($fileId);
                    if (!empty($file)) {
                        $item = [
                            'post_id' => $postData['post_id'],
                            'file_id' => $file->id
                        ];
                        $this->builderPostFiles->insert($item);
                    }
                }
            }

            $tagModel = new TagModel();
            $tagModel->addPostTags($post->id, $postData['tags']);
            // $this->builder->where('id', $postData['post_id'])->update($data);
        }
    }

    //update post
    public function editPost($id, $postType)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $data = $this->setPostData($postType);
            $data['created_at'] = inputPost('date_published');
            $data['user_id'] = inputPost('user_id');
            $data['is_scheduled'] = inputPost('scheduled_post');
            if (empty($data['is_scheduled'])) {
                $data['is_scheduled'] = 0;
            }
            $publish = inputPost('publish');
            if (!empty($publish) && $publish == 1) {
                $data['status'] = 1;
            }
            //update post image
            $postImageId = inputPost('post_image_id');
            if (!empty($postImageId)) {
                $fileModel = new FileModel();
                $image = $fileModel->getImage($postImageId);
                if (!empty($image)) {
                    $data['image_big'] = $image->image_big;
                    $data['image_default'] = $image->image_default;
                    $data['image_slider'] = $image->image_slider;
                    $data['image_mid'] = $image->image_mid;
                    $data['image_small'] = $image->image_small;
                    $data['image_mime'] = $image->image_mime;
                    $data['image_url'] = '';
                    $data['image_storage'] = 'local';
                    if ($image->storage == 'aws_s3') {
                        $data['image_storage'] = 'aws_s3';
                    }
                }
            }
            if (!empty(inputPost('image_url'))) {
                $data['image_url'] = inputPost('image_url');
                $data['image_big'] = '';
                $data['image_default'] = '';
                $data['image_slider'] = '';
                $data['image_mid'] = '';
                $data['image_small'] = '';
                $data['image_mime'] = 'jpg';
                $data['image_storage'] = '';
            }
            if (empty($data['visibility'])) {
                $data['visibility'] = 0;
            }
            $data['updated_at'] = date('Y-m-d H:i:s');
            return $this->builder->where('id', $post->id)->update($data);
        }
        return false;
    }

    //set post data
    public function setPostData($postType)
    {
        $data = $this->inputValues();
        if (!isset($data['is_featured'])) {
            $data['is_featured'] = 0;
        }
        if (!isset($data['is_breaking'])) {
            $data['is_breaking'] = 0;
        }
        if (!isset($data['is_slider'])) {
            $data['is_slider'] = 0;
        }
        if (!isset($data['is_recommended'])) {
            $data['is_recommended'] = 0;
        }
        if (!isset($data['need_auth'])) {
            $data['need_auth'] = 0;
        }
        $subCategoryId = inputPost('subcategory_id');
        if (!empty($subCategoryId)) {
            $data['category_id'] = $subCategoryId;
        }
        $data['show_item_numbers'] = 0;
        if (!empty(inputPost('show_item_numbers'))) {
            $data['show_item_numbers'] = 1;
        }
        if (empty($data['title_slug'])) {
            $data['title_slug'] = strSlug($data['title']);
        } else {
            $data['title_slug'] = removeSpecialCharacters($data['title_slug'], true);
            if (!empty($data['title_slug'])) {
                $data['title_slug'] = str_replace(' ', '-', $data['title_slug']);
            }
        }
        if ($postType == 'video') {
            $data['video_url'] = inputPost('video_url');
            $data['video_embed_code'] = inputPost('video_embed_code');
            $data['video_path'] = inputPost('video_path');
            $data['video_storage'] = inputPost('video_storage');
        }
        $votePermission = inputPost('vote_permission');
        if ($votePermission == 'registered') {
            $data['is_poll_public'] = 0;
        } else {
            $data['is_poll_public'] = 1;
        }
        return $data;
    }

    //update slug
    public function updateSlug($id)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            if (empty($post->title_slug) || $post->title_slug == '-') {
                $this->builder->where('id', $post->id)->update(['title_slug' => $post->id]);
            } else {
                if ($this->generalSettings->post_url_structure == 'id') {
                    $this->builder->where('id', $post->id)->update(['title_slug' => $post->id]);
                } else {
                    $row = $this->builder->where('title_slug', cleanStr($post->title_slug))->where('id != ', $post->id)->get()->getRow();
                    if (!empty($row)) {
                        $this->builder->where('id', $post->id)->update(['title_slug' => $post->title_slug . '-' . $post->id]);
                    }
                }
            }
        }
    }

    //get post
    public function getPost($id)
    {
        return $this->builder
        ->where('id', cleanNumber($id))->get()->getRow();
    }

    public function getPostsByDefinition($id)
    {
        return $this->builder
        ->select('posts.*, pd.id AS pdId, pd.category_definition_id AS category_definition_id')
        ->join('post_definition pd', 'posts.definition_id = pd.id')
        ->where('definition_id', cleanNumber($id))->get()->getResultArray();
    }

    public function getPostDefinition($id)
    {
        return $this->builderDefinition->where('id', cleanNumber($id))->get()->getRow();
    }

    //get posts count
    public function getPostsCount($list = null)
    {
        $this->filterPosts($list);
        return $this->builder->where('is_scheduled', 0)->where('status', 1)->where('visibility', 1)->countAllResults();
    }

    //get paginated posts
    public function getPostsPaginated($list, $perPage, $offset)
    {
        $this->filterPosts($list);
        return $this->builder->where('posts.visibility', 1)->where('posts.status', 1)->where('posts.is_scheduled', 0)->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }


    public function getTotalPostView()
    {
        return $this->builder->selectSum('pageviews')->get()->getRow();
    }

    //get pending posts count
    public function getPendingPostsCount()
    {
        $this->filterPosts();
        return $this->builder->where('is_scheduled', 0)->where('status', 1)->where('visibility', 0)->countAllResults();
    }

    //get paginated pending posts
    public function getPendingPostsPaginated($perPage, $offset)
    {
        $this->filterPosts();
        return $this->builder->where('posts.visibility', 0)->where('posts.status', 1)->where('posts.is_scheduled', 0)->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get scheduled posts count
    public function getScheduledPostsCount()
    {
        $this->filterPosts();
        return $this->builder->where('is_scheduled', 1)->where('status', 1)->countAllResults();
    }

    //get paginated scheduled posts
    public function getScheduledPostsPaginated($perPage, $offset)
    {
        $this->filterPosts();
        return $this->builder->where('is_scheduled', 1)->where('status', 1)->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get drafts count
    public function getDraftsCount()
    {
        $this->filterPosts();
        return $this->builder->where('status', 0)->countAllResults();
    }

    //get paginated scheduled posts
    public function getDraftsPaginated($perPage, $offset)
    {
        $this->filterPosts();
        return $this->builder->where('status', 0)->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter by values
    public function filterPosts($list = null)
    {
        $langId = cleanNumber(inputGet('lang_id'));
        if(empty(($langId))) {
            $langId = $this->activeLang->id;
        }

        $postType = cleanStr(inputGet('post_type'));
        $user = cleanNumber(inputGet('user'));
        $categoryId = cleanNumber(inputGet('category'));
        $subCategoryId = cleanNumber(inputGet('subcategory'));
        $q = inputGet('q');
        if (!empty($subCategoryId)) {
            $categoryId = $subCategoryId;
        }
        $userId = null;
        if (checkUserPermission('manage_all_posts')) {
            if (!empty($user)) {
                $userId = $user;
            }
        } else {
            $userId = user()->id;
        }
        if (!empty($userId)) {
            $this->builder->where('posts.user_id', cleanNumber($userId));
        }
        if (!empty($langId)) {
            $this->builder->where('posts.lang_id', cleanNumber($langId));
        }
        if (!empty($postType)) {
            $this->builder->where('posts.post_type', cleanStr($postType));
        }
        
        $this->builder->select('posts.*');

        if (!empty($categoryId)) {
            $categoryModel = new CategoryModel();
            $categories = $categoryModel->getCategories();
            $category = $categoryModel->getCategory($categoryId);

            $categoryIds = getCategoryTree2($category->id, $category->definition_id, $categories);
            if (!empty($categoryIds) && countItems($categoryIds) > 0) {
                $this->builder
                ->join('post_definition pd', 'posts.definition_id = pd.id')
                ->whereIn('pd.category_definition_id', $categoryIds, false);
            }
        }
        if (!empty($q)) {
            $this->builder->like('posts.title', cleanStr($q));
        }
        if (!empty($list)) {
            if ($list == 'slider_posts') {
                $this->builder->where('posts.is_slider', 1);
            }
            if ($list == 'featured_posts') {
                $this->builder->where('posts.is_featured', 1);
            }
            if ($list == 'breaking_news') {
                $this->builder->where('posts.is_breaking', 1);
            }
            if ($list == 'recommended_posts') {
                $this->builder->where('posts.is_recommended', 1);
            }
        }
    }

    //add or remove post from slider
    public function addRemoveSlider($post)
    {
        if (!empty($post)) {
            if ($post->is_slider == 1) {
                return $this->builder->where('id', $post->id)->update(['is_slider' => 0]);
            } else {
                return $this->builder->where('id', $post->id)->update(['is_slider' => 1]);
            }
        }
        return false;
    }

    //add or remove post from featured
    public function addRemoveFeatured($post)
    {
        if (!empty($post)) {
            if ($post->is_featured == 1) {
                return $this->builder->where('id', $post->id)->update(['is_featured' => 0]);
            } else {
                return $this->builder->where('id', $post->id)->update(['is_featured' => 1]);
            }
        }
        return false;
    }

    public function addRemoveBreaking2($post)
    {
        $posts = $this->getPostsByDefinition($post->definition_id);

        if (!empty($posts)) {
            foreach ($posts as $post) { 
                if ($post['is_breaking'] == 1) {
                    $this->builder->where('id', $post['id'])->update(['is_breaking' => 0]);
                } else {
                    $this->builder->where('id', $post['id'])->update(['is_breaking' => 1]);
                }
            }
            return true;
        }
        return false;
    }

    //add or remove post from breaking
    public function addRemoveBreaking($post)
    {
        if (!empty($post)) {
            if ($post->is_breaking == 1) {
                return $this->builder->where('id', $post->id)->update(['is_breaking' => 0]);
            } else {
                return $this->builder->where('id', $post->id)->update(['is_breaking' => 1]);
            }
        }
        return false;
    }

    //add or remove post from recommended
    public function addRemoveRecommended($post)
    {
        if (!empty($post)) {
            if ($post->is_recommended == 1) {
                return $this->builder->where('id', $post->id)->update(['is_recommended' => 0]);
            } else {
                return $this->builder->where('id', $post->id)->update(['is_recommended' => 1]);
            }
        }
        return false;
    }

    //approve post
    public function approvePost($post)
    {
        if (!empty($post)) {
            return $this->builder->where('id', $post->id)->update(['visibility' => 1]);
        }
        return false;
    }

    //publish post
    public function publishPost($post)
    {
        if (!empty($post)) {
            return $this->builder->where('id', $post->id)->update(['is_scheduled' => 0, 'created_at' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    //publish draft
    public function publishDraft($post)
    {
        if (!empty($post)) {
            return $this->builder->where('id', $post->id)->update(['status' => 1, 'created_at' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    public function publishDraft2($post)
    {
        if (!empty($post)) {
            return $this->builder->where('definition_id', $post->definition_id)->update(['status' => 1, 'created_at' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    //check scheduled posts
    public function checkScheduledPosts()
    {
        $posts = $this->builder->where('is_scheduled', 1)->get()->getResult();
        $date = date('Y-m-d H:i:s');
        $isUpdated = false;
        if (!empty($posts)) {
            foreach ($posts as $post) {
                if ($post->created_at <= $date) {
                    $this->builder->where('id', $post->id)->update(['is_scheduled' => 0]);
                    $isUpdated = true;
                }
            }
        }
        if ($isUpdated) {
            resetCacheDataOnChange();
        }
        echo "All scheduled posts have been checked.";
    }

    //save home slider post order
    public function setHomeSliderPostOrder($id, $order)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $order = cleanNumber($order);
            if ($order > 999999) {
                $order = 999999;
            }
            $this->builder->where('id', $post->id)->update(['slider_order' => $order]);
        }
    }

    //save feaured post order
    public function setFeauredPostOrder($id, $order)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $order = cleanNumber($order);
            if ($order > 999999) {
                $order = 999999;
            }
            $this->builder->where('id', $post->id)->update(['featured_order' => $order]);
        }
    }

    //post bulk options
    public function postBulkOptions($operation, $postIds)
    {
        $data = array();
        if ($operation == 'add_slider') {
            $data['is_slider'] = 1;
        } elseif ($operation == 'remove_slider') {
            $data['is_slider'] = 0;
        } elseif ($operation == 'add_featured') {
            $data['is_featured'] = 1;
        } elseif ($operation == 'remove_featured') {
            $data['is_featured'] = 0;
        } elseif ($operation == 'add_breaking') {
            $data['is_breaking'] = 1;
        } elseif ($operation == 'remove_breaking') {
            $data['is_breaking'] = 0;
        } elseif ($operation == 'add_recommended') {
            $data['is_recommended'] = 1;
        } elseif ($operation == 'remove_recommended') {
            $data['is_recommended'] = 0;
        } elseif ($operation == 'publish_scheduled') {
            $data['is_scheduled'] = 0;
            $data['created_at'] = date('Y-m-d H:i:s');
        } elseif ($operation == 'approve') {
            $data['visibility'] = 1;
        } elseif ($operation == 'publish_draft') {
            $data['status'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!empty($postIds)) {
            foreach ($postIds as $id) {
                $post = $this->getPost($id);
                if (!empty($post)) {
                    $this->builder->where('id', $id)->update($data);
                }
            }
        }
    }

    //delete post
    public function deletePost($id)
    {
        $postDefinition = $this->getPostDefinition($id);
        if(!empty($postDefinition)) {
            $posts = $this->getPostsByDefinition($postDefinition->id);
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    if (!checkPostOwnership($post['user_id'])) {
                        return false;
                    }
                    //delete additional images
                    $this->deleteAdditionalImages($post['id']);
                    //delete audios
                    $this->deletePostAudios($post['id']);
                    //delete list items
                    $postItemModel = new PostItemModel();
                    $postItemModel->deletePostListItems($post['id'], 'gallery');
                    $postItemModel->deletePostListItems($post['id'], 'sorted_list');
                    //delete quiz questions
                    $quizModel = new QuizModel();
                    $quizModel->deleteQuizQuestions($post['id']);
                    $quizModel->deleteQuizResults($post['id']);
                    //delete post tags
                    $tagModel = new TagModel();
                    $tagModel->deletePostTags($post['id']);
                    //delete comments
                    $this->db->table('comments')->where('post_id', $post['id'])->delete();
                    //delete post
                    $this->builder->where('id', $post['id'])->delete();
                }
                return $this->builderDefinition->where('id', cleanNumber($postDefinition->id))->delete();
            }
        }
        
        return false;
    }

    //delete multi post
    public function deleteMultiPosts($postIds)
    {
        if (!empty($postIds)) {
            foreach ($postIds as $id) {
                $this->deletePost($id);
            }
        }
    }

    //delete old posts
    public function deleteOldPosts()
    {
        if ($this->generalSettings->auto_post_deletion == 1) {
            $days = $this->generalSettings->auto_post_deletion_days;
            if ($this->generalSettings->auto_post_deletion_delete_all != 1) {
                $this->builder->where("feed_id != ''");
            }
            $posts = $this->builder->where('created_at < DATE_SUB(NOW(), INTERVAL ' . cleanNumber($days) . ' DAY)')->get()->getResult();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $this->deletePost($post->id);
                }
            }
        }
    }

    /*
    *------------------------------------------------------------------------------------------
    * POST FILES
    *------------------------------------------------------------------------------------------
    */

    //delete post main image
    public function deletePostMainImage($postId)
    {
        $post = $this->getPost($postId);
        if (!empty($post)) {
            if (!checkPostOwnership($post->user_id)) {
                return false;
            }
            $data = [
                'image_big' => '',
                'image_default' => '',
                'image_slider' => '',
                'image_mid' => '',
                'image_small' => '',
                'image_url' => ''
            ];
            $this->builder->where('id', $post->id)->update($data);
        }
    }

    //add post additional images
    public function addPostAdditionalImages($postId)
    {
        $imageIds = inputPost('additional_post_image_id');
        if (!empty($imageIds)) {
            foreach ($imageIds as $imageId) {
                $fileModel = new FileModel();
                $image = $fileModel->getImage($imageId);
                if (!empty($image)) {
                    $item = [
                        'post_id' => $postId,
                        'image_big' => $image->image_big,
                        'image_default' => $image->image_default,
                        'storage' => $image->storage
                    ];
                    if (!empty($item['image_default'])) {
                        $this->builderPostImages->insert($item);
                    }
                }
            }
        }
    }

    //delete additional image
    public function deletePostAdditionalImage($fileId)
    {
        $image = $this->getAdditionalImage($fileId);
        if (!empty($image)) {
            $this->builderPostImages->where('id', $image->id)->delete();
        }
    }

    //get additional images
    public function getAdditionalImages($postId)
    {
        return $this->builderPostImages->where('post_id', cleanNumber($postId))->get()->getResult();
    }

    public function getSelectedPostCategory($categoryId)
    {
        return $this->builder
        ->select('categories.id as category_id, categories.name as category_name, posts.id as post_id, posts.title as post_title')
        ->join('categories', 'categories.post_id = posts.id') 
        ->where('categories.id', cleanNumber($categoryId)) 
        ->get()
        ->getRow();
    }

    public function getSelectedPostCategoryByDefinition($definitionId)
    {
        return $this->builderCategoryDefinition
        ->select('p.id as post_id, p.title as post_title')
        ->join('post_definition pd', 'category_definition.post_definition_id = pd.id') 
        ->join('posts p', 'pd.id = p.definition_id') 
        ->where('p.lang_id', cleanNumber($this->activeLang->id)) 
        ->where('category_definition.id', cleanNumber($definitionId)) 
        ->get()
        ->getRow();
    }

    //get additional image
    public function getAdditionalImage($id)
    {
        return $this->builderPostImages->where('id', cleanStr($id))->get()->getRow();
    }

    //delete additional images
    public function deleteAdditionalImages($postId)
    {
        $images = $this->getAdditionalImages($postId);
        if (!empty($images)) {
            foreach ($images as $image) {
                $this->builderPostImages->where('id', $image->id)->delete();
            }
        }
    }

    //get post audio
    public function getPostAudio($audioId)
    {
        return $this->builderPostAudios->where('id', cleanNumber($audioId))->get()->getRow();
    }

    //add post audios
    public function addPostAudios($postId)
    {
        $audioIds = inputPost('post_audio_id');
        if (!empty($audioIds)) {
            foreach ($audioIds as $audioId) {
                $fileModel = new FileModel();
                $audio = $fileModel->getAudio($audioId);
                if (!empty($audio)) {
                    $item = [
                        'post_id' => $postId,
                        'audio_id' => $audio->id,
                    ];
                    $this->builderPostAudios->insert($item);
                }
            }
        }
    }

    //get post audios
    public function getPostAudios($postId)
    {
        return $this->db->table('audios')->join('post_audios', 'audios.id = post_audios.audio_id')->select('audios.*, post_audios.id AS post_audio_id')
            ->where('post_audios.post_id', cleanNumber($postId))->orderBy('post_audios.id')->get()->getResult();
    }

    //delete post audio
    public function deletePostAudio($id)
    {
        $row = $this->getPostAudio($id);
        if (!empty($row)) {
            $post = $this->getPost($row->post_id);
            if (!empty($post)) {
                if (!checkPostOwnership($post->user_id)) {
                    return false;
                }
                $this->builderPostAudios->where('id', cleanNumber($id))->delete();
            }
        }
    }

    //delete post audios
    public function deletePostAudios($postId)
    {
        $audios = $this->builderPostAudios->where('post_id', cleanNumber($postId))->get()->getResult();
        if (!empty($audios)) {
            foreach ($audios as $audio) {
                $this->builderPostAudios->where('id', $audio->id)->delete();
            }
        }
    }

    //delete post video
    public function deletePostVideo($postId)
    {
        $post = $this->getPost($postId);
        if (!empty($post)) {
            if (!checkPostOwnership($post->user_id)) {
                return false;
            }
            $this->builder->where('id', $post->id)->update(['video_path' => '']);
        }
    }

    //add post files
    public function addPostFiles($postId)
    {
        $fileIds = inputPost('post_selected_file_id');
        if (!empty($fileIds)) {
            foreach ($fileIds as $fileId) {
                $fileModel = new FileModel();
                $file = $fileModel->getFile($fileId);
                if (!empty($file)) {
                    $item = [
                        'post_id' => $postId,
                        'file_id' => $file->id
                    ];
                    $this->builderPostFiles->insert($item);
                }
            }
        }
    }

    //get post file
    public function getPostFile($id)
    {
        return $this->builderPostFiles->where('id', cleanStr($id))->get()->getRow();
    }

    //get post files
    public function getPostFiles($postId)
    {
        return $this->builderPostFiles->join('files', 'files.id = post_files.file_id')->select('files.*, post_files.id AS post_file_id')
            ->where('post_files.post_id', cleanNumber($postId))->orderBy('post_files.id')->get()->getResult();
    }

    //delete post file
    public function deletePostFile($id)
    {
        $file = $this->getPostFile($id);
        if (!empty($file)) {
            $post = $this->getPost($file->post_id);
            if (!checkPostOwnership($post->user_id)) {
                return false;
            }
            if (!empty($post)) {
                $this->builderPostFiles->where('id', $file->id)->delete();
            }
        }
    }

    //generate CSV object
    public function generateCSVObject($filePath)
    {
        $array = array();
        $fields = array();
        $txtName = uniqid() . '.txt';
        $i = 0;
        $handle = fopen($filePath, 'r');
        if ($handle) {
            while (($row = fgetcsv($handle)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }
                foreach ($row as $k => $value) {
                    $array[$i][$fields[$k]] = $value;
                }
                $i++;
            }
            if (!feof($handle)) {
                return false;
            }
            fclose($handle);
            if (!empty($array)) {
                $txtFile = fopen(FCPATH . 'uploads/tmp/' . $txtName, 'w');
                fwrite($txtFile, serialize($array));
                fclose($txtFile);
                $obj = new \stdClass();
                $obj->numberOfItems = countItems($array);
                $obj->txtFileName = $txtName;
                @unlink($filePath);
                return $obj;
            }
        }
        return false;
    }

    //import csv item
    public function importCSVItem($txtFileName, $index)
    {
        $filePath = FCPATH . 'uploads/tmp/' . $txtFileName;
        $file = fopen($filePath, 'r');
        $content = fread($file, filesize($filePath));
        $array = @unserialize($content);
        if (!empty($array)) {
            $uploadModel = new UploadModel();
            $i = 1;
            foreach ($array as $item) {
                if ($i == $index) {
                    $data = array();
                    $data['lang_id'] = getCSVInputValue($item, 'lang_id', 'int');
                    $data['title'] = getCSVInputValue($item, 'title');
                    $data['title_slug'] = getCSVInputValue($item, 'title_slug') ? getCSVInputValue($item, 'title_slug') : strSlug($data['title']);
                    $data['title_hash'] = '';
                    $data['keywords'] = getCSVInputValue($item, 'keywords');
                    $data['summary'] = getCSVInputValue($item, 'summary');
                    $data['content'] = getCSVInputValue($item, 'content');
                    $data['category_id'] = getCSVInputValue($item, 'category_id', 'int');
                    $data['image_big'] = '';
                    $data['image_default'] = '';
                    $data['image_slider'] = '';
                    $data['image_mid'] = '';
                    $data['image_small'] = '';
                    $data['image_mime'] = 'jpg';
                    $data['optional_url'] = '';
                    $data['pageviews'] = 0;
                    $data['need_auth'] = 0;
                    $data['is_slider'] = 0;
                    $data['slider_order'] = 0;
                    $data['is_featured'] = 0;
                    $data['featured_order'] = 0;
                    $data['is_recommended'] = 0;
                    $data['is_breaking'] = 0;
                    $data['is_scheduled'] = 0;
                    $data['visibility'] = 0;
                    $data['post_type'] = getCSVInputValue($item, 'post_type') ? getCSVInputValue($item, 'post_type') : 'article';
                    $data['video_path'] = '';
                    $data['image_url'] = '';
                    $data['video_url'] = '';
                    $data['video_embed_code'] = getCSVInputValue($item, 'video_embed_code');
                    $data['user_id'] = user()->id;
                    $data['status'] = getCSVInputValue($item, 'status', 'int');
                    $data['feed_id'] = 0;
                    $data['post_url'] = '';
                    $data['show_post_url'] = 0;
                    $data['image_description'] = getCSVInputValue($item, 'image_description');
                    $data['show_item_numbers'] = 0;
                    $data['created_at'] = date('Y-m-d H:i:s');
                    //download image
                    $imgURL = getCSVInputValue($item, 'image_url');
                    if (!empty($imgURL)) {
                        $ext = pathinfo($imgURL, PATHINFO_EXTENSION);
                        if ($ext == 'gif') {
                            try {
                                $tempPath = $uploadModel->downloadTempImage($imgURL, 'gif');
                                if (!empty($tempPath) && file_exists($tempPath)) {
                                    $gifPath = $uploadModel->uploadGIF('temp.gif', 'images');
                                    $dataImage = [
                                        'image_big' => $gifPath,
                                        'image_default' => $gifPath,
                                        'image_slider' => $gifPath,
                                        'image_mid' => $gifPath,
                                        'image_small' => $gifPath,
                                        'image_mime' => 'gif',
                                        'file_name' => $data['title_slug'],
                                        'user_id' => user()->id
                                    ];
                                }
                            } catch (\Exception $e) {
                            }
                        } else {
                            try {
                                $tempPath = $uploadModel->downloadTempImage($imgURL, 'jpg');
                                if (!empty($tempPath) && file_exists($tempPath)) {
                                    $dataImage = [
                                        'image_big' => $uploadModel->uploadPostImage($tempPath, 'big'),
                                        'image_default' => $uploadModel->uploadPostImage($tempPath, 'default'),
                                        'image_slider' => $uploadModel->uploadPostImage($tempPath, 'slider'),
                                        'image_mid' => $uploadModel->uploadPostImage($tempPath, 'mid'),
                                        'image_small' => $uploadModel->uploadPostImage($tempPath, 'small'),
                                        'image_mime' => 'jpg',
                                        'file_name' => $data['title_slug'],
                                        'user_id' => user()->id
                                    ];
                                }
                            } catch (\Exception $e) {
                            }
                        }
                        //add image to database
                        if (!empty($dataImage)) {
                            if ($this->db->table('images')->insert($dataImage)) {
                                $data['image_big'] = $dataImage['image_big'];
                                $data['image_default'] = $dataImage['image_default'];
                                $data['image_slider'] = $dataImage['image_slider'];
                                $data['image_mid'] = $dataImage['image_mid'];
                                $data['image_small'] = $dataImage['image_small'];
                                $data['image_mime'] = $dataImage['image_mime'];
                            }
                        }
                    }
                    //check visibility
                    if (checkUserPermission('manage_all_posts') || $this->generalSettings->approve_updated_user_posts != 1) {
                        $data['visibility'] = 1;
                    }
                    if ($this->builder->insert($data)) {
                        $lastId = $this->db->insertID();
                        //update slug
                        $this->updateSlug($lastId);
                        //add tags
                        $tags = getCSVInputValue($item, 'tags');
                        if (!empty($tags)) {
                            $tagModel = new TagModel();
                            $tagModel->addPostTags($lastId, $tags);
                        }
                    }
                    return $data['title'];
                }
                $i++;
            }
        }
    }
}

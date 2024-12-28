<?php namespace App\Models;

use CodeIgniter\Model;

class BannerModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('banner');
    }

    public function inputValues()
    {
        $data = [
            'lang_id' => inputPost('lang_id'),
            'sort' => inputPost('sort'),
            'visibility' => inputPost('visibility'),
        ];
        return $data;
    }

    public function getImagesPaginated($perPage, $offset)
    {
        $this->filterImages();
        return $this->builder->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    public function filterImages()
    {
        $q = inputGet('q');

        $q = inputGet('q');
        if (!empty($q)) {
            $this->builder->like('file_path', cleanStr($q));
        }

        $langId = inputGet('lang_id');
        if (!empty($langId)) {
            $this->builder->where('lang_id', cleanNumber($langId));
        }
    }

    public function getImagesCount()
    {
        $this->filterImages();
        return $this->builder->countAllResults();
    }

    public function getImage($id)
    {
        return $this->builder->where('id', cleanNumber($id))->get()->getRow();
    }

    public function deleteImage($id)
    {
        $image = $this->getImage($id);
        if (!empty($image)) {
            $this->deleteImageFile($id);
            return $this->builder->where('id', $image->id)->delete();
        }
        return false;
    }

    public function getImageByLang($lang)
    {
        return $this->builder->where('lang_id', cleanNumber($lang))->orderBy('created_at desc')->get()->getResult();
    }

    public function addImage()
    {
        $data = $this->inputValues();
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
                                $gifPath = $uploadModel->uploadGIF($newName, 'banner');
                                $data["file_path"] = $gifPath;
                                $data["file_name"] = $newName;
                            } else {
                                $data["file_path"] = $uploadModel->uploadBannerImage($newPath, 1920, 1080);
                                $data["file_name"] = $newName;
                            }
                        }

                        $data["storage"] = $this->generalSettings->storage;
                        $data["created_at"] = date('Y-m-d H:i:s');
                        $db = \Config\Database::connect(null, false);

                        $db->table('banner')->insert($data);
                        $db->close();
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
            return true;
        }
        return false;
    }

    public function editImage()
    {
        $id = inputPost('id');
        $image = $this->getImage($id);
        if (!empty($image)) {
            $data = $this->inputValues();
            $uploadModel = new UploadModel();
            $tempData = $uploadModel->uploadTempFile('file', true);
            if (!empty($tempData)) {
                $tempPath = $tempData['path'];
                
                if ($tempData['ext'] == 'gif') {
                    $gifPath = $uploadModel->uploadGIF($tempData['name'], 'banner');
                    $data["file_path"] = $gifPath;
                    $data["file_name"] = $tempData['name'];
                } else {
                    $data["file_path"] = $uploadModel->uploadBannerImage($tempPath, 1920, 1080);
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
            return $this->builder->where('id', cleanNumber($id))->update($data);
        }
        return false;
    }

    public function deleteImageFile($id)
    {
        $image = $this->getImage($id);
        if (!empty($image)) {
            if ($image->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $awsModel->deleteFile($image->file_path);
            } else {
                @unlink(FCPATH . $image->file_path);
            }
        }
    }
}
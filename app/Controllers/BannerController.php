<?php

namespace App\Controllers;

use App\Models\BannerModel;

class BannerController extends BaseAdminController
{
    protected $bannerModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->bannerModel = new BannerModel();
    }

    public function index()
    {
        $data['title'] = trans("images");
        $numRows = $this->bannerModel->getImagesCount();
        $pager = paginate($this->perPage, $numRows);
        $data['images'] = $this->bannerModel->getImagesPaginated($this->perPage, $pager->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/banner/banners', $data);
        echo view('admin/includes/_footer');
    }

    public function addBanner()
    {
        $data['title'] = trans("add_image");

        echo view('admin/includes/_header', $data);
        echo view('admin/banner/add_banner', $data);
        echo view('admin/includes/_footer');
    }

    public function addBannerPost()
    {
        if ($this->bannerModel->addImage()) {
            $this->session->setFlashdata('success', trans('msg_added'));
        } else {
            $this->session->setFlashdata('error', trans("msg_error"));
            return redirect()->to(adminUrl('banners/save'))->withInput();
        }
        return redirect()->to(adminUrl('banners/save'));
    }

    public function editBanner($id)
    {
        $data['title'] = trans("update_image");
        $data['image'] = $this->bannerModel->getImage($id);
        if (empty($data['image'])) {
            return redirect()->to(adminUrl('banners'));
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/banner/edit_banner', $data);
        echo view('admin/includes/_footer');
    }

    public function editBannerPost()
    {
        if ($this->bannerModel->editImage()) {
            $this->session->setFlashdata('success', trans('msg_updated'));
        } else {
            $this->session->setFlashdata('error', trans("msg_error"));
            return redirect()->back()->withInput();
        }

        return redirect()->to(adminUrl('banners'));
    }

    public function deleteBannerPost()
    {
        $id = inputPost('id');
        if ($this->bannerModel->deleteImage($id)) {
            $this->session->setFlashdata('success', trans('msg_deleted'));
        } else {
            $this->session->setFlashdata('error', trans('msg_error'));
        }
    }
}
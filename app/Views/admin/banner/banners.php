<div class="row">
    <div class="col-sm-12">
        <?= view('admin/includes/_messages'); ?>
    </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans("images"); ?></h3>
        </div>
        <div class="right">
            <a href="<?= adminUrl('banners/save'); ?>" class="btn btn-success btn-add-new"><i class="fa fa-plus"></i><?= trans("add_image"); ?></a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
            <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <?= view('admin/banner/_filter'); ?>
                        <thead>
                        <tr role="row">
                            <th width="20" class="text-nowrap text-center"><?= trans('id'); ?></th>
                            <th class="text-nowrap text-center"><?= trans('image'); ?></th>
                            <th class="text-nowrap text-center"><?= trans('language'); ?></th>
                            <th class="text-nowrap text-center"><?= trans('date'); ?></th>
                            <th class="text-nowrap text-center"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($images)):
                            foreach ($images as $item):
                                $imgBaseURL = getBaseURLByStorage($item->storage); 
                                ?>
                                <tr>
                                    <td class="text-nowrap text-center"><?= esc($item->id); ?></td>
                                    <td>
                                        <div style="position: relative; height: 105px; overflow: hidden">
                                            <img src="<?= $imgBaseURL . esc($item->file_path); ?>" alt="" class="img-responsive" style="max-width: 140px; max-height: 105px;">
                                        </div>
                                    </td>
                                    <td class="text-start text-nowrap">
                                        <?php $lang = getLanguage($item->lang_id);
                                        if (!empty($lang)) {
                                            echo esc($lang->name);
                                        } ?>
                                    </td>
                                    <td class="text-nowrap text-center"><?= formatDate($item->created_at); ?></td>
                                    <td class="td-select-option">
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_an_option'); ?>&nbsp;&nbsp;<span class="caret"></span></button>
                                            <ul class="dropdown-menu options-dropdown">

                                                <li>
                                                    <a href="<?= adminUrl('banners/edit/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteItem('BannerController/deleteBannerPost','<?= $item->id; ?>','<?= clrQuotes(trans("confirm_image")); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
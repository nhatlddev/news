<div class="row">
    <div class="col-sm-12">
        <?= view('admin/includes/_messages'); ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("add_image"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('banners'); ?>" class="btn btn-success btn-add-new"><i class="fa fa-bars"></i><?= trans("images"); ?></a>
                </div>
            </div>

            <form action="<?= base_url('BannerController/addBannerPost'); ?>" enctype="multipart/form-data" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $activeLang->id == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= trans("sort"); ?></label>
                        <input type="number" class="form-control" name="sort" placeholder="<?= trans('sort'); ?>" value="1" min="1" max="3000" >
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3 col-xs-12">
                                <label><?= trans('visibility'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="visibility" value="1" id="page_enabled" class="square-purple" <?= old("visibility") == 1 || old("visibility") == "" ? 'checked' : ''; ?>>
                                <label for="page_enabled" class="option-label"><?= trans('show'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="visibility" value="0" id="page_disabled" class="square-purple" <?= old("visibility") == 0 && old("visibility") != "" ? 'checked' : ''; ?>>
                                <label for="page_disabled" class="option-label"><?= trans('hide'); ?></label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label"><?= trans('image'); ?></label>
                        <div class="col-sm-12">
                            <div class="row">
                                <a class='btn btn-success btn-sm btn-file-upload'>
                                    <?= trans('select_image'); ?>
                                    <input type="file" id="Multifileupload" name="files[]" size="40" accept=".png, .jpg, .webp, .jpeg, .gif" required>
                                </a>
                                <!-- <span>(<?= trans("select_multiple_images"); ?>)</span> -->
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="row">
                                <div id="MultidvPreview">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_image'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
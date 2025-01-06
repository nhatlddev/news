<div class="row">
    <div class="col-sm-12">
        <?= view('admin/includes/_messages'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('update_image'); ?></h3>
            </div>
            <form action="<?= base_url('BannerController/editBannerPost'); ?>" enctype="multipart/form-data" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <input type="hidden" name="id" value="<?= $image->id; ?>">
                    <input type="hidden" name="file_name" value="<?= esc($image->file_name); ?>">
                    <input type="hidden" name="file_path" value="<?= esc($image->file_path); ?>">

                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $image->lang_id == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= trans("title"); ?></label>
                        <textarea class="tinyMCE form-control" name="title"><?= esc($image->title); ?></textarea>
                        <!-- <input type="text" class="form-control" name="title" placeholder="<?= trans("title"); ?>" value="<?= esc($image->title); ?>" maxlength="200"> -->
                    </div>

                    <div class="form-group">
                        <label><?= trans("content"); ?></label>
                        <textarea class="tinyMCE form-control" name="content"><?= $image->content; ?></textarea>
                        <!-- <textarea class="form-control" name="content" placeholder="<?= trans('content'); ?>"><?= $image->content; ?></textarea> -->
                    </div>

                    <div class="form-group">
                        <label><?= trans("sort"); ?></label>
                        <input type="number" class="form-control" name="sort" placeholder="<?= trans('sort'); ?>" value="1" min="1" max="3000" value="<?= esc($image->sort); ?>">
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3 col-xs-12">
                                <label><?= trans('visibility'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                            <input type="radio" name="visibility" value="1" id="page_enabled" class="square-purple" <?= $image->visibility == 1 ? 'checked' : ''; ?>>
                                <label for="page_enabled" class="option-label"><?= trans('show'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                            <input type="radio" name="visibility" value="0" id="page_disabled" class="square-purple" <?= $image->visibility == 0 ? 'checked' : ''; ?>>
                                <label for="page_disabled" class="option-label"><?= trans('hide'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('image'); ?> </label>
                        <div class="row">
                            <div class="col-sm-4">
                                <?php $imgBaseURL = getBaseURLByStorage($image->storage); ?>
                                <img src="<?= $imgBaseURL . esc($image->file_path); ?>" alt="" class="thumbnail img-responsive" style="max-width: 300px; max-height: 300px;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <a class='btn btn-success btn-sm btn-file-upload'>
                                    <?= trans('select_image'); ?>
                                    <input type="file" id="Multifileupload" name="file" size="40" accept=".png, .jpg, .webp, .jpeg, .gif" style="cursor: pointer;">
                                </a>
                            </div>
                        </div>
                        <div id="MultidvPreview"></div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
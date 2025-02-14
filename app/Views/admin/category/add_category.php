<div class="row">
    <div class="col-sm-12">
        <?= view('admin/includes/_messages'); ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("add_category"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('categories'); ?>" class="btn btn-success btn-add-new"><i class="fa fa-bars"></i><?= trans("categories"); ?></a>
                </div>
            </div>
            <form action="<?= base_url('CategoryController/addCategoryPost'); ?>" method="post"  enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control" onchange="getParentCategoriesByLang(this.value);">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $activeLang->id == $language->id ? 'selected' : ''; ?>><?= $language->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= trans('parent_category'); ?></label>
                        <select id="categories" class="form-control" name="parent_id" onchange="hideParentCategoryInputs(this.value);">
                            <option value=""><?= trans('none'); ?></option>
                            <?php if (!empty($parentCategories)):
                                foreach ($parentCategories as $item): ?>
                                    <option value="<?= $item->id; ?>"><?= $item->name; ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= trans("category_name"); ?></label>
                        <input type="text" class="form-control" name="name" placeholder="<?= trans("category_name"); ?>" value="<?= old('name'); ?>" maxlength="200" required>
                    </div>

                    <div class="form-group d-none">
                        <label class="control-label"><?= trans("slug"); ?>
                            <small>(<?= trans("slug_exp"); ?>)</small>
                        </label>
                        <input type="text" class="form-control" name="name_slug" placeholder="<?= trans("slug"); ?>" value="<?= old('name_slug'); ?>">
                    </div>

                    <div class="form-group d-none">
                        <label class="control-label"><?= trans('description'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="description" placeholder="<?= trans('description'); ?> (<?= trans('meta_tag'); ?>)" value="<?= old('description'); ?>">
                    </div>

                    <div class="form-group d-none">
                        <label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= old('keywords'); ?>">
                    </div>
                    <div class="form-group input-parent d-none">
                        <label><?= trans('color'); ?></label>
                        <div class="input-group my-colorpicker">
                            <input type="text" class="form-control" name="color" maxlength="200" placeholder="<?= trans('color'); ?>">
                            <div class="input-group-addon"><i></i></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?= trans('order_1'); ?></label>
                        <input type="number" class="form-control" name="category_order" placeholder="<?= trans('order'); ?>" value="1" min="1" required>
                    </div>

                    <div class="form-group">
                        <label><?= trans('posts'); ?></label>
                        <select id="post_id" class="form-control" name="post_id"  data-placeholder="<?= trans('none'); ?>">
                            <option value=""><?= trans('none'); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-5 col-xs-12">
                                <label><?= trans('show_on_menu'); ?></label>
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                                <input type="radio" id="rb_show_on_menu_1" name="show_on_menu" value="1" class="square-purple" checked>
                                <label for="rb_show_on_menu_1" class="cursor-pointer"><?= trans('yes'); ?></label>
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                                <input type="radio" id="rb_show_on_menu_2" name="show_on_menu" value="0" class="square-purple">
                                <label for="rb_show_on_menu_2" class="cursor-pointer"><?= trans('no'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group input-parent">
                        <div class="row">
                            <div class="col-sm-5 col-xs-12">
                                <label><?= trans('show_at_footer'); ?></label>
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                                <input type="radio" id="rb_show_at_footer_1" name="show_at_footer" value="1" class="square-purple" checked>
                                <label for="rb_show_at_footer_1" class="cursor-pointer"><?= trans('yes'); ?></label>
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                                <input type="radio" id="rb_show_at_footer_2" name="show_at_footer" value="0" class="square-purple">
                                <label for="rb_show_at_footer_2" class="cursor-pointer"><?= trans('no'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group input-parent">
                        <div class="row">
                            <div class="col-sm-5 col-xs-12">
                                <label><?= trans('show_at_homepage'); ?></label>
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                                <input type="radio" id="rb_show_at_homepage_1" name="show_at_homepage" value="1" class="square-purple" checked>
                                <label for="rb_show_at_homepage_1" class="cursor-pointer"><?= trans('yes'); ?></label>
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                                <input type="radio" id="rb_show_at_homepage_2" name="show_at_homepage" value="0" class="square-purple">
                                <label for="rb_show_at_homepage_2" class="cursor-pointer"><?= trans('no'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="show_at_body_sort_group">
                        <label><?= trans("show_at_body_sort"); ?></label>
                        <input id="show_at_body_sort" type="number" class="form-control" name="show_at_body_sort" placeholder="<?= trans('show_at_body_sort'); ?>" value="1" min="0" max="3000">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('image'); ?></label>
                        <div class="col-sm-12">
                            <div class="row">
                                <a class='btn btn-success btn-sm btn-file-upload'>
                                    <?= trans('select_image'); ?>
                                    <input type="file" id="Multifileupload" name="files[]" size="40" accept=".png, .jpg, .webp, .jpeg, .gif">
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="row">
                                <div id="MultidvPreview">
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($activeTheme->theme == 'classic'): ?>
                        <div class="form-group input-parent d-none">
                            <label><?= trans('category_block_style'); ?></label>
                            <div class="row m-b-15 m-t-15">
                                <div class="category-block-box">
                                    <div class="col-sm-12 text-center m-b-15">
                                        <input type="radio" name="block_type" value="block-1" class="square-purple" checked>
                                    </div>
                                    <img src="<?= base_url('assets/admin/img/block-1.png'); ?>" alt="" class="img-responsive cat-block-img">
                                </div>
                                <div class="category-block-box">
                                    <div class="col-sm-12 text-center m-b-15">
                                        <input type="radio" name="block_type" value="block-2" class="square-purple">
                                    </div>
                                    <img src="<?= base_url('assets/admin/img/block-2.png'); ?>" alt="" class="img-responsive cat-block-img">
                                </div>

                                <div class="category-block-box">
                                    <div class="col-sm-12 text-center m-b-15">
                                        <input type="radio" name="block_type" value="block-3" class="square-purple">
                                    </div>
                                    <img src="<?= base_url('assets/admin/img/block-3.png'); ?>" alt="" class="img-responsive cat-block-img">
                                </div>
                                <div class="category-block-box">
                                    <div class="col-sm-12 text-center m-b-15">
                                        <input type="radio" name="block_type" value="block-4" class="square-purple">
                                    </div>
                                    <img src="<?= base_url('assets/admin/img/block-4.png'); ?>" alt="" class="img-responsive cat-block-img">
                                </div>
                                <div class="category-block-box">
                                    <div class="col-sm-12 text-center m-b-15">
                                        <input type="radio" name="block_type" value="block-5" class="square-purple">
                                    </div>
                                    <img src="<?= base_url('assets/admin/img/block-5.png'); ?>" alt="" class="img-responsive cat-block-img">
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row m-b-15 m-t-15 input-parent  d-none">
                            <div class="category-block-box">
                                <div class="col-sm-12 text-center m-b-15">
                                    <input type="radio" name="block_type" value="block-1" class="square-purple" checked>
                                </div>
                                <img src="<?= base_url('assets/admin/img/magazine/block-1.jpg'); ?>" alt="" class="img-responsive cat-block-img">
                            </div>
                            <div class="category-block-box">
                                <div class="col-sm-12 text-center m-b-15">
                                    <input type="radio" name="block_type" value="block-2" class="square-purple">
                                </div>
                                <img src="<?= base_url('assets/admin/img/magazine/block-2.jpg'); ?>" alt="" class="img-responsive cat-block-img">
                            </div>
                            <div class="category-block-box">
                                <div class="col-sm-12 text-center m-b-15">
                                    <input type="radio" name="block_type" value="block-3" class="square-purple">
                                </div>
                                <img src="<?= base_url('assets/admin/img/magazine/block-3.jpg'); ?>" alt="" class="img-responsive cat-block-img">
                            </div>
                            <div class="category-block-box">
                                <div class="col-sm-12 text-center m-b-15">
                                    <input type="radio" name="block_type" value="block-4" class="square-purple">
                                </div>
                                <img src="<?= base_url('assets/admin/img/magazine/block-4.jpg'); ?>" alt="" class="img-responsive cat-block-img">
                            </div>
                            <div class="category-block-box">
                                <div class="col-sm-12 text-center m-b-15">
                                    <input type="radio" name="block_type" value="block-5" class="square-purple">
                                </div>
                                <p style="margin-bottom: 2px; text-align: center; font-weight: 700; font-size: 12px;"><?= trans("slider"); ?></p>
                                <img src="<?= base_url('assets/admin/img/magazine/block-5.jpg'); ?>" alt="" class="img-responsive cat-block-img">
                            </div>
                            <div class="category-block-box">
                                <div class="col-sm-12 text-center m-b-15">
                                    <input type="radio" name="block_type" value="block-6" class="square-purple">
                                </div>
                                <img src="<?= base_url('assets/admin/img/magazine/block-6.jpg'); ?>" alt="" class="img-responsive cat-block-img">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_category'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function hideParentCategoryInputs(val) {
        if (val) {
            $('.input-parent').hide();
        } else {
            $('.input-parent').show();
        }
    }
</script>

<script>
    $(document).ready(function () {
        // Initialize Select2
        $('#post_id').select2({
            placeholder: "<?= trans('search_posts'); ?>",
            allowClear: true,
            ajax: {
                url: "<?= adminUrl('posts/searchDropdown'); ?>",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        query: params.term || '',
                        limit: 10 
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.title
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 0
        });
    });
</script>


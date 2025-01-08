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
            <?= csrf_field(); ?>
            <div class="box-body pt-0" style="padding-top: 0px;">

                <div class="form-group">
                    <label><?= trans('parent_category'); ?></label>
                    <select id="parent_id" class="form-control" name="parent_id" onchange="hideParentCategoryInputs(this.value);">
                        <option value=""><?= trans('none'); ?></option>
                        <?php if (!empty($parentCategories)):
                            foreach ($parentCategories as $item): ?>
                                <option value="<?= $item->id; ?>"><?= $item->name; ?></option>
                            <?php endforeach;
                        endif; ?>
                    </select>
                </div>

                <ul class="nav nav-tabs" role="tablist">
                    <?php foreach ($activeLanguages as $index => $language): ?>
                        <li class="<?= $index === 0 ? 'active text-primary' : ''; ?>">
                            <a href="#form-lang-<?= $language->id; ?>" 
                                role="tab" 
                                data-toggle="tab"
                                class="text-dark">
                                Tên và slug (<?= $language->name; ?>)
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="tab-content">
                    <?php foreach ($activeLanguages as $index => $language): ?>
                    <input type="hidden" name="categories[<?= $language->id; ?>][lang_id]" value="<?= $language->id; ?>">
                    <div class="tab-pane <?= $index === 0 ? 'active' : ''; ?>" 
                        id="form-lang-<?= $language->id; ?>">
                        <div class="form-group mt-3">
                            <label><?= trans("category_name"); ?> (<?= $language->name; ?>)</label>
                            <input type="text" class="form-control" name="categories[<?= $language->id; ?>][name]" placeholder="<?= trans("category_name"); ?>" value="<?= old('name'); ?>" maxlength="200" required>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans("slug"); ?> (<?= $language->name; ?>)
                                <small>(<?= trans("slug_exp"); ?>)</small>
                            </label>
                            <input type="text" class="form-control" name="categories[<?= $language->id; ?>][slug]" placeholder="<?= trans("slug"); ?>" value="<?= old('slug'); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                
                
                <div class="form-group">
                    <label><?= trans('order_1'); ?></label>
                    <input id="category_order" type="number" class="form-control" name="category_order" placeholder="<?= trans('order'); ?>" value="1" min="0" required>
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

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-5 col-xs-12">
                            <label><?= trans('show_at_homepage'); ?></label>
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                            <input type="radio" id="rb_show_at_homepage_1" name="show_at_homepage" value="1" class="square-purple" >
                            <label for="rb_show_at_homepage_1" class="cursor-pointer"><?= trans('yes'); ?></label>
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                            <input type="radio" id="rb_show_at_homepage_2" name="show_at_homepage" value="0" class="square-purple" checked>
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

                <div class="box-footer">
                    <button class="btn btn-primary pull-right" onclick="submitFormsAsJSON()"><?= trans('add_category'); ?></button>
                </div>
            </div>
    </div>
</div>

<!-- <div class="form-group">
                <label><?= trans('order_1'); ?></label>
                <input type="number" class="form-control" name="categories[<?= $language->id; ?>][category_order]" placeholder="<?= trans('order'); ?>" value="1" min="1" required>
            </div>

            <div class="form-group">
                <label><?= trans('posts'); ?></label>
                <select id="categories_<?= $language->id; ?>_post_id" class="form-control" style="width:100%" name="categories_<?= $language->id; ?>_post_id"  data-placeholder="<?= trans('none'); ?>">
                    <option value=""><?= trans('none'); ?></option>
                </select>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5 col-xs-12">
                        <label><?= trans('show_on_menu'); ?></label>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                        <input type="radio" id="rb_show_on_menu_<?= $language->id; ?>_1" name="categories[<?= $language->id; ?>][show_on_menu]" value="1" class="square-purple" checked>
                        <label for="rb_show_on_menu_<?= $language->id; ?>_1" class="cursor-pointer"><?= trans('yes'); ?></label>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                        <input type="radio" id="rb_show_on_menu_<?= $language->id; ?>_2" name="categories[<?= $language->id; ?>][show_on_menu]" value="0" class="square-purple">
                        <label for="rb_show_on_menu_<?= $language->id; ?>_2" class="cursor-pointer"><?= trans('no'); ?></label>
                    </div>
                </div>
            </div>

            <div class="form-group input-parent">
                <div class="row">
                    <div class="col-sm-5 col-xs-12">
                        <label><?= trans('show_at_footer'); ?></label>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                        <input type="radio" id="rb_show_at_footer_<?= $language->id; ?>_1" name="categories[<?= $language->id; ?>][show_at_footer]" value="1" class="square-purple" checked>
                        <label for="rb_show_at_footer_<?= $language->id; ?>_1" class="cursor-pointer"><?= trans('yes'); ?></label>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                        <input type="radio" id="rb_show_at_footer_<?= $language->id; ?>_2" name="categories[<?= $language->id; ?>][show_at_footer]" value="0" class="square-purple">
                        <label for="rb_show_at_footer_<?= $language->id; ?>_2" class="cursor-pointer"><?= trans('no'); ?></label>
                    </div>
                </div>
            </div>

            <div class="form-group input-parent">
                <div class="row">
                    <div class="col-sm-5 col-xs-12">
                        <label><?= trans('show_at_homepage'); ?></label>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                        <input type="radio" id="rb_show_at_homepage_<?= $language->id; ?>_1" name="categories[<?= $language->id; ?>][show_at_homepage]" value="1" class="square-purple" checked>
                        <label for="rb_show_at_homepage_<?= $language->id; ?>_1" class="cursor-pointer"><?= trans('yes'); ?></label>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12 col-option">
                        <input type="radio" id="rb_show_at_homepage_<?= $language->id; ?>_2" name="categories[<?= $language->id; ?>][show_at_homepage]" value="0" class="square-purple">
                        <label for="rb_show_at_homepage_<?= $language->id; ?>_2" class="cursor-pointer"><?= trans('no'); ?></label>
                    </div>
                </div>
            </div>

            <div class="form-group" id="categories[<?= $language->id; ?>][show_at_body_sort_group]">
                <label><?= trans("show_at_body_sort"); ?></label>
                <input id="categories[<?= $language->id; ?>][show_at_body_sort]" type="number" class="form-control" name="categories[<?= $language->id; ?>][show_at_body_sort]" placeholder="<?= trans('show_at_body_sort'); ?>" value="1" min="0" max="3000">
            </div>
        </div> -->

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
    function submitFormsAsJSON() {
        tinymce.triggerSave();

        const languageForms = document.querySelectorAll('.tab-pane');
        const fileInput = document.getElementById('Multifileupload');
        const files = fileInput.files;

        const formData = new FormData();

        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        formData.append('parent_id', document.getElementById('parent_id').value);
        formData.append('post_id', document.getElementById('post_id').value);
        formData.append('category_order', document.getElementById('category_order').value);
        formData.append('show_on_menu', document.querySelector('input[name="show_on_menu"]:checked').value);
        formData.append('show_at_footer', document.querySelector('input[name="show_at_footer"]:checked').value);
        formData.append('show_at_homepage', document.querySelector('input[name="show_at_homepage"]:checked').value);
        formData.append('show_at_body_sort', document.getElementById('show_at_body_sort').value);
        formData.append('name', '');
        formData.append('name_slug', '');
        formData.append('description', '');
        formData.append('keywords', '');
        formData.append('color', '');
        formData.append('block_type', '');

        languageForms.forEach(form => {
            const langId = form.id.split('-')[2];
            formData.append(`categories[${langId}][lang_id]`, langId);
            formData.append(`categories[${langId}][name]`, form.querySelector(`input[name="categories[${langId}][name]"]`).value);
            formData.append(`categories[${langId}][slug]`, form.querySelector(`input[name="categories[${langId}][slug]"]`).value);
        });

        Swal.fire({
            title: 'Đang lưu dữ liệu...',
            text: 'Vui lòng chờ trong giây lát',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            customClass: {
                popup: 'swal-large-popup',
                title: 'swal-large-title',
                htmlContainer: 'swal-large-content',
                confirmButton: 'swal-large-button'
            }
        });

        fetch('<?= base_url("CategoryController/addCategoryPost2"); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash(); ?>'
            },
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Dữ liệu đã được gửi thành công!',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'swal-large-popup',
                        title: 'swal-large-title',
                        htmlContainer: 'swal-large-content',
                        confirmButton: 'swal-large-button'
                    }
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: result.message || 'Đã xảy ra lỗi khi xử lý yêu cầu!',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'swal-large-popup',
                        title: 'swal-large-title',
                        htmlContainer: 'swal-large-content',
                        confirmButton: 'swal-large-button'
                    }
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Đã xảy ra lỗi khi gửi dữ liệu!',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal-large-popup',
                    title: 'swal-large-title',
                    htmlContainer: 'swal-large-content',
                    confirmButton: 'swal-large-button'
                }
            });
        });
    }
</script>

<script>
    $(document).ready(function () {
        const showAtBodySortGroup = document.getElementById("show_at_body_sort_group");
        const showAtBodySort = document.getElementById("show_at_body_sort");
    
        const rbShowAtHomepage1 = document.getElementById("rb_show_at_homepage_1");
        const rbShowAtHomepage2 = document.getElementById("rb_show_at_homepage_2");
    
        if (rbShowAtHomepage1.checked) {
            showAtBodySortGroup.style.display = "block";
            showAtBodySort.setAttribute("required", "true");
        } else if (rbShowAtHomepage2.checked) {
            showAtBodySortGroup.style.display = "none";
            showAtBodySort.removeAttribute("required");
        }

        // Initialize Select2
        $('#post_id').select2({
            placeholder: "<?= trans('search_posts'); ?>",
            allowClear: true,
            ajax: {
                url: "<?= adminUrl('posts/searchDropdown2'); ?>",
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
                                id: item.definition_id,
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
    // $(document).ready(function () {
    //     <?php foreach ($activeLanguages as $lang): ?>
    //         $('#rb_show_at_homepage_<?= $lang->id; ?>_1').on('ifChecked', function () {
    //             const showAtBodySortGroup = document.getElementById("categories[<?= $lang->id; ?>][show_at_body_sort_group]");
    //             showAtBodySortGroup.style.display = "block";
    //             const showAtBodySort = document.getElementById("categories[<?= $lang->id; ?>][show_at_body_sort]");
    //             showAtBodySort.setAttribute("required", "true");

    //             $('input[name*="[show_at_homepage]"][value="1"]').each(function () {
    //                 if ($(this).attr('id') !== 'rb_show_at_homepage_<?= $lang->id; ?>_1') {
    //                     $(this).iCheck('check');
    //                     const group = $(this).closest('.form-group').next();
    //                     group.show();
    //                     group.find('input').attr('required', 'true');
    //                 }
    //             });
    //         });

    //         $('#rb_show_at_homepage_<?= $lang->id; ?>_2').on('ifChecked', function () {
    //             const showAtBodySortGroup = document.getElementById("categories[<?= $lang->id; ?>][show_at_body_sort_group]");
    //             showAtBodySortGroup.style.display = "none";
    //             const showAtBodySort = document.getElementById("categories[<?= $lang->id; ?>][show_at_body_sort]");
    //             showAtBodySort.removeAttribute("required");

    //             $('input[name*="[show_at_homepage]"][value="0"]').each(function () {
    //                 if ($(this).attr('id') !== 'rb_show_at_homepage_<?= $lang->id; ?>_2') {
    //                     $(this).iCheck('check');
    //                     const group = $(this).closest('.form-group').next();
    //                     group.hide();
    //                     group.find('input').removeAttr('required');
    //                 }
    //             });
    //         });

    //         $('#categories_<?= $lang->id; ?>_post_id').select2({
    //             placeholder: "<?= trans('search_posts'); ?>",
    //             allowClear: true,
    //             ajax: {
    //                 url: "<?= adminUrl('posts/searchDropdown2'); ?>",
    //                 dataType: 'json',
    //                 delay: 250,
    //                 data: function (params) {
    //                     return {
    //                         query: params.term || '',
    //                         limit: 10 
    //                     };
    //                 },
    //                 processResults: function (data) {
    //                     return {
    //                         results: $.map(data, function (item) {
    //                             return {
    //                                 id: item.id,
    //                                 text: item.title
    //                             };
    //                         })
    //                     };
    //                 },
    //                 cache: true
    //             },
    //             minimumInputLength: 0
    //         }).on('select2:select', function (e) {
    //             const selectedValue = e.params.data;

    //             $('select[id*="_post_id"]').each(function () {
    //                 if ($(this).attr('id') !== 'categories_<?= $lang->id; ?>_post_id') {
    //                     const optionExists = $(this).find('option[value="' + selectedValue.id + '"]').length > 0;

    //                     if (!optionExists) {
    //                         const newOption = new Option(selectedValue.text, selectedValue.id, true, true);
    //                         $(this).append(newOption).trigger('change');
    //                     } else {
    //                         $(this).val(selectedValue.id).trigger('change');
    //                     }
    //                 }
    //             });
    //         }).on('select2:unselect', function (e) {
    //             const unselectedValue = e.params.data;

    //             $('select[id*="_post_id"]').each(function () {
    //                 if ($(this).attr('id') !== 'categories_<?= $lang->id; ?>_post_id') {
    //                     $(this).find('option[value="' + unselectedValue.id + '"]').remove();
    //                     $(this).trigger('change');
    //                 }
    //             });
    //         });
    //     <?php endforeach; ?>
    // });
    
</script>


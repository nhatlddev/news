<div class="row">
    <div class="col-sm-12">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" role="tablist">
            <?php foreach ($activeLanguages as $index => $language): ?>
                <li class="<?= $index === 0 ? 'active text-primary' : ''; ?>">
                    <a href="#form-lang-<?= $language->id; ?>" 
                       role="tab" 
                       data-toggle="tab"
                       class="text-dark">
                        <?= $language->name; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="row">
            <div class="col-12 col-md-9">
                <div class="tab-content">
                    <?php foreach ($activeLanguages as $index => $language): ?>
                        <div class="tab-pane <?= $index === 0 ? 'active' : ''; ?>" 
                            id="form-lang-<?= $language->id; ?>">

                            <!-- <h4 class="mb-3 fw-bold"><?= trans('add_article') . ' (' . $language->name . ')'; ?></h4> -->

                            <input type="hidden" name="posts[<?= $language->id; ?>][lang_id]" value="<?= $language->id; ?>">

                            <div class="form-post">
                                <?= view('admin/post/multiple_post/add_post_left', ['language' => $language]); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <?= view('admin/post/multiple_post/_upload_image_box2', ['language' => $language]); ?>
                <div class="box">
                    <div class="box-header with-border">
                        <div class="left">
                            <h3 class="box-title"><?= trans('category'); ?></h3>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label"><?= trans('category'); ?></label>
                            <select id="categories" name="posts-category_id" class="form-control category-selector" onchange="updateSubCategories(this.value);">
                                <option value=""><?= trans('select_category'); ?></option>
                                <?php if (!empty($parentCategories)):
                                    foreach ($parentCategories as $item): ?>
                                        <option value="<?= $item->id; ?>"><?= esc($item->name); ?></option>
                                    <?php endforeach;
                                endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('subcategory'); ?></label>
                            <select id="subcategories" name="posts-subcategory_id" class="form-control subcategory-selector">
                                <option value="0"><?= trans('select_category'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header with-border">
                        <div class="left">
                            <h3 class="box-title"><?= trans('publish'); ?></h3>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label><?= trans('date_publish'); ?></label>
                                    <div class='input-group date'>
                                        <input type='text' 
                                            class="form-control flatpickr" 
                                            name="date_published" 
                                            id="input_date_published" 
                                            placeholder="<?= trans("date_publish"); ?>"
                                            onchange="syncDatePublished(this.value);"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <button name="status" 
                                    value="1" 
                                    class="btn btn-primary pull-right" 
                                    onclick="submitFormsAsJSON()">
                                <?= trans('btn_submit'); ?>
                            </button>
                            <button name="status" 
                                    value="0" 
                                    class="btn btn-warning btn-draft pull-right" 
                                    onclick="allowSubmitForm = true;">
                                <?= trans('save_draft'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        
        <!-- Tab Content -->
        
    </div>
</div>

<?= view('admin/file-manager/_load_file_manager', ['loadImages' => true, 'loadFiles' => true, 'loadVideos' => false, 'loadAudios' => false]); ?>

<script>

    $(document).ready(function () {

        $('input[name*="[visibility]"]').on('ifChecked', function () {
            const selectedValue = $(this).val();

            $('input[name*="[visibility]"]').each(function () {
                if ($(this).val() === selectedValue) {
                    $(this).iCheck('check'); 
                }
            });
        });

        $('input[name*="[is_featured]"]').on('ifChecked', function () {
            $('input[name*="[is_featured]"]').iCheck('check');
        });

        $('input[name*="[is_featured]"]').on('ifUnchecked', function () {
            $('input[name*="[is_featured]"]').iCheck('uncheck');
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        flatpickr('.flatpickr', {
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i:S",
            defaultDate: new Date(),
            // onChange: function (selectedDates, dateStr) {
            //     syncDatePublished(dateStr);
            // }
        });
    });

    function syncDatePublished(value) {
        const dateInputs = document.querySelectorAll('input[name="date_published"]');
        dateInputs.forEach(input => {
            input.value = value;
        });
    }

    function syncCategory(value) {
        const categorySelectors = document.querySelectorAll('.category-selector');
        categorySelectors.forEach(selector => {
            selector.value = value;
        });
    }

    function syncSubCategory(value) {
        const subcategorySelectors = document.querySelectorAll('.subcategory-selector');
        subcategorySelectors.forEach(selector => {
            selector.value = value;
        });
    }

    function updateSubCategories(parentCategoryId) {
        if (!parentCategoryId) {
            const subcategorySelectors = document.querySelectorAll('.subcategory-selector');
            subcategorySelectors.forEach(selector => {
                selector.innerHTML = '<option value="0"><?= trans('select_category'); ?></option>';
            });
            return;
        }

        fetch('<?= adminUrl("categories/getSubCategories2"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= csrf_hash(); ?>'
            },
            body: JSON.stringify({ parentCategoryId: parentCategoryId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json(); 
        })
        .then(data => {
            const subcategorySelectors = document.querySelectorAll('.subcategory-selector');
            subcategorySelectors.forEach(selector => {
                selector.innerHTML = '<option value="0"><?= trans('select_category'); ?></option>';
                data.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = subcategory.name;
                    selector.appendChild(option);
                });
            });
        })
        .catch(error => {
            console.error('Lỗi khi lấy subcategories:', error);
        });
    }

    function submitFormsAsJSON() {
        tinymce.triggerSave();

        const languageForms = document.querySelectorAll('.tab-pane');
        const data = {
            post_definition: {},
            posts: []
        };

        languageForms.forEach(form => {
            const langId = form.id.split('-')[2];
            const post = {
                lang_id: langId,
                title: form.querySelector('input[name="posts[' + langId + '][title]"]').value,
                slug: form.querySelector('input[name="posts[' + langId + '][title_slug]"]').value,
                metatag: form.querySelector('textarea[name="posts[' + langId + '][metatag]"]').value,
                content: form.querySelector('textarea[name="posts[' + langId + '][content]"]').value,
                tags: form.querySelector('input[name="posts[' + langId + '][tags]"]').value,
                visibility: form.querySelector('input[name="posts[' + langId + '][visibility]"]:checked').value,
                is_featured: form.querySelector('input[name="posts[' + langId + '][is_featured]"]').checked ? 1 : 0,
                category_id: document.getElementById(`categories`).value,
                subcategory_id: document.getElementById(`subcategories`).value,
                selected_files: [],
                date_published: document.getElementById(`input_date_published`).value,
                post_image_id: document.getElementById(`post_image_id`).value,
                image_url: document.getElementById(`video_thumbnail_url`).value
            };

            const selectedFileInputs = document.querySelectorAll('#post_selected_files' + langId + ' input[name="post_selected_file_id[]"]');
            selectedFileInputs.forEach(input => {
                post.selected_files.push(input.value);
            });

            data.posts.push(post);
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

        fetch('<?= base_url("PostController/addPostPost2"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= csrf_hash(); ?>'
            },
            body: JSON.stringify(data)
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


<?php foreach ($activeLanguages as $lang): ?>
<div id="file_manager_image<?= $lang->id; ?>" class="modal fade modal-file-manager" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= trans('images'); ?></h4>
                <div class="file-manager-search">
                    <input type="text" id="input_search_image<?= $lang->id; ?>" class="form-control" placeholder="<?= trans("search"); ?>">
                </div>
            </div>
            <div class="modal-body">
                <div class="file-manager">
                    <div class="file-manager-left">
                        <div class="dm-uploader-container">
                            <div id="drag-and-drop-zone-image<?= $lang->id; ?>" class="dm-uploader text-center">
                                <p class="file-manager-file-types">
                                    <span>JPG</span>
                                    <span>JPEG</span>
                                    <span>WEBP</span>
                                    <span>PNG</span>
                                    <span>GIF</span>
                                </p>
                                <p class="dm-upload-icon">
                                    <i class="fa fa-cloud-upload"></i>
                                </p>
                                <p class="dm-upload-text"><?= trans("drag_drop_files_here"); ?></p>
                                <p class="text-center">
                                    <button class="btn btn-default btn-browse-files"><?= trans('browse_files'); ?></button>
                                </p>
                                <a class='btn btn-md dm-btn-select-files'>
                                    <input type="file" name="file" size="40" multiple="multiple">
                                </a>
                                <ul class="dm-uploaded-files" id="files-image<?= $lang->id; ?>"></ul>
                                <button type="button" id="btn_reset_upload_image<?= $lang->id; ?>" class="btn btn-reset-upload"><?= trans("reset"); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="file-manager-right">
                        <div class="file-manager-content">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div id="image_file_upload_response<?= $lang->id; ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selected_img_file_id<?= $lang->id; ?>">
                    <input type="hidden" id="selected_img_mid_file_path<?= $lang->id; ?>">
                    <input type="hidden" id="selected_img_default_file_path<?= $lang->id; ?>">
                    <input type="hidden" id="selected_img_slider_file_path<?= $lang->id; ?>">
                    <input type="hidden" id="selected_img_big_file_path<?= $lang->id; ?>">
                    <input type="hidden" id="selected_img_storage<?= $lang->id; ?>">
                    <input type="hidden" id="selected_img_base_url<?= $lang->id; ?>">
                </div>
            </div>
            <div class="modal-footer">
                <div class="file-manager-footer">
                    <button type="button" id="btn_img_delete<?= $lang->id; ?>" class="btn btn-danger pull-left btn-file-delete"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?= trans('delete'); ?></button>
                    <button type="button" id="btn_img_select<?= $lang->id; ?>" class="btn bg-olive btn-file-select"><i class="fa fa-check"></i>&nbsp;&nbsp;<?= trans('select_image'); ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= trans('close'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="files-template-image<?= $lang->id; ?>">
    <li class="media">
        <img class="preview-img" src="<?= base_url('assets/admin/plugins/file-manager/file.png'); ?>" alt="">
        <div class="media-body">
            <div class="progress">
                <div class="dm-progress-waiting"><?= trans("waiting"); ?></div>
                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </li>
</script>
<?php endforeach; ?>

<script>
    <?php foreach ($activeLanguages as $lang): ?>
    $(function () {
        $('#drag-and-drop-zone-image<?= $lang->id; ?>').dmUploader({
            url: '<?= base_url("FileController/uploadImage"); ?>',
            queue: true,
            allowedTypes: 'image/*',
            extFilter: ["jpg", "jpeg", "webp", "png", "gif"],
            extraData: function (id) {
                return {
                    "file_id": id,
                    '<?= csrf_token() ?>': '<?= csrf_hash(); ?>'
                };
            },
            onDragEnter: function () {
                this.addClass('active');
            },
            onDragLeave: function () {
                this.removeClass('active');
            },
            onNewFile: function (id, file) {
                ui_multi_add_file(id, file, "image");
                if (typeof FileReader !== "undefined") {
                    var reader = new FileReader();
                    var img = $('#uploaderFile' + id).find('img');

                    reader.onload = function (e) {
                        img.attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            },
            onBeforeUpload: function (id) {
                $('#uploaderFile' + id + ' .dm-progress-waiting').hide();
                ui_multi_update_file_progress(id, 0, '', true);
                ui_multi_update_file_status(id, 'uploading', 'Uploading...');
                $("#btn_reset_upload_image<?= $lang->id; ?>").show();
            },
            onUploadProgress: function (id, percent) {
                ui_multi_update_file_progress(id, percent);
            },
            onUploadSuccess: function (id, data) {
                document.getElementById("uploaderFile" + id).remove();
                refresh_images<?= $lang->id; ?>();
                ui_multi_update_file_status(id, 'success', 'Upload Complete');
                ui_multi_update_file_progress(id, 100, 'success', false);
                $("#btn_reset_upload_image<?= $lang->id; ?>").hide();
            },
            onUploadError: function (id, xhr, status, message) {
                if (message == "Not Acceptable") {
                    $("#uploaderFile" + id).remove();
                    $(".error-message-img-upload").show();
                    $(".error-message-img-upload p").html("");
                    setTimeout(function () {
                        $(".error-message-img-upload").fadeOut("slow");
                    }, 4000)
                }
            },
            onFileTypeError: function (file) {
                swal({
                    text: "<?= trans("invalid_file_type");?>",
                    icon: "warning",
                    button: VrConfig.textOk
                });
            },
            onFileExtError: function (file) {
                swal({
                    text: "<?= trans("invalid_file_type");?>",
                    icon: "warning",
                    button: VrConfig.textOk
                });
            }
        });
    });
    $(document).on('click', '#btn_reset_upload_image<?= $lang->id; ?>', function () {
        $("#drag-and-drop-zone-image<?= $lang->id; ?>").dmUploader("reset");
        $("#files-image<?= $lang->id; ?>").empty();
        $(this).hide();
    });
<?php endforeach; ?>
</script>

<script src="<?= base_url('assets/admin/js/jquery-ui.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/plugins/file-uploader/js/jquery.dm-uploader.min.js'); ?>"></script>
<script>
<?php foreach ($activeLanguages as $lang): ?>
    var image_type<?= $lang->id; ?> = 'main';
    var data_list_item_id<?= $lang->id; ?> = '';
    var data_is_update<?= $lang->id; ?> = '';
    var data_editor_id<?= $lang->id; ?> = '';

    $('#file_manager_image<?= $lang->id; ?>').on('show.bs.modal', function (e) {
        image_type<?= $lang->id; ?> = $(e.relatedTarget).attr('data-image-type');
        data_is_update<?= $lang->id; ?> = $(e.relatedTarget).attr('data-is-update');
        if (image_type<?= $lang->id; ?> == 'list_item') {
            data_list_item_id<?= $lang->id; ?> = $(e.relatedTarget).attr('data-list-item-id');
        }
        if (image_type<?= $lang->id; ?> == 'list_item_editor') {
            data_list_item_id<?= $lang->id; ?> = $(e.relatedTarget).attr('data-editor-id');
        }
        refresh_images<?= $lang->id; ?>();
    });

    $(document).on('click', '#file_manager_image<?= $lang->id; ?> .file-box', function () {
        $('#file_manager_image<?= $lang->id; ?> .file-box').removeClass('selected');
        $(this).addClass('selected');
        $('#selected_img_file_id<?= $lang->id; ?>').val($(this).attr('data-file-id'));
        $('#selected_img_mid_file_path<?= $lang->id; ?>').val($(this).attr('data-mid-file-path'));
        $('#selected_img_default_file_path<?= $lang->id; ?>').val($(this).attr('data-default-file-path'));
        $('#selected_img_slider_file_path<?= $lang->id; ?>').val($(this).attr('data-slider-file-path'));
        $('#selected_img_big_file_path<?= $lang->id; ?>').val($(this).attr('data-big-file-path'));
        $('#selected_img_storage<?= $lang->id; ?>').val($(this).attr('data-file-storage'));
        $('#selected_img_base_url<?= $lang->id; ?>').val($(this).attr('data-file-base-url'));
        $('#btn_img_delete<?= $lang->id; ?>').show();
        $('#btn_img_select<?= $lang->id; ?>').show();
    });

    function refresh_images<?= $lang->id; ?>() {
        $.ajax({
            type: "POST",
            url: VrConfig.baseURL + "/FileController/getImages",
            data: setAjaxData({}),
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("image_file_upload_response<?= $lang->id; ?>").innerHTML = obj.content;
                }
            }
        });
    }

    $(document).on('click', '#file_manager_image<?= $lang->id; ?> #btn_img_delete<?= $lang->id; ?>', function () {
        var file_id = $('#selected_img_file_id<?= $lang->id; ?>').val();
        $('#img_col_id_' + file_id).remove();
        var data = {
            "file_id": file_id
        };
        $.ajax({
            type: "POST",
            url: VrConfig.baseURL + "/FileController/deleteImage",
            data: setAjaxData(data),
            success: function (response) {
                $('#btn_img_delete<?= $lang->id; ?>').hide();
                $('#btn_img_select<?= $lang->id; ?>').hide();
            }
        });
    });

    $(document).on('click', '#file_manager_image<?= $lang->id; ?> #btn_img_select<?= $lang->id; ?>', function () {
        select_image<?= $lang->id; ?>();
    });

    $(document).on('dblclick', '#file_manager_image<?= $lang->id; ?> .file-box', function () {
        select_image<?= $lang->id; ?>();
    });

    function select_image<?= $lang->id; ?>() {
        var file_id = $('#selected_img_file_id<?= $lang->id; ?>').val();
        var img_mid_file_path = $('#selected_img_mid_file_path<?= $lang->id; ?>').val();
        var img_default_file_path = $('#selected_img_default_file_path<?= $lang->id; ?>').val();
        var img_slider_file_path = $('#selected_img_slider_file_path<?= $lang->id; ?>').val();
        var img_big_file_path = $('#selected_img_big_file_path<?= $lang->id; ?>').val();
        var img_storage = $('#selected_img_storage<?= $lang->id; ?>').val();
        var img_base_url = $('#selected_img_base_url<?= $lang->id; ?>').val();

        if (image_type<?= $lang->id; ?> == 'additional') {
            var image = '<div class="additional-item additional-item-' + file_id + '"><img class="img-additional" src="' + img_base_url + img_mid_file_path + '" alt="">' +
                '<input type="hidden" name="additional_post_image_id[]" value="' + file_id + '">' +
                '<a class="btn btn-danger btn-sm btn-delete-additional-image" data-value="' + file_id + '">' +
                '<i class="fa fa-times"></i> ' +
                '</a>' +
                '</div>';
            $('.additional-image-list').append(image);
        } else if (image_type<?= $lang->id; ?> == 'video_thumbnail') {
            $('input[name=post_image_id]').val(file_id);
            $('#selected_image_file<?= $lang->id; ?>').attr('src', img_mid_file_path);
            if ($("#video_thumbnail_url<?= $lang->id; ?>").length) {
                $('#video_thumbnail_url<?= $lang->id; ?>').val('');
            }
        } else if (image_type<?= $lang->id; ?> == 'editor') {
            var editor = tinymce.get('editor_<?= $lang->id; ?>');
            if (editor) {
                editor.execCommand('mceInsertContent', false, '<p><img src="' + img_base_url + img_default_file_path + '" alt=""/></p>');
            } else {
                console.error('Editor for lang ID <?= $lang->id; ?> not found.');
            }
            
        } else if (image_type<?= $lang->id; ?> == 'list_item_editor') {
            tinymce.get('editor_' + data_editor_id<?= $lang->id; ?>).execCommand('mceInsertContent', false, '<p><img src="' + img_base_url + img_default_file_path + '" alt=""/></p>');
        } else if (image_type<?= $lang->id; ?> == 'list_item') {
            var input = '<input type="hidden" name="list_item_image[]" value="' + img_big_file_path + '">' +
                '<input type="hidden" name="list_item_image_large[]" value="' + img_default_file_path + '">' +
                '<input type="hidden" name="list_item_image_storage[]" value="' + img_storage + '">';
            if (data_is_update<?= $lang->id; ?> == 1) {
                input = '<input type="hidden" name="list_item_image_' + data_list_item_id<?= $lang->id; ?> + '" value="' + img_big_file_path + '">' +
                    '<input type="hidden" name="list_item_image_large_' + data_list_item_id<?= $lang->id; ?> + '" value="' + img_default_file_path + '">' +
                    '<input type="hidden" name="list_item_image_storage_' + data_list_item_id<?= $lang->id; ?> + '" value="' + img_storage + '">';
            }
            var image = '<div class="list-item-image-container">' +
                input +
                '<img src="' + img_base_url + img_big_file_path + '" alt="">' +
                '<a class="btn btn-danger btn-sm btn-delete-selected-file-image btn-delete-selected-list-item-image" data-image-type="list_item" data-list-item-id="' + data_list_item_id<?= $lang->id; ?> + '" data-is-update="' + data_is_update<?= $lang->id; ?> + '">' +
                '<i class="fa fa-times"></i> ' +
                '</a>' +
                '</div>';
            document.getElementById("post_list_item_image_container_" + data_list_item_id<?= $lang->id; ?>).innerHTML = image;
        } else {
            var image = '<div class="post-select-image-container">' +
                '<img src="' + img_base_url + img_mid_file_path + '" alt="">' +
                '<a id="btn_delete_post_main_image<?= $lang->id; ?>" class="btn btn-danger btn-sm btn-delete-selected-file-image">' +
                '<i class="fa fa-times"></i> ' +
                '</a>' +
                '</div>';
            document.getElementById("post_select_image_container<?= $lang->id; ?>").innerHTML = image;
            $('input[name=post_image_id]').val(file_id);
        }

        $('#file_manager_image<?= $lang->id; ?>').modal('toggle');
        $('#file_manager_image<?= $lang->id; ?> .file-box').removeClass('selected');
        $('#btn_img_delete<?= $lang->id; ?>').hide();
        $('#btn_img_select<?= $lang->id; ?>').hide();
    }

    jQuery(function ($) {
    $('#file_manager_image<?= $lang->id; ?> .file-manager-content').on('scroll', function () {
        var search = $("#input_search_image<?= $lang->id; ?>").val().trim();
        if (search.length < 1) {
            if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                var min = 0;
                $('#image_file_upload_response<?= $lang->id; ?> .file-box').each(function () {
                    var value = parseInt($(this).attr('data-file-id'));
                    if (min == 0) {
                        min = value;
                    }
                    if (value < min) {
                        min = value;
                    }
                });
                var data = {
                    'min': min
                };
                $.ajax({
                    type: "POST",
                    url: VrConfig.baseURL + "/FileController/loadMoreImages",
                    data: setAjaxData(data),
                    success: function (response) {
                        setTimeout(function () {
                            var obj = JSON.parse(response);
                            if (obj.result == 1) {
                                $("#image_file_upload_response<?= $lang->id; ?>").append(obj.content);
                            }
                        }, 100);
                    }
                });
            }
        }
    })
});

$(document).on('input', '#input_search_image<?= $lang->id; ?>', function () {
    var search = $(this).val().trim();
    var data = {
        "search": search
    };
    $.ajax({
        type: "POST",
        url: VrConfig.baseURL + "/FileController/searchImage",
        data: setAjaxData(data),
        success: function (response) {
            if (search.length > 1) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    document.getElementById("image_file_upload_response<?= $lang->id; ?>").innerHTML = obj.content;
                }
            } else {
                refresh_images<?= $lang->id; ?>();
            }

        }
    });
});
<?php endforeach; ?>
</script>




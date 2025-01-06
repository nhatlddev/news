<?php foreach ($activeLanguages as $lang): ?>
<div id="file_manager<?= $lang->id; ?>" class="modal fade modal-file-manager" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= trans('files'); ?> (Lang: <?= $lang->id; ?>)</h4>
                <div class="file-manager-search">
                    <input type="text" id="input_search_file<?= $lang->id; ?>" class="form-control" placeholder="<?= trans("search"); ?>">
                </div>
            </div>
            <div class="modal-body">
                <div class="file-manager">
                    <div class="file-manager-left">
                        <div class="dm-uploader-container">
                            <div id="drag-and-drop-zone<?= $lang->id; ?>" class="dm-uploader text-center">
                                <?php if (!empty($generalSettings->allowed_file_extensions)):
                                    $exts = null;
                                    if (!empty($generalSettings->allowed_file_extensions)) {
                                        $exts = explode(',', $generalSettings->allowed_file_extensions);
                                    }
                                    if (!empty($exts) && countItems($exts) > 0): ?>
                                        <p class="file-manager-file-types">
                                            <?php foreach ($exts as $ext):
                                                $ext = strReplace('"', '', $ext);
                                                if (!empty($ext)) {
                                                    $ext = strtoupper($ext);
                                                } ?>
                                                <span><?= esc($ext); ?></span>
                                            <?php endforeach; ?>
                                        </p>
                                    <?php endif;
                                endif; ?>
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
                                <ul class="dm-uploaded-files" id="files-file<?= $lang->id; ?>"></ul>
                                <button type="button" id="btn_reset_upload<?= $lang->id; ?>" class="btn btn-reset-upload"><?= trans("reset"); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="file-manager-right">
                        <div class="file-manager-content">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div id="file_upload_response<?= $lang->id; ?>">
                                        <?php foreach ($files as $file):
                                            if (!empty($file)): ?>
                                                <div class="col-file-manager" id="file_col_id_<?= $file->id; ?>">
                                                    <div class="file-box" data-file-id="<?= $file->id; ?>" data-file-name="<?= esc($file->file_name); ?>">
                                                        <div class="image-container icon-container">
                                                            <div class="file-icon file-icon-lg" data-type="<?= @pathinfo($file->file_path, PATHINFO_EXTENSION); ?>"></div>
                                                        </div>
                                                        <span class="file-name"><?= esc($file->file_name); ?></span>
                                                    </div>
                                                </div>
                                            <?php endif;
                                        endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selected_file_id<?= $lang->id; ?>">
                    <input type="hidden" id="selected_file_name<?= $lang->id; ?>">
                    <input type="hidden" id="selected_file_path<?= $lang->id; ?>">
                </div>
            </div>
            <div class="modal-footer">
                <div class="file-manager-footer">
                    <button type="button" id="btn_file_delete<?= $lang->id; ?>" class="btn btn-danger pull-left btn-file-delete"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?= trans('delete'); ?></button>
                    <button type="button" id="btn_file_select<?= $lang->id; ?>" class="btn bg-olive btn-file-select"><i class="fa fa-check"></i>&nbsp;&nbsp;<?= trans('select_file'); ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= trans('close'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endforeach; ?>

<?php $fileExtensions = '';
$extArray = array();
if (!empty($generalSettings->allowed_file_extensions)) {
    $extArray = @explode(',', $generalSettings->allowed_file_extensions);
}
$newArray = array();
if (!empty($extArray)) {
    foreach ($extArray as $item) {
        $item = strReplace('"', '', $item);
        $item = strReplace("'", '', $item);
        array_push($newArray, $item);
    }
    $fileExtensions = json_encode($newArray);
} ?>

<script src="<?= base_url('assets/admin/plugins/file-uploader/js/jquery.dm-uploader.min.js'); ?>"></script>
<script>
    <?php foreach ($activeLanguages as $lang): ?>
    $(function () {
        $('#drag-and-drop-zone<?= $lang->id; ?>').dmUploader({
            url: '<?= base_url("FileController/uploadFile"); ?>',
            queue: true,
            extFilter: <?= $fileExtensions; ?>,
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
                ui_multi_add_file(id, file, "file");
            },
            onBeforeUpload: function (id) {
                $('#uploaderFile' + id + ' .dm-progress-waiting').hide();
                ui_multi_update_file_progress(id, 0, '', true);
                ui_multi_update_file_status(id, 'uploading', 'Uploading...');
                $("#btn_reset_upload<?= $lang->id; ?>").show();
            },
            onUploadProgress: function (id, percent) {
                ui_multi_update_file_progress(id, percent);
            },
            onUploadSuccess: function (id, data) {
                refresh_files_<?= $lang->id; ?>();
                document.getElementById("uploaderFile" + id).remove();
                ui_multi_update_file_status(id, 'success', 'Upload Complete');
                ui_multi_update_file_progress(id, 100, 'success', false);
                $("#btn_reset_upload<?= $lang->id; ?>").hide();
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

    $(document).on('click', '#btn_reset_upload<?= $lang->id; ?>', function () {
        $("#drag-and-drop-zone<?= $lang->id; ?>").dmUploader("reset");
        $("#files-file<?= $lang->id; ?>").empty();
        $(this).hide();
    });

    function refresh_files_<?= $lang->id; ?>() {
        $.ajax({
            type: "POST",
            url: '<?= base_url("FileController/getFiles"); ?>',
            success: function (response) {
                $("#file_upload_response<?= $lang->id; ?>").html(response);
            }
        });
    }
    <?php endforeach; ?>
</script>

<script src="<?= base_url('assets/admin/js/jquery-ui.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/plugins/file-uploader/js/jquery.dm-uploader.min.js'); ?>"></script>
<script>
    <?php foreach ($activeLanguages as $lang): ?>
        $(document).on('click', '#file_manager<?= $lang->id; ?> .file-box', function () {
        $('#file_manager<?= $lang->id; ?> .file-box').removeClass('selected');
        $(this).addClass('selected');
        var file_id = $(this).attr('data-file-id');
        var file_name = $(this).attr('data-file-name');
        $('#selected_file_id<?= $lang->id; ?>').val(file_id);
        $('#selected_file_name<?= $lang->id; ?>').val(file_name);
        $('#btn_file_delete<?= $lang->id; ?>').show();
        $('#btn_file_select<?= $lang->id; ?>').show();
    });

        $(document).on('click', '#file_manager<?= $lang->id; ?> #btn_file_delete<?= $lang->id; ?>', function () {
        var file_id = $('#selected_file_id<?= $lang->id; ?>').val();
        $('#file_col_id_' + file_id).remove();
        var data = {
            "file_id": file_id
        };
        $.ajax({
            type: "POST",
            url: VrConfig.baseURL + "/FileController/deleteFile",
            data: setAjaxData(data),
            success: function (response) {
                $('#btn_file_delete<?= $lang->id; ?>').hide();
                $('#btn_file_select<?= $lang->id; ?>').hide();
            }
        });
    });

    //select file button
    $(document).on('click', '#file_manager<?= $lang->id; ?> #btn_file_select<?= $lang->id; ?>', function () {
        select_file<?= $lang->id; ?>();
    });

    //select file on double click
    $(document).on('dblclick', '#file_manager<?= $lang->id; ?> .file-box', function () {
        select_file<?= $lang->id; ?>();
    });

    function select_file<?= $lang->id; ?>() {
        var file_id = $('#selected_file_id<?= $lang->id; ?>').val();
        var file_name = $('#selected_file_name<?= $lang->id; ?>').val();

        var file = '<div id="file_' + file_id + '" class="item">\n' +
            '<input type="hidden" name="post_selected_file_id[]" value="' + file_id + '">\n' +
            '<div class="left">\n' +
            '<i class="fa fa-file"></i>\n' +
            '</div>\n' +
            '<div class="center">\n' +
            '<span>' + file_name + '</span>\n' +
            '</div>\n' +
            '<div class="right">\n' +
            '<a href="javascript:void(0)" class="btn btn-sm btn-selected-file-list-item btn-delete-selected-file" data-value="' + file_id + '"><i class="fa fa-times"></i></a></p>\n' +
            '</div>\n' +
            '</div>';
        $('#post_selected_files<?= $lang->id; ?>').append(file);
        $('#file_manager<?= $lang->id; ?>').modal('toggle');
        $('#file_manager<?= $lang->id; ?> .file-box').removeClass('selected');
        $('#btn_file_delete<?= $lang->id; ?>').hide();
        $('#btn_file_select<?= $lang->id; ?>').hide();
    }
    <?php endforeach; ?>
</script>

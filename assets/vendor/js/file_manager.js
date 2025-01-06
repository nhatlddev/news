// var image_type2 = 'main';
// var data_list_item_id2 = '';
// var data_is_update2 = '';
// var data_editor_id2 = '';

// $('#file_manager_image2').on('show.bs.modal', function (e) {
//     image_type2 = $(e.relatedTarget).attr('data-image-type');
//     data_is_update2 = $(e.relatedTarget).attr('data-is-update');
//     if (image_type2 == 'list_item') {
//         data_list_item_id2 = $(e.relatedTarget).attr('data-list-item-id');
//     }
//     if (image_type2 == 'list_item_editor') {
//         data_list_item_id2 = $(e.relatedTarget).attr('data-editor-id');
//     }
//     refresh_images2();
// });

// $(document).on('click', '#file_manager_image2 .file-box', function () {
//     $('#file_manager_image2 .file-box').removeClass('selected');
//     $(this).addClass('selected');
//     $('#selected_img_file_id2').val($(this).attr('data-file-id'));
//     $('#selected_img_mid_file_path2').val($(this).attr('data-mid-file-path'));
//     $('#selected_img_default_file_path2').val($(this).attr('data-default-file-path'));
//     $('#selected_img_slider_file_path2').val($(this).attr('data-slider-file-path'));
//     $('#selected_img_big_file_path2').val($(this).attr('data-big-file-path'));
//     $('#selected_img_storage2').val($(this).attr('data-file-storage'));
//     $('#selected_img_base_url2').val($(this).attr('data-file-base-url'));
//     $('#btn_img_delete2').show();
//     $('#btn_img_select2').show();
// });

// function refresh_images2() {
//     $.ajax({
//         type: "POST",
//         url: VrConfig.baseURL + "/FileController/getImages",
//         data: setAjaxData({}),
//         success: function (response) {
//             var obj = JSON.parse(response);
//             if (obj.result == 1) {
//                 document.getElementById("image_file_upload_response2").innerHTML = obj.content;
//             }
//         }
//     });
// }

// $(document).on('click', '#file_manager_image2 #btn_img_delete2', function () {
//     var file_id = $('#selected_img_file_id2').val();
//     $('#img_col_id_' + file_id).remove();
//     var data = {
//         "file_id": file_id
//     };
//     $.ajax({
//         type: "POST",
//         url: VrConfig.baseURL + "/FileController/deleteImage",
//         data: setAjaxData(data),
//         success: function (response) {
//             $('#btn_img_delete2').hide();
//             $('#btn_img_select2').hide();
//         }
//     });
// });

// $(document).on('click', '#file_manager_image2 #btn_img_select2', function () {
//     select_image2();
// });

// //select image file on double click
// $(document).on('dblclick', '#file_manager_image2 .file-box', function () {
//     select_image2();
// });

// function select_image2() {
//     var file_id = $('#selected_img_file_id2').val();
//     var img_mid_file_path = $('#selected_img_mid_file_path2').val();
//     var img_default_file_path = $('#selected_img_default_file_path2').val();
//     var img_slider_file_path = $('#selected_img_slider_file_path2').val();
//     var img_big_file_path = $('#selected_img_big_file_path2').val();
//     var img_storage = $('#selected_img_storage2').val();
//     var img_base_url = $('#selected_img_base_url2').val();

//     if (image_type2 == 'additional') {
//         var image = '<div class="additional-item additional-item-' + file_id + '"><img class="img-additional" src="' + img_base_url + img_mid_file_path + '" alt="">' +
//             '<input type="hidden" name="additional_post_image_id[]" value="' + file_id + '">' +
//             '<a class="btn btn-danger btn-sm btn-delete-additional-image" data-value="' + file_id + '">' +
//             '<i class="fa fa-times"></i> ' +
//             '</a>' +
//             '</div>';
//         $('.additional-image-list').append(image);
//     } else if (image_type2 == 'video_thumbnail') {
//         $('input[name=post_image_id]').val(file_id);
//         $('#selected_image_file2').attr('src', img_mid_file_path);
//         if ($("#video_thumbnail_url2").length) {
//             $('#video_thumbnail_url2').val('');
//         }
//     } else if (image_type2 == 'editor') {
//         tinymce.activeEditor.execCommand('mceInsertContent', false, '<p><img src="' + img_base_url + img_default_file_path + '" alt=""/></p>');
//     } else if (image_type == 'list_item_editor') {
//         tinymce.get('editor_' + data_editor_id2).execCommand('mceInsertContent', false, '<p><img src="' + img_base_url + img_default_file_path + '" alt=""/></p>');
//     } else if (image_type2 == 'list_item') {
//         var input = '<input type="hidden" name="list_item_image[]" value="' + img_big_file_path + '">' +
//             '<input type="hidden" name="list_item_image_large[]" value="' + img_default_file_path + '">' +
//             '<input type="hidden" name="list_item_image_storage[]" value="' + img_storage + '">';
//         if (data_is_update2 == 1) {
//             input = '<input type="hidden" name="list_item_image_' + data_list_item_id2 + '" value="' + img_big_file_path + '">' +
//                 '<input type="hidden" name="list_item_image_large_' + data_list_item_id2 + '" value="' + img_default_file_path + '">' +
//                 '<input type="hidden" name="list_item_image_storage_' + data_list_item_id2 + '" value="' + img_storage + '">';
//         }
//         var image = '<div class="list-item-image-container">' +
//             input +
//             '<img src="' + img_base_url + img_big_file_path + '" alt="">' +
//             '<a class="btn btn-danger btn-sm btn-delete-selected-file-image btn-delete-selected-list-item-image" data-image-type="list_item" data-list-item-id="' + data_list_item_id + '" data-is-update="' + data_is_update2 + '">' +
//             '<i class="fa fa-times"></i> ' +
//             '</a>' +
//             '</div>';
//         document.getElementById("post_list_item_image_container_" + data_list_item_id2).innerHTML = image;
//     } else {
//         var image = '<div class="post-select-image-container">' +
//             '<img src="' + img_base_url + img_mid_file_path + '" alt="">' +
//             '<a id="btn_delete_post_main_image2" class="btn btn-danger btn-sm btn-delete-selected-file-image">' +
//             '<i class="fa fa-times"></i> ' +
//             '</a>' +
//             '</div>';
//         document.getElementById("post_select_image_container2").innerHTML = image;
//         $('input[name=post_image_id]').val(file_id);
//     }

//     $('#file_manager_image2').modal('toggle');
//     $('#file_manager_image2 .file-box').removeClass('selected');
//     $('#btn_img_delete2').hide();
//     $('#btn_img_select2').hide();
// }
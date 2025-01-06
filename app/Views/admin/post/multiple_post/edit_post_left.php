<div class="form-post-left">
    <div class="box">
        <div class="box-header with-border">
            <div class="left">
                <h3 class="box-title"><?= trans('post_details'); ?></h3>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label class="control-label"><?= trans('title'); ?></label>
                <input type="text" class="form-control" 
                       name="posts[<?= $language->id; ?>][title]" 
                       placeholder="<?= trans('title'); ?>" 
                       value="<?= $post['title'] ?? old('title'); ?>">
            </div>

            <input type="hidden" name="posts[<?= $language->id; ?>][id]" value="<?= $post['id']; ?>">

            <div class="form-group">
                <label class="control-label"><?= trans('slug'); ?><small>(<?= trans('slug_exp'); ?>)</small></label>
                <input type="text" class="form-control" 
                       name="posts[<?= $language->id; ?>][title_slug]" 
                       placeholder="<?= trans('slug'); ?>" 
                       value="<?= $post['title_slug'] ?? old('title_slug'); ?>">
            </div>

            <div class="form-group d-none">
                <label class="control-label"><?= trans('meta_tag'); ?></label>
                <textarea class="form-control" 
                          name="posts[<?= $language->id; ?>][metatag]" 
                          placeholder="<?= trans('meta_tag'); ?>"><?= $post['metatag'] ?? old('metatag'); ?></textarea>
            </div>

            <?php if (checkUserPermission('manage_all_posts')): ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4 col-xs-12">
                            <label><?= trans('visibility'); ?></label>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 col-option">
                            <input type="radio" 
                                   id="rb_visibility_<?= $language->id; ?>_1" 
                                   name="posts[<?= $language->id; ?>][visibility]" 
                                   value="1" 
                                   class="square-purple" 
                                   <?= ($post['visibility'] ?? 1) == 1 ? 'checked' : ''; ?>>
                            <label for="rb_visibility_<?= $language->id; ?>_1" class="cursor-pointer"><?= trans('show'); ?></label>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 col-option">
                            <input type="radio" 
                                   id="rb_visibility_<?= $language->id; ?>_2" 
                                   name="posts[<?= $language->id; ?>][visibility]" 
                                   value="0" 
                                   class="square-purple" 
                                   <?= ($post['visibility'] ?? 1) == 0 ? 'checked' : ''; ?>>
                            <label for="rb_visibility_<?= $language->id; ?>_2" class="cursor-pointer"><?= trans('hide'); ?></label>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (checkUserPermission('manage_all_posts')): ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            <label class="control-label"><?= trans('add_featured'); ?></label>
                        </div>
                        <div class="col-md-8 col-sm-12">
                            <input type="checkbox" 
                                name="posts[<?= $language->id; ?>][is_featured]" 
                                value="1" 
                                class="square-purple" 
                                <?= (isset($post) && $post['is_featured'] == 1) ? 'checked' : ''; ?>>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" 
                    name="posts[<?= $language->id; ?>][is_featured]" 
                    value="<?= isset($post) ? $post['is_featured'] : '0'; ?>">
            <?php endif; ?>

            <div class="row my-4">
                <div class="col-md-4 col-sm-12">
                    <label class="control-label"><?= trans('files'); ?> <small class="small-title"><?= trans("files_exp"); ?></small></label>
                    <a class='btn btn-sm bg-purple' 
                        data-toggle="modal" 
                        data-target="#file_manager<?= $language->id; ?>">
                        <?= trans('select_file'); ?>
                    </a>
                </div>
                <div class="col-md-8 col-sm-12 post-selected-files-container">
                    <div id="post_selected_files<?= $language->id; ?>" class="post-selected-files">
                        <?php
						if (!empty($post)):
							$files = getPostFiles($post['id']);
							if (!empty($files)):
								foreach ($files as $file): ?>
									<div id="post_selected_file_<?= $file->post_file_id; ?>" class="item">
										<div class="left">
											<i class="fa fa-file"></i>
										</div>
										<div class="center">
											<span><?= $file->file_name; ?></span>
										</div>
										<div class="right">
											<a href="javascript:void(0)" class="btn btn-sm btn-selected-file-list-item btn-delete-selected-file-database" data-value="<?= $file->post_file_id; ?>"><i class="fa fa-times"></i></a></p>
										</div>
									</div>
								<?php endforeach;
							endif;
						endif; ?>
                        </div>
                </div>
            </div>

            <label class="control-label control-label-content"><?= trans('content'); ?></label>
            <div id="main_editor" class="mb-3">
                <div class="row">
                    <div class="col-sm-12 editor-buttons">
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#file_manager_image<?= $language->id; ?>" data-image-type="editor">
                            <i class="fa fa-image"></i>&nbsp;&nbsp;&nbsp;<?= trans("add_image"); ?>
                        </button>
                    </div>
                </div>
                <textarea class="tinyMCE form-control" 
                      id="editor_<?= $language->id; ?>" 
                      name="posts[<?= $language->id; ?>][content]"><?= $post['content'] ?? old('content'); ?></textarea>
            </div>

            <div class="form-group">
                <label class="control-label"><?= trans('tags'); ?></label>
                <input id="tags_<?= $language->id; ?>" type="text" name="posts[<?= $language->id; ?>][tags]" class="form-control tags"/>
                <small>(<?= trans('type_tag'); ?>)</small>
            </div>
        </div>
    </div>
</div>
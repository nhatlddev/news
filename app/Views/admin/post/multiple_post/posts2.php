<div class="row">
    <div class="col-sm-12">
        <?= view('admin/includes/_messages'); ?>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= $title; ?></h3>
        </div>
        <div class="right">
            <a href="<?= adminUrl('add-post'); ?>" class="btn btn-success btn-add-new pull-right">
                <i class="fa fa-plus"></i>
                <?= trans('add_post'); ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <?= view('admin/post/multiple_post/_filter_post2'); ?>
                        <thead>
                        <tr role="row">
                            <?php if (checkUserPermission('manage_all_posts')): ?>
                                <th class="text-center text-nowrap"><input type="checkbox" class="checkbox-table" id="checkAll"></th>
                            <?php endif; ?>
                            <th class="text-center text-nowrap"><?= trans('id'); ?></th>
                            <th class="text-center text-nowrap"><?= trans('post'); ?></th>
                            <!-- <th class="text-center text-nowrap"><?= trans('language'); ?></th> -->
                            <th class="text-center text-nowrap"><?= trans('post_type'); ?></th>
                            <th class="text-center text-nowrap"><?= trans('category'); ?></th>
                            <th class="text-center text-nowrap"><?= trans('author'); ?></th>
                            <?php if ($listType == 'slider_posts'): ?>
                                <th class="text-center text-nowrap"><?= trans('slider_order'); ?></th>
                            <?php endif;
                            if ($listType == 'featured_posts'): ?>
                                <th class="text-center text-nowrap"><?= trans('featured_order'); ?></th>
                            <?php endif; ?>
                            <th class="text-center text-nowrap"><?= trans('pageviews'); ?></th>
                            <th class="text-center text-nowrap"><?= trans('date_added'); ?></th>
                            <th class="text-center text-nowrap"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($posts)):
                            foreach ($posts as $item):
                                $language = getLanguage($item->lang_id); ?>
                                <tr>
                                    <?php if (checkUserPermission('manage_all_posts')): ?>
                                        <td class="text-center text-nowrap"><input type="checkbox" name="checkbox-table" class="checkbox-table" value="<?= $item->id; ?>"></td>
                                    <?php endif; ?>
                                    <td class="text-center text-nowrap"><?= esc($item->id); ?></td>
                                    <td class="text-start text-nowrap">
                                        <div class="td-post-item">
                                            <div class="post-title">
                                                <a href="<?= generatePostURL($item, generateBaseURLByLang($language)); ?>" target="_blank">
                                                    <?= esc($item->title); ?>
                                                </a>
                                                <div class="preview">
                                                    <?php if ($item->is_slider): ?>
                                                        <label class="label bg-red label-table"><?= trans('slider'); ?></label>
                                                    <?php endif;
                                                    if ($item->is_featured): ?>
                                                        <label class="label bg-olive label-table"><?= trans('featured'); ?></label>
                                                    <?php endif;
                                                    if ($item->is_recommended): ?>
                                                        <label class="label bg-aqua label-table"><?= trans('recommended'); ?></label>
                                                    <?php endif;
                                                    if ($item->is_breaking): ?>
                                                        <label class="label bg-teal label-table"><?= trans('breaking'); ?></label>
                                                    <?php endif;
                                                    if ($item->need_auth): ?>
                                                        <label class="label label-warning label-table"><?= trans('only_registered'); ?></label>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- <td class="text-center text-nowrap"> <?= !empty($language) ? esc($language->name) : ''; ?></td> -->
                                    <td class="td-post-type text-center text-nowrap"><?= trans($item->post_type); ?></td>
                                    <td class="text-center text-nowrap">
                                        <?php $categories = getParentCategoryTree($item->category_id, $baseCategories);
                                        if (!empty($categories)):
                                            foreach ($categories as $category):
                                                if (!empty($category)): ?>
                                                    <label class="category-label m-r-5 label-table" style="background-color: <?= esc($category->color); ?>!important;">
                                                        <?= esc($category->name); ?>
                                                    </label>
                                                <?php endif;
                                            endforeach;
                                        endif; ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?php $author = getUserById($item->user_id);
                                        if (!empty($author)): ?>
                                            <a href="<?= generateProfileURL($author->slug); ?>" target="_blank" class="table-user-link">
                                                <strong><?= esc($author->username); ?></strong>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($listType == "slider_posts"): ?>
                                        <td style="max-width: 150px;">
                                            <input type="number" class="form-control input-slider-post-order" data-id="<?= $item->id; ?>" value="<?= esc($item->slider_order); ?>">
                                        </td>
                                    <?php endif;
                                    if ($listType == "featured_posts"): ?>
                                        <td style="max-width: 150px;">
                                            <input type="number" class="form-control input-featured-post-order" data-id="<?= $item->id; ?>" value="<?= esc($item->featured_order); ?>">
                                        </td>
                                    <?php endif; ?>
                                    <td class="text-center text-nowrap"><?= $item->pageviews; ?></td>
                                    <td class="text-center text-nowrap"><?= $item->created_at; ?></td>
                                    <td style="width: 180px;" class="text-center">
                                        <form action="<?= base_url('PostController/postOptionsPost'); ?>" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= $item->id; ?>">
                                            <input type="hidden" name="back_url" value="<?= currentFullURL(); ?>">
                                            <div class="dropdown">
                                                <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_an_option'); ?>
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu options-dropdown">
                                                    <li>
                                                        <a href="<?= adminUrl('edit-post2/' . $item->definition_id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                    </li>
                                                    <?php if (checkUserPermission('manage_all_posts')): ?>
                                                        <?php if ($item->is_featured == 1): ?>
                                                            <li>
                                                                <button type="submit" name="option" value="add_remove_featured" class="btn-list-button">
                                                                    <i class="fa fa-times option-icon"></i><?= trans('remove_featured'); ?>
                                                                </button>
                                                            </li>
                                                        <?php else: ?>
                                                            <li>
                                                                <button type="submit" name="option" value="add_remove_featured" class="btn-list-button">
                                                                    <i class="fa fa-plus option-icon"></i><?= trans('add_featured'); ?>
                                                                </button>
                                                            </li>
                                                        <?php endif ?>
                                                        
                                                    <?php endif; ?>
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="deleteItem('PostController/deletePost','<?= $item->definition_id; ?>','<?= clrQuotes(trans("confirm_post")); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($posts)): ?>
                        <p class="text-center"><?= trans("no_records_found"); ?></p>
                    <?php endif; ?>
                    <div class="col-sm-12 table-ft">
                        <div class="row">
                            <div class="pull-right">
                                <?= view('common/_pagination'); ?>
                            </div>
                            <?php if (!empty($posts) && countItems($posts) > 0): ?>
                                <div class="pull-left bulk-options">
                                    <button class="btn btn-sm btn-danger btn-table-delete" onclick="deleteSelectePosts('<?= clrQuotes(trans("confirm_posts")); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></button>
                                    <?php if ($listType != 'slider_posts'): ?>
                                        <button class="btn btn-sm btn-default btn-table-delete" onclick="postBulkOptions('add_slider');"><i class="fa fa-plus option-icon"></i><?= trans('add_slider'); ?></button>
                                    <?php endif;
                                    if ($listType != 'featured_posts'): ?>
                                        <button class="btn btn-sm btn-default btn-table-delete" onclick="postBulkOptions('add_featured');"><i class="fa fa-plus option-icon"></i><?= trans('add_featured'); ?></button>
                                    <?php endif;
                                    if ($listType != 'breaking_news'): ?>
                                        <button class="btn btn-sm btn-default btn-table-delete" onclick="postBulkOptions('add_breaking');"><i class="fa fa-plus option-icon"></i><?= trans('add_breaking'); ?></button>
                                    <?php endif;
                                    if ($listType != 'recommended_posts'): ?>
                                        <button class="btn btn-sm btn-default btn-table-delete" onclick="postBulkOptions('add_recommended');"><i class="fa fa-plus option-icon"></i><?= trans('add_recommended'); ?></button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-default btn-table-delete" onclick="postBulkOptions('remove_slider');"><i class="fa fa-minus option-icon"></i><?= trans('remove_slider'); ?></button>
                                    <button class="btn btn-sm btn-default btn-table-delete" onclick="postBulkOptions('remove_featured');"><i class="fa fa-minus option-icon"></i><?= trans('remove_featured'); ?></button>
                                    <button class="btn btn-sm btn-default btn-table-delete" onclick="postBulkOptions('remove_breaking');"><i class="fa fa-minus option-icon"></i><?= trans('remove_breaking'); ?></button>
                                    <button class="btn btn-sm btn-default btn-table-delete" onclick="postBulkOptions('remove_recommended');"><i class="fa fa-minus option-icon"></i><?= trans('remove_recommended'); ?></button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
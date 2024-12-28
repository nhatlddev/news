<div class="row bg-white mb-3">
    <div class="col-12">
        <div class="container">
            <div class="title-breadcrumb-holder pt-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= langBaseUrl(); ?>"class="text-decoration-none"><?= trans("home"); ?></a>
                        </li>
                        <?php $categories = getParentCategoryTree($post->category_id, $baseCategories);
                        if (!empty($categories)):
                            foreach ($categories as $item):
                                if (!empty($item)):?>
                                    <li class="breadcrumb-item"><a href="<?= generateCategoryURL($item); ?>" class="text-decoration-none"><?= esc($item->name); ?></a></li>
                                <?php endif;
                            endforeach;
                        endif; ?>
                        
                        <li class="breadcrumb-item active" aria-current="page"><?= esc(characterLimiter($post->title, 160, '...')); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- <div class="middle-container"> -->
    <div class="d-flex gap-2 mb-3 flex-column flex-xl-row">
        <!-- <div class="col-lg-9"> -->
            <div class="blog-holder bg-white flex-grow-1">
                <article class="post type-post">
                    <div class="post-content">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="bd-highlight">
                                <a href="<?= generateCategoryURLById($post->category_id, $baseCategories); ?>">
                                    <span class="badge badge-category" style="background-color: <?= esc($post->category_color); ?>"><?= esc($post->category_name); ?></span>
                                </a>
                            </div>
                            <div class="bd-highlight ms-auto">
                                <?php if (authCheck() && user()->id == $post->user_id): ?>
                                    <a href="<?= adminUrl('edit-post/' . $post->id); ?>" class="btn btn-sm btn-warning btn-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                        </svg>
                                        <?= trans("edit"); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="px-4 py-3">
                            <h1 class="text-dark pb-2 text-capitalize font-size-7">
                            <?= esc($post->title); ?>
                            </h1>
                            <div class="d-flex align-items-center post-details-meta mb-4">
                                <?php if ($generalSettings->show_post_author == 1): ?>
                                    <div class="item-meta item-meta-author">
                                        <a class="text-decoration-none text-dark" href="<?= generateProfileURL($postUser->slug); ?>"><img src="<?= getUserAvatar($postUser->avatar); ?>" alt="<?= esc($postUser->username); ?>" width="32" height="32"><span><?= esc($postUser->username); ?></span></a>
                                    </div>
                                <?php endif;
                                if ($generalSettings->show_post_date == 1): ?>
                                    <div class="item-meta item-meta-date">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                        </svg>
                                        <span><?= formatDateFront($post->created_at); ?>&nbsp;-&nbsp;<?= formatHour($post->created_at); ?></span>
                                    </div>
                                    <?php if (!empty($post->updated_at)): ?>
                                        <div class="item-meta item-meta-date">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                            </svg>
                                            <span><?= formatDateFront($post->updated_at); ?>&nbsp;-&nbsp;<?= formatHour($post->updated_at); ?></span>
                                        </div>
                                    <?php endif;
                                endif; ?>
                                <div class="ms-auto item-meta item-meta-comment">
                                    <?php if ($generalSettings->comment_system == 1): ?>
                                        <!-- <span><i class="icon-comment"></i>&nbsp;<?= esc($post->comment_count); ?></span> -->
                                    <?php endif;
                                    if ($generalSettings->show_hits): ?>
                                        <span> <i class="bi bi-eye"></i>&nbsp;<?= esc($post->pageviews); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <section>
                            <?php if ($post->post_type == 'video'):
                                    echo loadView('post/details/_video', ['post' => $post]);
                                elseif ($post->post_type == 'audio'):
                                    echo loadView('post/details/_audio', ['post' => $post]);
                                elseif ($post->post_type == 'gallery'):
                                    echo loadView('post/details/_gallery', ['post' => $post]);
                                elseif ($post->post_type == 'sorted_list'):
                                    echo loadView('post/details/_sorted_list', ['post' => $post]);
                                elseif ($post->post_type == 'trivia_quiz' || $post->post_type == 'personality_quiz'):
                                    echo loadView('post/details/_quiz', ['post' => $post]);
                                elseif ($post->post_type == 'poll'):
                                    echo loadView('post/details/_poll', ['post' => $post]);
                                else:
                                    echo loadView('post/details/_article', ['post' => $post]);
                                endif;
                                echo loadView('partials/_ad_spaces', ['adSpace' => 'post_top', 'class' => '']);
                                if (!empty($post->content)):?>
                                    <div class="post-text mt-4">
                                        <?= loadView('post/_post_content'); ?>
                                    </div>
                                <?php endif;
                                if (!empty($post->optional_url)) : ?>
                                    <div class="d-flex flex-row-reverse mt-4">
                                        <a href="<?= esc($post->optional_url); ?>" class="btn btn-md btn-custom btn-icon" target="_blank" rel="nofollow">
                                            <?= esc($baseSettings->optional_url_button_name); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="m-l-5" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                            </svg>
                                        </a>
                                    </div>
                                <?php endif;
                                if (!empty($feed) && !empty($post->show_post_url)) : ?>
                                    <div class="d-flex flex-row-reverse mt-4">
                                        <a href="<?= $post->post_url; ?>" class="btn btn-md btn-custom" target="_blank" rel="nofollow">
                                            <?= esc($feed->read_more_button_text); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="m-l-5" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                            </svg>
                                        </a>
                                    </div>
                                <?php endif; ?>


                                <?php $files = getPostFiles($post->id);
                                $hasPdf = false;
                                if (!empty($files)): ?>
                                    <div class="post-files">
                                    <?php foreach ($files as $file): ?>
                                            <?php
                                            $fileExtension = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));

                                            if (in_array($fileExtension, ['pdf', 'ppt', 'pptx'])) {
                                                $hasPdf = true; 
                                            }
                                            ?>
                                        <?php endforeach; ?>

                                        <?php if (!$hasPdf): ?>
                                            <h2 class="title"><?= trans("files"); ?></h2>
                                        <?php endif; ?>
                                        <?php foreach ($files as $file): ?>
                                            <?php
                                            $fileExtension = pathinfo($file->file_path, PATHINFO_EXTENSION);
                                            ?>
                                            <div class="file">
                                                <?php if (strtolower($fileExtension) === 'pdf'): ?>
                                                    <div class="pdf-viewer" style="position: relative; width: 100%; height: 90vh; overflow: hidden;">
                                                        <iframe src="<?= base_url($file->file_path); ?>" style="width: 100%; height: 100%; object-fit: contain; border: none;"></iframe>
                                                    </div>
                                                <?php else: ?>
                                                    <form class="mt-3" action="<?= base_url('download-file'); ?>" method="post">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="x" value="<?= $file->file_path; ?>">
                                                        <input type="hidden" name="id" value="<?= $file->id; ?>">
                                                        <button class="btn btn-secondary text-white" type="submit" name="file_type" value="file">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-fill" viewBox="0 0 16 16">
                                                                <path d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm5.5 1.5v2a1 1 0 0 0 1 1h2l-3-3z"/>
                                                            </svg>
                                                            <?= esc($file->file_name); ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                 <!-- <?php $files = getPostFiles($post->id);
                                if (!empty($files)):?>
                                    <div class="post-files">
                                        <h2 class="title"><?= trans("files"); ?></h2>
                                        <?php foreach ($files as $file): ?>
                                            <form action="<?= base_url('download-file'); ?>" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="x" value="<?= $file->file_path; ?>">
                                                <input type="hidden" name="id" value="<?= $file->id; ?>">
                                                <div class="file">
                                                    <button class="btn btn-secondary text-white" type="submit" name="file_type" value="file">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-fill" viewBox="0 0 16 16">
                                                            <path d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm5.5 1.5v2a1 1 0 0 0 1 1h2l-3-3z"/>
                                                        </svg>
                                                        <?= esc($file->file_name); ?>
                                                    </button>
                                                </div>
                                            </form>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?> -->
                            </section>
                        </div>
                    </div>
                </article>
                <div class="post-next-prev mt-5">
                    <div class="row">
                        <div class="col-sm-6 col-xs-12 left">
                            <?php if (!empty($previousPost)): ?>
                                <div class="head-title text-end">
                                    <a class="text-decoration-none" href="<?= generatePostURL($previousPost); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                                        </svg>
                                        <?= trans("previous_article"); ?>
                                    </a>
                                </div>
                                <h3 class="title text-end">
                                    <a class="text-decoration-none text-dark hover-primary" href="<?= generatePostURL($previousPost); ?>"><?= esc(characterLimiter($previousPost->title, 80, '...')); ?></a>
                                </h3>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6 col-xs-12 right">
                            <?php if (!empty($nextPost)): ?>
                                <div class="head-title text-start">
                                    <a class="text-decoration-none" href="<?= generatePostURL($nextPost); ?>">
                                        <?= trans("next_article"); ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                        </svg>
                                    </a>
                                </div>
                                <h3 class="title text-start">
                                    <a class="text-decoration-none text-dark hover-primary" href="<?= generatePostURL($nextPost); ?>"><?= esc(characterLimiter($nextPost->title, 80, '...')); ?></a>
                                </h3>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <!-- </div> -->

        <!-- <div class="col-lg-3"> -->
            <div class="bg-white text-nowrap h-fit-content">
                <div class="badge-title mt-3 mt-md-0">
                    <a  class="text-decoration-none fs-5"><?= trans("post_related"); ?></a>
                </div>
                <div class="d-flex flex-column justify-content-between position-relative">
                    <ul class="list-unstyled">
                        <?php if(!empty(($postRelated))):
                            foreach ($postRelated as $item): ?>
                                <li class="p-2">
                                    <a href="<?= generatePostURL($item); ?>" class="text-dark text-decoration-none hover-primary">
                                        <?= esc(characterLimiter($item->title, 25, '...')); ?>
                                    </a>
                                    <div class="d-flex gap-2">
                                        <span class="text-primary cursor-pointer"><?= esc($item->category_name); ?></span>
                                        <p class="text-muted small mb-0"><?= formatDateFront($item->created_at); ?></p>
                                    </div>
                                </li>
                        <?php endforeach; 
                            endif;
                        ?>
                    </ul>
                </div>
            </div>
        <!-- </div> -->
    <!-- </div> -->
    </div>
    
</div>


<?php if ($generalSettings->facebook_comment_active) {
    echo $generalSettings->facebook_comment;
}
if (!empty($post->feed_id)): ?>
    <style>
        .post-text img {
            display: none !important;
        }

        .post-content .post-summary {
            display: none;
        }
    </style>
<?php endif; ?>
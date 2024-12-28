<div class="container">
    <div class="row my-3">
        <div class="col-lg-3">
            <div class="bg-white">
                <div class="badge-title mt-3 mt-md-0">
                    <a href="" class="text-decoration-none fs-4"><?= trans('most_view'); ?></a>
                </div>
                <div class="d-flex flex-column justify-content-between position-relative">
                    <ul class="list-unstyled">
                    <?php if(!empty(($postMostView))):
                            foreach ($postMostView as $item): ?>
                        <li class="p-2">
                                <a href="<?= generatePostURL($item); ?>" class="text-dark text-decoration-none fw-bold hover-primary">
                                    <?= esc(characterLimiter($item->title, 40, '...')); ?>
                                </a>
                                <div class="d-flex gap-2">
                                    <span class="text-primary cursor-pointer"><?= esc($item->category_name); ?></span>
                                    <p class="text-muted small mb-0"><?= formatDateFront($item->created_at); ?></p>
                                </div>
                            </li>
                    <?php endforeach; 
                        endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="section-title-holder clearfix pattern-light">
                <span class="st-title cursor-pointer" routerLink="/post/list">
                <?= esc($category->name); ?>
                </span>
            </div>
            <div class="row">
            <?php $i = 0;
                 if (!empty($posts)):
                    foreach ($posts as $item): ?>
                        <div class="col-lg-6 mb-3">
                            <div class="position-relative d-inline-block w-100 vertical-align-middle">
                                <div class="position-relative bg-white post-list-content-holder">
                                    <div class="post-list-info-category">
                                        <a class="text-decoration-none cursor-pointer d-inline-block vertical-align-top text-inherit"><?= esc($category->name); ?></a></div>
                                    <h5 class="post-list-title">
                                        <a class="text-truncate text-decoration-none text-dark d-block cursor-pointer hover-primary"
                                            href="<?= generatePostURL($item); ?>" 
                                            >
                                            <?= esc($item->title); ?>
                                        </a>
                                    </h5>
                                    <div class="font-size-8">
                                        <i class="text-primary bi bi-calendar"></i>
                                        <a class="text-decoration-none text-neutral-gray">
                                            <?= formatDateFront($item->created_at); ?>&nbsp;-&nbsp;<?= formatHour($item->created_at); ?>
                                         </a>
                                    </div>
                                </div>
                            </div>
                        </div>
            <?php $i++;
                    endforeach;
                endif; ?>
                <div class="col-12 mt-5 text-center d-flex justify-content-center">
                    <?= view('common/_pagination'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container my-4">
    <div class="row gx-2">
        <div class="col-lg-9">
            <div class="slider-container">
                <div class="site-slider owl-carousel">
                <?php 
                        $i = 0;
                        if(!empty($banners)): 
                            foreach($banners as $item):
                                $imgBaseURL = getBaseURLByStorage($item->storage); 
                        ?>
                    <div>
                        <div class="slider-img">
                            <img src="<?= esc($item->file_path); ?>" alt="Slide 1" />
                        </div>
                        <div class="slider-text">
                            <h3>
                                <?php 
                                    $arrayContent = explode('</p>', $item->title);
                                    if (!empty($arrayContent)) {
                                        foreach ($arrayContent as $p) {
                                            echo $p;
                                        }
                                    }
                                
                                ?>
                            </h3>
                            <p>
                            <?php 
                                $arrayContent = explode('</p>', $item->content);
                                if (!empty($arrayContent)) {
                                    foreach ($arrayContent as $p) {
                                        echo $p;
                                    }
                                }
                                ?>
                            </p>
                        </div>
                    </div>

                    <?php  
                                $i++;   
                                endforeach;
                        endif; 
                    ?> 
                </div>
            </div>
        </div>

        <div class="col-lg-3 bg-white">
            <div class="badge-title mt-3 mt-md-0">
                <a href="" class="text-decoration-none fs-4"><?= trans("post_new"); ?></a>
            </div>
            <?php $i = 0; 
                if (!empty($latestPosts)): ?>
                    <div class="d-flex flex-column justify-content-between position-relative">
                        <ul class="list-unstyled">
                        <?php foreach ($latestPosts as $item): 
                            if ($i < 6): ?>
                            <li class="p-2">
                                <a href="<?= generatePostURL($item); ?>" class="text-dark text-decoration-none fw-bold hover-primary">
                                    <?= esc(characterLimiter($item->title, 40, '...')); ?>
                                </a>
                                <div class="d-flex gap-2">
                                    <span class="text-primary cursor-pointer"><?= esc($item->category_name); ?></span>
                                    <p class="text-muted small mb-0"><?= formatDateFront($item->created_at); ?></p>
                                </div>
                            </li>
                            <?php               
                                endif;                      
                                $i++;
                            endforeach; ?>
                        </ul>
                    </div>
            <div class="text-end">
                <button class="btn btn-primary more-btn border-0">
                    <span><?= trans("load_more"); ?></span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        <?php endif;
        ?>
            
        </div>
    </div>

    <div class="row mt-3">
        <?php if(!empty($categoryModelShowAtHomes)): ?>
            <?php foreach( $categoryModelShowAtHomes as $item): 
                $imgBaseURL = getBaseURLByStorage($item->storage);
                ?>
                <div class="col-md-6 mb-4">
                    <div class="section-title-holder clearfix pattern-light mb-1">
                        <span class="st-title cursor-pointer text-center" style="width: 120px">
                        <?= esc($item->name); ?>
                        </span>
                    </div>

                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card rounded-0 position-relative">
                                <img src="<?= esc($item->file_path); ?>" class="card-img-top rounded-0" alt="Post Image">
                                <div class="card-img-overlay d-flex flex-column justify-content-end p-3">
                                    <div class="custom-overlay p-3 px-0">
                                        <?php
                                            $postItems = getPostByCategoryDefinition($item->definition_id);
                                            $sumView = getPageViewsSumByDefinitionId($item->definition_id);

                                            if (!empty($postItems)):
                                                $i = 0;
                                                foreach ($postItems as $postItem):
                                                    if ($i <= 2):
                                        ?>
                                            <div class="post-item border-bottom">
                                                <h5 class="card-title text-white mb-0">
                                                    <a class="text-decoration-none text-light hover-primary" 
                                                    href="<?= generatePostURL($postItem); ?>" <?php postURLNewTab($postItem); ?>>
                                                        <?= esc(characterLimiter($postItem->title, 80, '...')); ?>
                                                    </a>
                                                </h5>
                                                <p class="text-white mb-1 small">
                                                    <i class="bi bi-calendar text-light"></i> <?= formatDateFront($postItem->created_at); ?>
                                                </p>
                                            </div>
                                        <?php 
                                                    endif;
                                                    $i++;
                                                endforeach;
                                            endif;
                                        ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-primary text-white d-flex justify-content-between align-items-center rounded-0 py-1">
                                    <div>
                                        <i class="bi bi-eye"></i> <?= esc($sumView->pageviews ?? 0); ?> <?= trans("count_view"); ?>
                                    </div>
                                    <a href="<?= generateCategoryURL($item); ?>" class="text-white text-decoration-none"><?= trans("load_more"); ?> <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
    </div>

</div>

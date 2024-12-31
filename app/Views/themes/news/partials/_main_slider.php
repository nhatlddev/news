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
                            <h3><?= esc($item->title); ?></h3>
                            <p><?= esc($item->content); ?></p>
                        </div>
                    </div>

                    <?php  
                                $i++;   
                                endforeach;
                        endif; 
                    ?> 
                        <!-- <div>
                        <div class="slider-img">
                            <img src="https://webtechball.files.wordpress.com/2017/09/slider-img-3.jpg" alt="Slide 2" />
                        </div>
                        <div class="slider-text">
                            <h3>Slide Two</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                        </div>
                        </div>
                        <div>
                        <div class="slider-img">
                            <img src="https://webtechball.files.wordpress.com/2017/09/slider-img-2.jpg" alt="Slide 3" />
                        </div>
                        <div class="slider-text">
                            <h3>Slide Three</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                        </div> -->
                    <!-- </div> -->
                </div>
            </div>

            <!-- <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <<?php 
                        $i = 0;
                        if(!empty($banners)): 
                            foreach($banners as $item):
                                $imgBaseURL = getBaseURLByStorage($item->storage); 
                        ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : ''; ?>">
                                <img src="<?= esc($item->file_path); ?>" class="d-block w-100" alt="News 1">
                                <div class="carousel-caption d-none d-lg-block">
                                    <span class="badge bg-primary fs-1 mb-1"><?= esc($item->title); ?></span>
                                    <h4><?= esc($item->content); ?></h4>
                                </div>
                            </div>
                    <?php  
                                $i++;   
                                endforeach;
                        endif; 
                    ?> 
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div> -->
        
            

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
                    <span>Xem thêm</span>
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
                        <span class="st-title cursor-pointer" routerLink="/post/list">
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
                                            $postItems = getPostByCategoryId($item->id);
                                            $sumView = getPageViewsSumByCategory($item->id);

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
                                        <i class="bi bi-eye"></i> <?= esc($sumView->pageviews ?? 0); ?> Lượt xem
                                    </div>
                                    <a href="<?= generateCategoryURL($item); ?>" class="text-white text-decoration-none">Xem thêm <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
    </div>

</div>

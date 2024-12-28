<div class="container my-4">
    <div class="row gx-2">
        <div class="col-lg-8">
            <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <<?php 
                        $i = 0;
                        if(!empty($banners)): 
                            foreach($banners as $item):
                                $imgBaseURL = getBaseURLByStorage($item->storage); 
                        ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : ''; ?>">
                                <img src="<?= esc($item->file_path); ?>" class="d-block w-100" alt="News 1">
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
            </div>
        </div>

        <div class="col-lg-4 bg-white">
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
                <div class="col-md-6 mb-3">
                    <div class="section-title-holder clearfix pattern-light">
                        <span class="st-title cursor-pointer" routerLink="/post/list">
                        <?= esc($item->name); ?>
                        </span>
                    </div>

                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card rounded-0">
                                <img src="<?= esc($item->file_path); ?>" class="card-img-top rounded-0" alt="Post Image">
                                <div class="card-body pb-0">
                                <?php
                                    $postItems = getPostByCategoryId($item->id);
                                    if (!empty($postItems)):
                                        $mainPostItems= $postItems[0];
                                ?>
                                    <h5 class="card-title cursor-pointer hover-primary text-capitalize">
                                        <a class="text-decoration-none text-dark cursor-pointer hover-primary text-capitalize" 
                                            href="<?= generatePostURL($mainPostItems); ?>" <?php postURLNewTab($mainPostItems); ?>>
                                            <?= esc(characterLimiter($mainPostItems->title, 80, '...')); ?>
                                        </a>
                                    </h5>
                                    <p class="text-muted font-size-8">
                                        <i class="bi bi-calendar text-primary"></i> <?= formatDateFront($mainPostItems->created_at); ?>
                                    </p>

                                    <?php 
                                    endif;
                                    ?>
                                </div>
                                <div
                                    class="card-footer bg-primary text-white d-flex justify-content-between align-items-center rounded-0 py-1 font-size-8">
                                    <div>
                                        <i class="bi bi-eye"></i> 16313 Views
                                    </div>
                                    <a href="#" class="text-white text-decoration-none">Read More <i
                                            class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>

                            <div class=" rounded-0">
                                <ul class="list-group rounded-0">
                                <?php
                                    $i = 0;
                                    if (!empty($postItems)):
                                        foreach ($postItems as $item):
                                            if ($i >= 1 && $i <= 5):
                                ?>
                                            <li class="list-group-item d-flex">
                                                <div class="">
                                                    <a href="<?= generatePostURL($item); ?>" <?php postURLNewTab($item); ?>
                                                        class="text-dark text-decoration-none hover-svg">
                                                        <?= esc(characterLimiter($item->title, 80, '...')); ?>
                                                    </a>
                                                    <p class="mb-0 text-muted font-size-8">
                                                        <i class="bi bi-calendar text-primary"></i>
                                                        <?= formatDateFront($item->created_at); ?>
                                                    </p>
                                                </div>
                                            </li>
                                <?php      
                                            endif;
                                        $i++;
                                        endforeach;
                                    endif;
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
    </div>

</div>

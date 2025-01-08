<?= view('common/_json_ld'); ?>
<footer class="pt-5">
    <div class="container">
        <div class="row">
            <!-- Logo and Address -->
            <div class="col-md-3 mb-4">
                <h5 class="text-primary-first">UNI-PRESIDENT VIỆT NAM</h5>
                <!-- <img src="./assets/img/uni-logo.jpg" alt="Office Image" class="img-fluid mb-3"> -->
                <!-- <p><strong>Địa chỉ:</strong></p> -->
                <p>
                    Địa chỉ: Số 16-18-20, Đường ĐT 743, KCN Sóng Thần II, P. Dĩ An, TP. Dĩ An,
                    Tỉnh Bình Dương, Việt Nam.<br>
                    Email: services&#64;upvn.com.vn
                    <br>
                    Điện thoại: (84-274)-3.790811~6
                </p>
            </div>

            <!-- Recent Posts -->
            <div class="col-md-4 mb-4">
                <h5 class="text-primary-first"><?= trans("most_viewed_posts"); ?></h5>
                <ul class="list-unstyled">
                    <?php $mostViewedPosts = getMostViewedPosts(5); 
                        if (!empty($mostViewedPosts)):
                            foreach ($mostViewedPosts as $item): ?>
                            <li class="mb-2">
                                <p class="mb-0">
                                    <a class="hover-primary" href="<?= generatePostURL($item); ?>" <?php postURLNewTab($item); ?>>
                                        <?= esc(characterLimiter($item->title, 45, '...')); ?>
                                    </a>
                                </p>
                                <!-- <small class="text-muted"><?= formatDateFront($item->created_at); ?></small> -->
                            </li>
                    <?php endforeach;
                        endif; ?>
                </ul>
            </div>

            <!-- Recent Comments -->
            <div class="col-md-3 mb-4">
                <h5 class="text-primary-first">Chuyên mục</h5>
                <ul class="list-unstyled">
                <?php $categoryFooter = getCategoryFooter(5); ?>
                <?php 
                    if (!empty($categoryFooter)):
                        foreach ($categoryFooter as $item): ?>
                    <li class="mb-2">
                        <a class="hover-primary" href="<?= generateCategoryURL($item); ?>">
                            <?= esc(characterLimiter($item->name, 80, '...')); ?>
                        </a>
                    </li>
                <?php endforeach;
                    endif; ?>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="col-md-2 mb-4">
                <h5 class="text-primary-first"><?= trans("footer_contact"); ?></h5>
                <!-- <p>Get in touch with us right now.</p> -->
                <div class="d-flex flex-column cursor-pointer">
                    <span class="hover-primary">Anh Khoa - 2303</span>
                    <span class="hover-primary">Tường Ly - 2301</span>
                    <span class="hover-primary">Đức Nhật - 2302</span>
                </div>
                <!-- Social Icons -->
                <!-- <div class="social-icons mt-3">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div> -->
            </div>
        </div>
        <hr class="bg-light">
        <div class="row">
            <div class="col-md-6">
                <p>© Uni President, All Rights Reserved</p>
            </div>
            <div class="col-md-6 text-end">
            <?php 
                if (!empty($baseMenuLinks)):
                    $filteredLinks = array_filter($baseMenuLinks, function ($item) {
                        return $item->item_visibility == 1 && $item->item_location == "footer";
                    });
                    $total = count($filteredLinks); 
                    $count = 0; 
                    foreach ($filteredLinks as $item):
                        $count++; 
                ?>
                        <a href="#"><?= esc($item->item_name); ?></a>
                        <?php if ($count < $total): ?> | <?php endif; ?>
                    <?php 
                    endforeach;
                endif; 
                ?>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="btn btn-secondary rounded-circle">
    <i class="bi bi-chevron-double-up"></i>
</button>

    <!-- <a href="#" class="scrollup"><i class="icon-arrow-up"></i></a> -->
<?php if (empty(helperGetCookie('cks_warning')) && $baseSettings->cookies_warning): ?>
    <div class="cookies-warning">
        <button type="button" aria-label="close" class="close" onclick="closeCookiesWarning();">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
            </svg>
        </button>
        <div class="text">
            <?= $baseSettings->cookies_warning_text; ?>
        </div>
        <button type="button" class="btn btn-md btn-block btn-custom" aria-label="close" onclick="closeCookiesWarning();"><?= trans("accept_cookies"); ?></button>
    </div>
<?php endif; ?>
    <script src="<?= base_url($assetsPath . '/js/jquery-3.6.1.min.js'); ?> "></script>
    <script src="<?= base_url('assets/vendor/bootstrap-5.3.3/js/bootstrap.bundle.min.js'); ?> "></script>
    <script src="<?= base_url($assetsPath . '/js/plugins.js'); ?> "></script>
    <script src="<?= base_url($assetsPath . '/js/main-2.2.min.js'); ?> "></script>
    <script src="<?= base_url('assets/vendor/js/common.js'); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.2.4/owl.carousel.js"></script> -->
<?= loadView('partials/_js_footer'); ?>
    <script>$("form[method='post']").append("<input type='hidden' name='sys_lang_id' value='<?= $activeLang->id; ?>'>");</script>
<?php if ($generalSettings->pwa_status == 1): ?>
    <script>if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('<?= base_url('pwa-sw.js');?>').then(function (registration) {
                }, function (err) {
                    console.log('ServiceWorker registration failed: ', err);
                }).catch(function (err) {
                    console.log(err);
                });
            });
        } else {
            console.log('service worker is not supported');
        }</script>
<?php endif; ?>
<?= $generalSettings->adsense_activation_code; ?>
<?= $generalSettings->google_analytics; ?>

    </body>
    </html>
<?php if (!empty($isPage404)): exit(); endif; ?>
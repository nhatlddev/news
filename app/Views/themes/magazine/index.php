<?php if ($generalSettings->show_newsticker == 1 && countItems($breakingNews) > 0): ?>
    <section class="section section-newsticker bg-white">
        <div class="container-xl">
            <div class="row py-2">
                <div class="d-flex newsticker-container">
                    <div class="newsticker-title bg-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lightning-fill" viewBox="0 0 16 16">
                            <path d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641l2.5-8.5z"/>
                        </svg>
                        <span><?= trans("breaking_news"); ?></span>
                    </div>
                    <ul class="newsticker">
                        <?php foreach ($breakingNews as $item): ?>
                            <li>
                                <a 
                                    href="<?= generatePostURL($item); ?>"<?php postURLNewTab($item); ?>
                                    class="text-decoration-none text-dark fw-bold">
                                    <?= esc($item->title); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="ms-auto">
                        <div id="nav_newsticker" class="nav-sm-buttons">
                            <button class="prev border-0" aria-label="prev"><i class="bi bi-arrow-left-short"></i></button>
                            <button class="next border-0" aria-label="next"><i class="bi bi-arrow-right-short"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif;
if ($generalSettings->show_featured_section == 1):
    if ($activeTheme->theme == 'news'):
        echo view('themes/news/partials/_main_slider');
    else:
        echo loadView('partials/_main_slider');
    endif;
endif; ?>

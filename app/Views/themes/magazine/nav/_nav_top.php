<?php $menuLimit = $generalSettings->menu_limit;

use CodeIgniter\CLI\Console;
$itemsMegaMenu = array(); ?>

<div class="container d-flex justify-content-between align-items-center py-3 border-bottom">
    <a href="<?= langBaseUrl(); ?>" class="d-block">
        <img src="<?= $darkMode == 1 ? getLogoFooter() : getLogo(); ?>" alt="UniPresident Logo"
            style="max-height: 90px;">
    </a>
    <div class="advertise-banner d-none d-md-block">
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark d-none d-lg-flex sticky-top">
    <div class="container d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#desktopNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="desktopNavbar">
                <ul class="navbar-nav">
                    <?php if ($generalSettings->show_home_link == 1): ?>
                        <li class="nav-item text-center">
                            <a class="nav-link cursor-pointer fs-5 <?= uri_string() == '' ? 'active' : ''; ?>"
                                href="<?= langBaseUrl(); ?>"><?= trans("home"); ?> </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $totalItem = 0;
                    $i = 1;

                    // Kiểm tra nếu có menu links
                    if (!empty($baseMenuLinks)):
                        foreach ($baseMenuLinks as $item):
                            if (
                                $item->item_visibility == 1 &&
                                $item->item_location == "main" &&
                                $item->item_parent_id == '0'
                            ):
                                if ($i < $menuLimit):
                                    $subLinks = getSubMenuLinks($baseMenuLinks, $item->item_id, $item->item_type);
                                    
                                    if ($item->item_type == "category"):
                                        $category = getCategory($item->item_id, $baseCategories);
                                        $subCategories = getSubcategories($category->id, $baseCategories);

                                        if (!empty($category)):
                                            $isActiveCategory = false;
                                            if (!empty($subCategories)): 
                                                foreach ($subCategories as $subMenu) {
                                                    if (current_url() == generateCategoryURL($subMenu)) {
                                                        $isActiveCategory = true;
                                                        break;
                                                    }
                                                }
                                            ?>
                                                <li class="nav-item text-center dropdown nav-item-category-<?= $category->id; ?>"
                                                    data-category-id="<?= $category->id; ?>">
                                                    <a class="nav-link dropdown-toggle nav-item-category-<?= $category->id; ?> <?= $isActiveCategory ? 'active' : ''; ?> fs-5"
                                                        href="<?= generateMenuItemURL($item, $baseCategories); ?>" 
                                                        id="drp1-<?= $category->id; ?>"
                                                        role="button" aria-expanded="false">
                                                        <?= esc($category->name); ?>
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="drp1-<?= $category->id; ?>">
                                                        <?php foreach ($subCategories as $subMenu): ?>
                                                            <li>
                                                                <a class="dropdown-item fs-5"
                                                                href="<?= !empty($subMenu->title_slug) ? generatePostURL($subMenu) : generateCategoryURL($subMenu); ?>">
                                                                    <?= esc($subMenu->name); ?>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </li>

                                            <?php
                                            else:
                                            ?>
                                                <li class="nav-item text-center">
                                                    <a class="nav-link cursor-pointer <?= uri_string() == $item->item_slug ? 'active' : ''; ?> fs-5"
                                                        href="<?= generateMenuItemURL($item, $baseCategories); ?>"><?= esc($category->name); ?></a>
                                                </li>
                                                <?php

                                            endif;
                                        endif;

                                        // Nếu không phải "category"
                                    else:
                                        if (!empty($subLinks)): ?>
                                            <li class="nav-item text-center dropdown nav-item-category-<?= $item->item_id; ?>"
                                                data-category-id="<?= $item->item_id; ?>">
                                                <a class="nav-link dropdown-toggle" href="<?= generateMenuItemURL($item, $baseCategories); ?> fs-5"
                                                    id="drp2-<?= $item->item_id; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <?= esc($item->item_name); ?>
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="drp2-<?= $item->item_id; ?>">
                                                    <?php foreach ($subLinks as $subItem):
                                                        if ($subItem->item_visibility == 1): ?>
                                                            <li>
                                                                <a href="<?= generateMenuItemURL($subItem, $baseCategories); ?>" class="dropdown-item">
                                                                    <?= esc($subItem->item_name); ?>
                                                                </a>
                                                            </li>
                                                        <?php endif;
                                                    endforeach; ?>
                                                </ul>
                                            </li>
                                        <?php else: ?>
                                            <li class="nav-item">
                                                <a href="<?= generateMenuItemURL($item, $baseCategories); ?> fs-5"
                                                    class="nav-link <?= uri_string() == $item->item_slug ? 'active' : ''; ?>">
                                                    <?= esc($item->item_name); ?>
                                                </a>
                                            </li>
                                        <?php endif;
                                    endif;
                                    $i++;
                                endif;

                                $totalItem++;
                            endif;
                        endforeach;
                    endif;

                    if ($totalItem >= $menuLimit): ?>
                        <li class="nav-item nav-item-more text-center dropdown">
                            <a class="nav-link dropdown-toggle fs-5" href="#" id="drp-more" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <?= trans("more"); ?>
                            </a>
                            <ul class="dropdown-more dropdown-menu" aria-labelledby="drp-more">
                                <?php
                                $i = 1;
                                if (!empty($baseMenuLinks)):
                                    foreach ($baseMenuLinks as $item):
                                        if ($item->item_visibility == 1 && $item->item_location == "main" && $item->item_parent_id == "0"):
                                            if ($i >= $menuLimit):
                                                $subLinks = getSubMenuLinks($baseMenuLinks, $item->item_id, $item->item_type);
                                                if (!empty($subLinks)): ?>
                                                    <li class="dropend nav-dropend">
                                                        <a class="dropdown-item dropdown-toggle fs-5" <?= generateMenuItemURL($item, $baseCategories); ?>
                                                            id="dropend-<?= $item->item_id; ?>" role="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <?= esc($item->item_name); ?>
                                                        </a>
                                                        <ul class="dropdown-menu" aria-labelledby="dropend-<?= $item->item_id; ?>">
                                                            <?php foreach ($subLinks as $subItem):
                                                                if ($subItem->item_visibility == 1): ?>
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="<?= generateMenuItemURL($subItem, $baseCategories); ?>">
                                                                            <?= esc($subItem->item_name); ?>
                                                                        </a>
                                                                    </li>
                                                                <?php endif;
                                                            endforeach; ?>
                                                        </ul>
                                                    </li>
                                                <?php else: ?>
                                                    <li>
                                                        <a href="<?= generateMenuItemURL($item, $baseCategories); ?>" class="dropdown-item fs-5">
                                                            <?= esc($item->item_name); ?>
                                                        </a>
                                                    </li>
                                                <?php endif;
                                            endif;
                                            $i++;
                                        endif;
                                    endforeach;
                                endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <?php if ($generalSettings->multilingual_system == 1 && countItems($activeLanguages) > 1): ?>
            <div class="nav-item dropdown ms-auto">
                <a class="dropdown-toggle text-light text-decoration-none d-flex align-items-center fs-5" href="#" role="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="d-flex align-items-center gap-2">
                    <img
                        src="<?= base_url('assets/img/flag_placeholder.png'); ?>"
                        class="flag flag-<?= strtolower($activeLang->short_form); ?>"
                        style="width: 25px;" />
                        <div class="font-medium"><?= esc($activeLang->name); ?></div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                    <?php foreach ($activeLanguages as $language):
                        $langURL = base_url($language->short_form . "/");
                        if ($language->id == $generalSettings->site_lang):
                            $langURL = base_url();
                        endif; ?>
                        <li>
                            <a class="dropdown-item" href="<?= $langURL; ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <img
                                        src="<?= base_url('assets/img/flag_placeholder.png'); ?>"
                                        class="flag flag-<?= strtolower($language->short_form); ?>"
                                        style="width: 18px;" />
                                    <div class="font-medium"><?= esc($language->name); ?></div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        <?php endif; ?>
    </div>
</nav>

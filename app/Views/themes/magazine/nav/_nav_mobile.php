<?php $menuLimit = $generalSettings->menu_limit;

use CodeIgniter\CLI\Console;
$itemsMegaMenu = array(); ?>

<nav class="navbar navbar-dark bg-dark d-lg-none">
    <div class="container">
        <?php if ($generalSettings->show_home_link == 1): ?>
            <a class="navbar-brand" href="<?= langBaseUrl(); ?>"><?= trans("home"); ?> </a>
        <?php endif; ?>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mobileNavbar">
            <ul class="navbar-nav">
                <?php
                    $totalItem = 0;
                    $i = 1;
                    if (!empty($baseMenuLinks)):
                        foreach ($baseMenuLinks as $item):
                            if ($item->item_visibility == 1 && $item->item_location == "main" && $item->item_parent_id == '0'):
                                if ($i < $menuLimit):
                                    $subLinks = getSubMenuLinks($baseMenuLinks, $item->item_id, $item->item_type);
                                    if ($item->item_type == "category"):
                                        $category = getCategory($item->item_id, $baseCategories);
                                        $subCategories = getSubcategories($category->id, $baseCategories);
                                        if (!empty($category)):
                                            if (!empty($subCategories)): ?>
                                                <li class="nav-item dropdown">
                                                    <a class="nav-link dropdown-toggle" 
                                                       href="<?= generateMenuItemURL($item, $baseCategories); ?>"
                                                       id="mobileHomeDropdown-<?= $category->id; ?>" role="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <?= esc($category->name); ?>
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="mobileHomeDropdown-<?= $category->id; ?>">
                                                        <?php foreach ($subCategories as $subMenu): ?>
                                                            <li>
                                                                <a class="dropdown-item" href="<?= generateCategoryURL($subMenu); ?>">
                                                                    <?= esc($subMenu->name); ?>
                                                                </a>
                                                            </li>
                                                        <?php endforeach ?>
                                                    </ul>
                                                </li>
                                            <?php
                                            else:
                                            ?>
                                                <li class="nav-item">
                                                    <a class="nav-link cursor-pointer <?= uri_string() == $item->item_slug ? 'active' : ''; ?>"
                                                    href="<?= generateMenuItemURL($item, $baseCategories); ?>"><?= esc($category->name); ?></a>
                                                </li>
                                            <?php
                                            endif;
                                        endif;
                                    else:
                                        if (!empty($subLinks)): ?>
                                            <li class="nav-item dropdown">
                                                <a class="nav-link dropdown-toggle" href="<?= generateMenuItemURL($item, $baseCategories); ?>"
                                                    id="mobileHomeDropdown2-<?= $item->item_id; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <?= esc($item->item_name); ?>
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="mobileHomeDropdown2-<?= $item->item_id; ?>">
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
                                                <a href="<?= generateMenuItemURL($item, $baseCategories); ?>"
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="drp-more" role="button" data-bs-toggle="dropdown"
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
                                                    <a class="dropdown-item dropdown-toggle" <?= generateMenuItemURL($item, $baseCategories); ?>
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
                                                    <a href="<?= generateMenuItemURL($item, $baseCategories); ?>" class="dropdown-item">
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
                <?php if ($generalSettings->multilingual_system == 1 && countItems($activeLanguages) > 1): ?>
                    <li class="nav-item">
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle text-light text-decoration-none" href="#" role="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="me-1"><?= esc($activeLang->name); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                <?php foreach ($activeLanguages as $language):
                                    $langURL = base_url($language->short_form . "/");
                                    if ($language->id == $generalSettings->site_lang):
                                        $langURL = base_url();
                                    endif; ?>
                                    <li><a class="dropdown-item" href="<?= $langURL; ?>"><?= esc($language->name); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
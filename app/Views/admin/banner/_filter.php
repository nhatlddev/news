<?php $model = new \App\Models\BannerModel(); ?>
<div class="row table-filter-container">
    <div class="col-sm-12">
        <form action="<?= adminUrl('banners'); ?>" method="get">
            <div class="item-table-filter" style="width: 80px; min-width: 80px;">
                <label><?= trans("show"); ?></label>
                <select name="show" class="form-control">
                    <option value="15" <?= inputGet('show') == '15' ? 'selected' : ''; ?>>15</option>
                    <option value="30" <?= inputGet('show') == '30' ? 'selected' : ''; ?>>30</option>
                    <option value="60" <?= inputGet('show') == '60' ? 'selected' : ''; ?>>60</option>
                    <option value="100" <?= inputGet('show') == '100' ? 'selected' : ''; ?>>100</option>
                </select>
            </div>

            <div class="item-table-filter">
                <label><?= trans("language"); ?></label>
                <select name="lang_id" class="form-control" onchange="getAlbumsByLang(this.value);">
                    <option value=""><?= trans("all"); ?></option>
                    <?php foreach ($activeLanguages as $language): ?>
                        <option value="<?= $language->id; ?>" <?= inputGet('lang_id', true) == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="item-table-filter item-table-filter-long">
                <label><?= trans("search"); ?></label>
                <input name="q" class="form-control" placeholder="<?= trans("search") ?>" type="search" value="<?= esc(inputGet('q', true)); ?>">
            </div>

            <div class="item-table-filter md-top-10" style="width: 65px; min-width: 65px;">
                <label style="display: block">&nbsp;</label>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i>
                    <?= trans("filter"); ?>
                </button>
            </div>
        </form>
    </div>
</div>
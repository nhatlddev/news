<div class="row">
    <div class="col-sm-12">
        <?= view('admin/includes/_messages'); ?>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('administrators'); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped dataTable" id="cs_datatable" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th class="text-nowrap text-center"><?= trans('id'); ?></th>
                            <th class="text-nowrap text-center"><?= trans('user'); ?></th>
                            <th class="text-nowrap text-center"><?= trans('email'); ?></th>
                            <th class="text-nowrap text-center"><?= trans('date'); ?></th>
                            <th class="text-nowrap text-center"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($users)):
                            foreach ($users as $user): ?>
                                <tr>
                                    <td width="20" class="text-nowrap text-center"><?= $user->id; ?></td>
                                    <td class="td-user text-start text-nowrap">
                                        <a href="<?= generateProfileURL($user->slug); ?>" target="_blank" class="table-link">
                                            <!-- <img src="<?= getUserAvatar($user->avatar); ?>" alt="" class="img-responsive"> -->
                                            <strong><?= esc($user->username); ?></strong>
                                        </a>
                                        <!-- <?php if ($user->reward_system_enabled == 1): ?>
                                            <p><label class="label bg-primary"><?= trans('reward_system'); ?></label></p>
                                        <?php endif; ?> -->
                                    </td>
                                    <td class="text-nowrap text-center"><?= esc($user->email); ?></td>
                                    <td class="text-nowrap text-center"><?= formatDate($user->created_at); ?></td>
                                    <td class="td-select-option ">
                                        <form action="<?= base_url('AdminController/userOptionsPost'); ?>" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= $user->id; ?>">
                                            <input type="hidden" name="back_url" value="administrators">
                                            <div class="dropdown">
                                                <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_an_option'); ?>
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu options-dropdown">
                                                    <?php if ($user->reward_system_enabled == 1): ?>
                                                        <!-- <li>
                                                            <button type="submit" name="submit" value="reward_system" class="btn-list-button"><i class="fa fa-money option-icon"></i><?= trans('disable_reward_system'); ?></button>
                                                        </li> -->
                                                    <?php else: ?>
                                                        <!-- <li>
                                                            <button type="submit" name="submit" value="reward_system" class="btn-list-button"><i class="fa fa-money option-icon"></i><?= trans('enable_reward_system'); ?></button>
                                                        </li> -->
                                                    <?php endif; ?>
                                                    <li>
                                                        <a href="<?= adminUrl('edit-user/' . $user->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="deleteItem('AdminController/deleteUserPost','<?= $user->id; ?>','<?= clrQuotes(trans("confirm_user")); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
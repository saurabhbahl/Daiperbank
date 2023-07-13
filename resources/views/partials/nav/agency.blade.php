<div class="nav-item mlauto">
    <a href="<?= route('order.index') ?>">
        <i class="fa fa-truck"></i>
        Orders
    </a>
</div>
<div class="nav-item">
    <a href="<?= route('family.index') ?>">
        <i class="fa fa-child"></i>
        Children
    </a>
</div>
<div class="nav-item">
    <a href="<?= route('menstruator.index'); ?>">
         <i class="fa fa-female"></i>
         Menstruators
     </a>
</div>
<div class="nav-item dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
        <i class="fa fa-cloud-download"></i>
        Resources
        <span class="dropdown-handle">
            <i class="fa fa-caret-down"></i>
        </span>
    </a>

    <ul class="dropdown-menu nav-links" role="menu">
        <li>
            <a href="<?= route('partner-handbook.index') ?>">
                <i class="fa fa-file"></i>
                Partner Handbook
            </a>
        </li>
        <li>
            <a href="<?= route('agreement.index') ?>">
                <i class="fa fa-thumbs-up"></i>
                Partner Agreement
            </a>
        </li>
    </ul>
</div>

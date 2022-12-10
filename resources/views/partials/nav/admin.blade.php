<div class="nav-item mlauto">
    <a href="<?= route('admin.order.index') ?>">
        <i class="fa fa-truck"></i>
        Orders
    </a>
</div>
<div class="nav-item">
    <a href="<?= route('admin.fulfillment.index') ?>">
        <i class="fa fa-dropbox"></i>
        Fulfillment
    </a>
</div>
<div class="nav-item">
    <a href="<?= route('admin.agency.index') ?>">
        <i class="fa fa-users"></i>
        Agencies
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
            <a href="<?= route('admin.resource.create') ?>">
                <i class="fa fa-file"></i>
                 Partner Handbook
            </a>
        </li>
        <li>
            <a href="<?= route('admin.agreement.create') ?>">
                <i class="fa fa-thumbs-up"></i>
                Partner Agreement
            </a>
        </li>
    </ul>
</div>
<div class="nav-item dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
        <i class="fa fa-bars"></i>
        More
        <span class="dropdown-handle">
            <i class="fa fa-caret-down"></i>
        </span>
    </a>

    <ul class="dropdown-menu nav-links" role="menu">
        <li>
            <a href="<?= route('admin.pickup.index') ?>">
                <i class="fa fa-calendar"></i>
                Pick-up Schedule
            </a>
        </li>
        <li>
            <a href="<?= route('admin.inventory.index') ?>">
                <i class="fa fa-bar-chart"></i>
                Inventory
            </a>
        </li>

        <li>
            <a href="<?= route('admin.reporting') ?>">
                <i class="fa fa-line-chart"></i>
                Reporting
            </a>
        </li>
    </ul>
</div>
